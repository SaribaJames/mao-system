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
            'program',
            'enrollments',
            'farmers',
            'staffUsers',
            'isAssignedUser',
            'isUnlocked'
        ));
        $program->load('achievements');
    }

    public function unlock(Request $request, Program $program)
    {
        abort_unless($program->isManagedBy(Auth::user()), 403, 'You are not the assigned personnel for this program.');




        if (!Auth::user()->verifyPin($request->pin)) {
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


    public function storeActivity(Request $request, Program $program)
    {
        $this->authorizeManage($program);

        $request->validate([
            'name' => 'required|string|max:255',
            'performance_achieved' => 'nullable|string',
            'achieved_value' => 'nullable|numeric',
            'challenges_encountered' => 'nullable|string',
            'proposed_intervention' => 'nullable|string',
            'target_performance' => 'nullable|string',
            'target_value' => 'nullable|numeric',
            'value_unit' => 'nullable|string|max:50',
            'expenditure_item' => 'nullable|string|max:255',
            'budget_years' => 'nullable|array',
            'budget_amounts' => 'nullable|array',
        ]);

        $budgetBreakdown = [];
        if ($request->filled('budget_years')) {
            foreach ($request->budget_years as $i => $year) {
                if (blank($year))
                    continue;
                $amount = $request->budget_amounts[$i] ?? null;
                $budgetBreakdown[$year] = $amount !== null ? str_replace(',', '', $amount) : 0;
            }
        }

        $activity = $program->activities()->create([
            'name' => $request->name,
            'performance_achieved' => $request->performance_achieved,
            'achieved_value' => $request->achieved_value,
            'challenges_encountered' => $request->challenges_encountered,
            'proposed_intervention' => $request->proposed_intervention,
            'target_performance' => $request->target_performance,
            'target_value' => $request->target_value,
            'value_unit' => $request->value_unit,
            'expenditure_item' => $request->expenditure_item,
            'budget_breakdown' => $budgetBreakdown,
            'created_by' => Auth::id(),
        ]);

        $this->applyStockUsage($request, $activity);

        return redirect()->route('programs.show', $program)
            ->with('success', 'Activity added successfully!');
    }

    public function storeAchievement(Request $request, Program $program)
    {
        $this->authorizeManage($program);

        $request->validate([
            'photo' => 'required|image|max:5120',
            'caption' => 'nullable|string|max:500',
        ]);

        $path = $request->file('photo')->store('program-achievements', 'cloudinary');

        $program->achievements()->create([
            'photo_path' => $path,
            'caption' => $request->caption,
            'posted_by' => Auth::id(),
        ]);

        return redirect()->route('programs.show', $program)
            ->with('success', 'Achievement photo posted!');
    }

    public function destroyAchievement(\App\Models\ProgramAchievement $achievement)
    {
        $this->authorizeManage($achievement->program);

        \Illuminate\Support\Facades\Storage::disk('cloudinary')->delete($achievement->photo_path);
        $achievement->delete();

        return redirect()->back()->with('success', 'Achievement photo removed.');
    }

    public function updateActivity(Request $request, \App\Models\ProgramActivity $activity)
    {
        $this->authorizeManage($activity->program);

        $request->validate([
            'name' => 'required|string|max:255',
            'performance_achieved' => 'nullable|string',
            'achieved_value' => 'nullable|numeric',
            'challenges_encountered' => 'nullable|string',
            'proposed_intervention' => 'nullable|string',
            'target_performance' => 'nullable|string',
            'target_value' => 'nullable|numeric',
            'value_unit' => 'nullable|string|max:50',
            'expenditure_item' => 'nullable|string|max:255',
            'budget_years' => 'nullable|array',
            'budget_amounts' => 'nullable|array',
        ]);

        $budgetBreakdown = [];
        if ($request->filled('budget_years')) {
            foreach ($request->budget_years as $i => $year) {
                if (blank($year))
                    continue;
                $amount = $request->budget_amounts[$i] ?? null;
                $budgetBreakdown[$year] = $amount !== null ? str_replace(',', '', $amount) : 0;
            }
        }

        $activity->update([
            'name' => $request->name,
            'performance_achieved' => $request->performance_achieved,
            'achieved_value' => $request->achieved_value,
            'challenges_encountered' => $request->challenges_encountered,
            'proposed_intervention' => $request->proposed_intervention,
            'target_performance' => $request->target_performance,
            'target_value' => $request->target_value,
            'value_unit' => $request->value_unit,
            'expenditure_item' => $request->expenditure_item,
            'budget_breakdown' => $budgetBreakdown,
        ]);

        $this->applyStockUsage($request, $activity);

        return redirect()->route('programs.show', $activity->program)
            ->with('success', 'Activity updated successfully!');
    }

    public function destroyActivity(\App\Models\ProgramActivity $activity)
    {
        $this->authorizeManage($activity->program);
        $program = $activity->program;
        $activity->delete();

        return redirect()->route('programs.show', $program)
            ->with('success', 'Activity deleted.');
    }

    private function applyStockUsage(Request $request, \App\Models\ProgramActivity $activity): void
    {
        if (!$request->filled('stock_ids')) {
            return;
        }

        foreach ($request->stock_ids as $i => $stockId) {
            if (blank($stockId))
                continue;
            $qty = (int) ($request->stock_quantities[$i] ?? 0);
            if ($qty <= 0)
                continue;

            $stock = \App\Models\Stock::find($stockId);
            if (!$stock || $stock->remaining_stock < $qty)
                continue;

            $transaction = $stock->transactions()->create([
                'type' => 'release',
                'quantity' => $qty,
                'recipient' => $activity->program->name . ' — ' . $activity->name,
                'notes' => 'Auto-released for program activity',
                'processed_by' => Auth::id(),
            ]);

            $stock->remaining_stock -= $qty;
            $stock->released_stock += $qty;
            $stock->save();
            $stock->updateStatus();

            $activity->stockUsages()->create([
                'stock_id' => $stock->id,
                'quantity_used' => $qty,
                'stock_transaction_id' => $transaction->id,
            ]);
        }
    }


    public function report(Program $program)
    {
        $this->authorizeManage($program);
        $program->load('activities');

        // Build a budget-distribution pie chart (as a static image, since dompdf can't run JS/Chart.js)
        $pieLabels = [];
        $pieData = [];
        foreach ($program->activities as $activity) {
            $total = array_sum($activity->budget_breakdown ?? []);
            if ($total > 0) {
                $pieLabels[] = $activity->name;
                $pieData[] = $total;
            }
        }

        $chartUrl = null;
        if (count($pieData) > 0) {
            $chartConfig = [
                'type' => 'pie',
                'data' => [
                    'labels' => $pieLabels,
                    'datasets' => [
                        [
                            'data' => $pieData,
                            'backgroundColor' => ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#7c3aed', '#0891b2', '#db2777', '#65a30d'],
                        ]
                    ],
                ],
                'options' => [
                    'plugins' => [
                        'title' => ['display' => true, 'text' => 'Budget Distribution by Activity'],
                        'legend' => ['position' => 'bottom'],
                    ],
                ],
            ];
            $chartUrl = 'https://quickchart.io/chart?width=500&height=350&c=' . urlencode(json_encode($chartConfig));
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('programs.report', compact('program', 'chartUrl'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true);

        return $pdf->stream($program->name . '-Report.pdf');
    }

    public function storeDispersalRecord(Request $request, Program $program)
    {
        $this->authorizeManage($program);

        $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'piglets_received' => 'nullable|integer|min:0',
            'date_received' => 'nullable|date',
            'piglets_returned' => 'nullable|integer|min:0',
            'date_returned' => 'nullable|date',
            'status' => 'required|in:waitlisted,received,compliant',
            'notes' => 'nullable|string',
        ]);

        \App\Models\SwineDispersalRecord::create([
            'program_id' => $program->id,
            'farmer_id' => $request->farmer_id,
            'piglets_received' => $request->piglets_received ?? 0,
            'date_received' => $request->date_received,
            'piglets_returned' => $request->piglets_returned ?? 0,
            'date_returned' => $request->date_returned,
            'status' => $request->status,
            'notes' => $request->notes,
            'recorded_by' => Auth::id(),
        ]);

        return redirect()->route('programs.show', $program)
            ->with('success', 'Dispersal record added successfully!');
    }

    public function updateDispersalRecord(Request $request, \App\Models\SwineDispersalRecord $record)
    {
        $this->authorizeManage($record->program);

        $request->validate([
            'piglets_received' => 'nullable|integer|min:0',
            'date_received' => 'nullable|date',
            'piglets_returned' => 'nullable|integer|min:0',
            'date_returned' => 'nullable|date',
            'status' => 'required|in:waitlisted,received,compliant',
            'notes' => 'nullable|string',
        ]);

        $record->update([
            'piglets_received' => $request->piglets_received ?? 0,
            'date_received' => $request->date_received,
            'piglets_returned' => $request->piglets_returned ?? 0,
            'date_returned' => $request->date_returned,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('programs.show', $record->program)
            ->with('success', 'Dispersal record updated successfully!');
    }

    public function destroyDispersalRecord(\App\Models\SwineDispersalRecord $record)
    {
        $this->authorizeManage($record->program);
        $program = $record->program;
        $record->delete();

        return redirect()->route('programs.show', $program)
            ->with('success', 'Dispersal record deleted.');
    }

}


