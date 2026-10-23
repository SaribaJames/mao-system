<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\Program;
use App\Models\ProgramEndorsement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EndorsementController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->isBarangayUser(), 403);

        $barangayId = Auth::user()->barangayAccount?->barangay_id;
        $farmers = Farmer::with('activePrograms')->where('barangay_id', $barangayId)->orderBy('surname')->get();
        $programs = Program::orderBy('name')->get();

        $endorsements = ProgramEndorsement::where('endorsed_by', Auth::id())
            ->with(['farmer.barangay', 'program'])
            ->latest()
            ->paginate(15);

        return view('endorsements.index', compact('farmers', 'programs', 'endorsements'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->isBarangayUser(), 403);

        // The justification is the whole point of an endorsement — a coordinator
        // can't judge a farmer they've never met without one. 30 characters is
        // enough to stop "ok" / "pls approve" while staying quick to write.
        $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'program_id' => 'required|exists:programs,id',
            'notes' => 'required|string|min:30|max:1000',
        ], [
            'notes.required' => 'Please explain why this farmer qualifies for the program.',
            'notes.min' => 'Please give a fuller reason — at least 30 characters — so the coordinator can properly assess the farmer.',
        ], [
            'notes' => 'reason for endorsement',
        ]);

        // Block a duplicate pending endorsement of the same farmer for the same
        // program — otherwise the coordinator sees the same request twice and
        // approving both enrolls the farmer twice.
        $alreadyPending = ProgramEndorsement::where('farmer_id', $request->farmer_id)
            ->where('program_id', $request->program_id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return back()->withInput()
                ->with('error', 'This farmer already has a pending endorsement for that program.');
        }

        ProgramEndorsement::create([
            'farmer_id' => $request->farmer_id,
            'program_id' => $request->program_id,
            'endorsed_by' => Auth::id(),
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('endorsements.index')
            ->with('success', 'Farmer endorsed successfully! The program coordinator will review it.');
    }

    public function approve(ProgramEndorsement $endorsement)
    {
        $program = $endorsement->program;
        abort_unless(
            Auth::user()->isAdmin() || $program->isManagedBy(Auth::user()),
            403,
            'Only the assigned personnel can review endorsements for this program.'
        );
        abort_unless(session()->get("unlocked_programs.{$program->id}", false), 403, 'Enter your PIN to unlock this program before managing it.');

        // Already reviewed — most often a double-submit or the back button.
        // Without this, approving twice creates two enrolment rows.
        if ($endorsement->status !== 'pending') {
            return redirect()->route('programs.show', $program)
                ->with('error', "This endorsement has already been {$endorsement->status}.");
        }

        // The farmer may already be in the program (enrolled directly, or via
        // an earlier endorsement). Approve the endorsement, but don't enrol
        // them a second time — duplicates inflate the counts and list the
        // farmer twice on every page that reads enrolments.
        $alreadyEnrolled = $program->enrollments()
            ->where('farmer_id', $endorsement->farmer_id)
            ->where('status', 'active')
            ->exists();

        if (!$alreadyEnrolled) {
            $program->enrollments()->create([
                'farmer_id' => $endorsement->farmer_id,
                'status' => 'active',
                'enrollment_date' => now(),
                'remarks' => 'Enrolled via barangay rep endorsement',
                'processed_by' => Auth::id(),
            ]);
        }

        $endorsement->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('programs.show', $program)->with(
            'success',
            $alreadyEnrolled
                ? 'Endorsement approved — the farmer was already enrolled in this program.'
                : 'Endorsement approved and farmer enrolled!'
        );
    }

    /**
     * Let a barangay rep clear out their own endorsement — a rejected one they
     * no longer need to see, or a pending one they want to withdraw before the
     * coordinator reviews it. An APPROVED endorsement can't be deleted: the
     * farmer is already enrolled, and the record is the reason why.
     */
    public function destroy(ProgramEndorsement $endorsement)
    {
        abort_unless(Auth::user()->isBarangayUser(), 403);

        // Own submissions only.
        abort_unless($endorsement->endorsed_by === Auth::id(), 403, 'You can only remove endorsements you submitted.');

        if ($endorsement->status === 'approved') {
            return back()->with('error', 'Approved endorsements cannot be removed — the farmer is already enrolled in the program.');
        }

        $farmerName = $endorsement->farmer?->first_name . ' ' . $endorsement->farmer?->surname;
        $wasPending = $endorsement->status === 'pending';
        $endorsement->delete();

        return redirect()->route('endorsements.index')->with(
            'success',
            $wasPending
                ? "Endorsement for {$farmerName} withdrawn."
                : "Rejected endorsement for {$farmerName} removed."
        );
    }

    public function reject(Request $request, ProgramEndorsement $endorsement)
    {
        $program = $endorsement->program;
        abort_unless(
            Auth::user()->isAdmin() || $program->isManagedBy(Auth::user()),
            403,
            'Only the assigned personnel can review endorsements for this program.'
        );
        abort_unless(session()->get("unlocked_programs.{$program->id}", false), 403, 'Enter your PIN to unlock this program before managing it.');

        // A rejection without a reason tells the barangay rep nothing and just
        // invites the same endorsement again.
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ], [
            'rejection_reason.required' => 'Please state why this endorsement is being rejected.',
            'rejection_reason.min' => 'Please give a clearer reason so the barangay representative understands what was lacking.',
        ]);

        $endorsement->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('programs.show', $program)
            ->with('success', 'Endorsement rejected. The barangay representative will see your reason.');
    }
}