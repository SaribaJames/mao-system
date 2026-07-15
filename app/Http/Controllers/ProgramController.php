<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\Farmer;
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
        ])->orderBy('name')->get();

        $totalPrograms = $programs->count();
        $totalActiveEnrollments = ProgramEnrollment::where('status', 'active')->count();

        return view('programs.index', compact('programs', 'totalPrograms', 'totalActiveEnrollments'));
    }

    public function show(Program $program)
    {
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

        return view('programs.show', compact('program', 'enrollments', 'farmers'));
    }

    public function enroll(Request $request, Program $program)
    {
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
}
