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
        $farmers = Farmer::where('barangay_id', $barangayId)->orderBy('surname')->get();
        $programs = Program::orderBy('name')->get();

        $endorsements = ProgramEndorsement::where('endorsed_by', Auth::id())
            ->with(['farmer', 'program'])
            ->latest()
            ->paginate(15);

        return view('endorsements.index', compact('farmers', 'programs', 'endorsements'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->isBarangayUser(), 403);

        $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'program_id' => 'required|exists:programs,id',
            'notes' => 'nullable|string',
        ]);

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

        $program->enrollments()->create([
            'farmer_id' => $endorsement->farmer_id,
            'status' => 'active',
            'enrollment_date' => now(),
            'remarks' => 'Enrolled via barangay rep endorsement',
            'processed_by' => Auth::id(),
        ]);

        $endorsement->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('programs.show', $program)
            ->with('success', 'Endorsement approved and farmer enrolled!');
    }

    public function reject(ProgramEndorsement $endorsement)
    {
        $program = $endorsement->program;
        abort_unless(
            Auth::user()->isAdmin() || $program->isManagedBy(Auth::user()),
            403,
            'Only the assigned personnel can review endorsements for this program.'
        );
        abort_unless(session()->get("unlocked_programs.{$program->id}", false), 403, 'Enter your PIN to unlock this program before managing it.');

        $endorsement->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('programs.show', $program)
            ->with('success', 'Endorsement rejected.');
    }
}