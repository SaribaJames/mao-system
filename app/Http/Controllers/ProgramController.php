<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\Farmer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::withCount([
            'enrollments as active_enrollments_count' => function ($q) {
                $q->where('status', 'active');
            }
        ])->with('assignedUser')->orderBy('name')->get();

        $totalPrograms = $programs->count();
        $totalActiveEnrollments = ProgramEnrollment::where('status', 'active')->count();

        return view('programs.index', compact('programs', 'totalPrograms', 'totalActiveEnrollments'));
    }

    public function show(Program $program)
    {
        // Only admins and this program's assigned personnel may view it.
        // Any other staff member gets blocked entirely, not just "manage".
        abort_unless(
            Auth::user()->isAdmin() || $program->isManagedBy(Auth::user()),
            403,
            'You are not assigned to this program.'
        );

        $query = $program->enrollments()->with(['farmer', 'farmer.barangay', 'processedBy']);

        if (request('search')) {
            $search = request('search');
            $query->whereHas('farmer', function ($fq) use ($search) {
                $fq->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('surname', 'like', '%' . $search . '%');
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $enrollments = $query->latest()->paginate(15);
        $farmers = Farmer::orderBy('surname')->get();
        $staffUsers = User::whereHas('role', fn($q) => $q->where('name', 'staff'))->orderBy('name')->get();

        $isAssignedUser = $program->isManagedBy(Auth::user());
        $isUnlocked = $isAssignedUser && session()->get("unlocked_programs.{$program->id}", false);

        return view('programs.show', compact(
            'program', 'enrollments', 'farmers', 'staffUsers', 'isAssignedUser', 'isUnlocked'
        ));
    }

    public function unlock(Request $request, Program $program)
    {
        abort_unless($program->isManagedBy(Auth::user()), 403, 'You are not the assigned personnel for this program.');

        $request->validate([
            'pin' => 'required|digits_between:4,6',
        ]);

        if (! Auth::user()->verifyPin($request->pin)) {
            return back()->with('error', 'Incorrect PIN.');
        }

        $request->session()->put("unlocked_programs.{$program->id}", true);

        return redirect()->route('programs.show', $program)
            ->with('success', 'Program unlocked for this session.');
    }

    public function enroll(Request $request, Program $program)
    {
        $this->authorizeManage($program);

        $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'remarks' => 'nullable|string',
        ]);

        $program->enrollments()->create([
            'farmer_id' => $request->farmer_id,
            'status' => 'active',
            'enrollment_date' => now(),
            'remarks' => $request->input('remarks'),
            'processed_by' => Auth::id(),
        ]);

        return redirect()->route('programs.show', $program)
            ->with('success', 'Farmer enrolled successfully!');
    }

    public function updateEnrollment(Request $request, ProgramEnrollment $enrollment)
    {
        $this->authorizeManage($enrollment->program);

        $request->validate([
            'status' => 'required|in:active,completed,dropped',
            'remarks' => 'nullable|string',
        ]);

        $enrollment->update([
            'status' => $request->status,
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->route('programs.show', $enrollment->program)
            ->with('success', 'Enrollment status updated!');
    }

    protected function authorizeManage(Program $program): void
    {
        abort_unless($program->isManagedBy(Auth::user()), 403, 'Only the assigned personnel can manage this program.');
        abort_unless(session()->get("unlocked_programs.{$program->id}", false), 403, 'Enter your PIN to unlock this program before managing it.');
    }
}