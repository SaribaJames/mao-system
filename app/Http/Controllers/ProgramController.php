<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramEnrollment;
use App\Models\Farmer;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // For the per-activity distribution lists: the stock items a
        // coordinator can hand out, the barangay dropdown for walk-ins, and
        // the farmers enrolled in this program (the usual recipients).
        $program->load(['activities.recipients.barangay', 'achievements']);
        $stocks = Stock::orderBy('item_name')->get();
        $barangays = \App\Models\Barangay::orderBy('name')->get();
        $enrolledFarmers = $program->farmers()->orderBy('surname')->orderBy('first_name')->get();

        return view('programs.show', compact(
            'program',
            'enrollments',
            'farmers',
            'staffUsers',
            'isAssignedUser',
            'isUnlocked',
            'stocks',
            'barangays',
            'enrolledFarmers'
        ));
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

    /**
     * Read-only access: viewing, printing and generating reports.
     *
     * Deliberately looser than authorizeManage() — an admin can always read a
     * program, and the assigned coordinator can read theirs without entering
     * the PIN first. The PIN protects CHANGES, not looking at the data, and
     * the same rule already governs show().
     */
    protected function authorizeView(Program $program): void
    {
        abort_unless(
            Auth::user()->isAdmin() || $program->isManagedBy(Auth::user()),
            403,
            'You are not assigned to this program.'
        );
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

        return redirect()->route('programs.show', $activity->program)
            ->with('success', 'Activity updated successfully!');
    }

    public function destroyActivity(\App\Models\ProgramActivity $activity)
    {
        $this->authorizeManage($activity->program);
        $program = $activity->program;

        // Put back everything this activity's distribution list took out of
        // inventory. Without this the recipient rows cascade away and the
        // stock stays deducted forever, with nothing left to reverse it.
        $reversed = 0;
        foreach ($activity->recipients as $recipient) {
            $this->reverseRecipientStock($recipient);
            $reversed++;
        }

        $activity->delete();

        $message = 'Activity deleted.';
        if ($reversed > 0) {
            $message .= " Stock released to {$reversed} farmer(s) has been returned to inventory.";
        }

        return redirect()->route('programs.show', $program)->with('success', $message);
    }

    // NOTE: stock is deducted in exactly two places — a normal release under
    // Stocks, and an activity's recipient list here. An activity's recipient
    // list is the coordinator's proof of distribution: it records who received
    // what, deducts the stock once, and prints for signatures. The report
    // below just sums those recipient rows; it never touches stock itself.

    public function report(Program $program)
    {
        $this->authorizeView($program);
        $program->load('activities.recipients');

        // Resources Distributed — summed per stock item across every farmer
        // on every activity's recipient list in this program.
        $resourceTotals = []; // stock_id => ['name' => ..., 'unit' => ..., 'qty' => ...]
        foreach ($program->activities as $activity) {
            foreach ($activity->distributedTotals() as $stockId => $row) {
                if (!isset($resourceTotals[$stockId])) {
                    $resourceTotals[$stockId] = ['name' => $row['name'], 'unit' => $row['unit'], 'qty' => 0];
                }
                $resourceTotals[$stockId]['qty'] += $row['qty'];
            }
        }

        // Drop items that were selected but never actually handed out.
        $resourceTotals = array_filter($resourceTotals, fn ($r) => $r['qty'] > 0);

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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('programs.report', compact('program', 'chartUrl', 'resourceTotals'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true);

        return $pdf->stream($program->name . '-Report.pdf');
    }

    // ---------------------------------------------------------------------
    // Activity recipient list — who received resources during this activity.
    // This is where a coordinator releases stock to a group of farmers, and
    // the printed list is their proof of distribution.
    // ---------------------------------------------------------------------

    /** Choose which stock items this activity hands out. */
    public function updateActivityItems(Request $request, \App\Models\ProgramActivity $activity)
    {
        $this->authorizeManage($activity->program);

        $request->validate([
            'stock_ids' => 'nullable|array',
            'stock_ids.*' => 'exists:stocks,id',
        ]);

        $newIds = array_map('intval', $request->stock_ids ?? []);

        // An item that farmers have already received can't be un-selected —
        // its quantities are recorded against them and its stock is deducted.
        $inUse = [];
        foreach ($activity->recipients as $recipient) {
            foreach ($recipient->quantities ?? [] as $stockId => $qty) {
                if ($qty > 0) {
                    $inUse[] = (int) $stockId;
                }
            }
        }
        $missing = array_diff(array_unique($inUse), $newIds);
        if (!empty($missing)) {
            $names = Stock::whereIn('id', $missing)->pluck('item_name')->implode(', ');
            return back()->with('error', "Cannot remove {$names} — farmers on this list have already received it. Remove those farmers first.");
        }

        $activity->update(['stock_ids' => $newIds]);

        return redirect()->route('programs.show', $activity->program)
            ->with('success', 'Items for this activity updated.');
    }

    /** Add one farmer by hand (a walk-in who isn't a registered farmer). */
    public function storeRecipient(Request $request, \App\Models\ProgramActivity $activity)
    {
        $this->authorizeManage($activity->program);

        $request->validate([
            'farmer_name' => 'required|string|max:150',
            'barangay_id' => 'nullable|exists:barangays,id',
            'address' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:1|max:120',
            'sex' => 'nullable|in:M,F',
            'quantities' => 'nullable|array',
        ]);

        $requested = [];
        foreach ($activity->stock_ids ?? [] as $stockId) {
            $requested[$stockId] = (float) ($request->input('quantities.' . $stockId) ?: 0);
        }

        $error = $this->releaseToRecipient($activity, $requested, [
            'farmer_id' => null,
            'farmer_name' => $request->farmer_name,
            'barangay_id' => $request->barangay_id,
            'address' => $request->address,
            'age' => $request->age,
            'sex' => $request->sex,
        ]);

        if ($error) {
            return back()->withErrors(['quantities' => $error])->withInput();
        }

        return redirect()->route('programs.show', $activity->program)
            ->with('success', 'Farmer added and stock released.');
    }

    /**
     * Add a batch of enrolled/registered farmers at once, ticked off a
     * checklist. Every ticked farmer gets the SAME quantity per item — for
     * different amounts, add those farmers individually instead.
     */
    public function storeRecipientsFromFarmers(Request $request, \App\Models\ProgramActivity $activity)
    {
        $this->authorizeManage($activity->program);

        $request->validate([
            'farmer_ids' => 'required|array|min:1',
            'farmer_ids.*' => 'exists:farmers,id',
            'quantities' => 'nullable|array',
        ]);

        $perFarmer = [];
        foreach ($activity->stock_ids ?? [] as $stockId) {
            $perFarmer[$stockId] = (float) ($request->input('quantities.' . $stockId) ?: 0);
        }

        if (array_sum($perFarmer) <= 0) {
            return back()->withErrors(['quantities' => 'Enter at least one quantity per selected farmer.'])->withInput();
        }

        // Check there's enough for ALL ticked farmers before releasing to any,
        // so a shortage never leaves the list half-processed.
        $farmerCount = count($request->farmer_ids);
        $stockItems = $activity->stockItems()->keyBy('id');
        foreach ($perFarmer as $stockId => $qty) {
            if ($qty <= 0) {
                continue;
            }
            $stock = $stockItems->get($stockId);
            $needed = $qty * $farmerCount;
            if (!$stock || $stock->remaining_stock < $needed) {
                $available = $stock->remaining_stock ?? 0;
                $name = $stock->item_name ?? 'item';
                $unit = $stock->unit ?? '';
                return back()->withErrors([
                    'quantities' => "Not enough {$name} for all {$farmerCount} farmer(s) at {$qty} {$unit} each ({$available} {$unit} left, {$needed} needed).",
                ])->withInput();
            }
        }

        $farmers = \App\Models\Farmer::whereIn('id', $request->farmer_ids)->get();

        $added = 0;
        $failed = [];
        foreach ($farmers as $farmer) {
            $error = $this->releaseToRecipient($activity, $perFarmer, [
                'farmer_id' => $farmer->id,
                'farmer_name' => $farmer->full_name,
                'barangay_id' => $farmer->barangay_id,
                // Column is 255 chars — a very long address would otherwise
                // fail the insert AFTER the stock was already deducted.
                'address' => mb_substr(trim($farmer->house_lot_number . ' ' . $farmer->street), 0, 255),
                'age' => $farmer->date_of_birth?->age,
                'sex' => $farmer->sex === 'male' ? 'M' : ($farmer->sex === 'female' ? 'F' : null),
            ]);

            if ($error) {
                // Never claim a farmer was added when they weren't — e.g. another
                // release consumed the stock between the check above and this loop.
                $failed[] = $farmer->full_name;
                continue;
            }
            $added++;
        }

        $redirect = redirect()->route('programs.show', $activity->program)
            ->with('success', $added . ' farmer(s) added and stock released.');

        if (!empty($failed)) {
            $redirect->with('error', 'Could not add: ' . implode(', ', $failed) . ' — not enough stock remaining.');
        }

        return $redirect;
    }

    /**
     * Shared release logic. Validates availability, deducts the stock, records
     * the StockTransaction ids on the recipient row so removal can reverse
     * exactly what was taken. Returns an error string, or null on success.
     */
    private function releaseToRecipient(\App\Models\ProgramActivity $activity, array $requestedQuantities, array $attributes): ?string
    {
        $stockItems = $activity->stockItems()->keyBy('id');
        $requested = [];

        foreach ($requestedQuantities as $stockId => $qty) {
            if ($qty <= 0) {
                continue;
            }
            $stock = $stockItems->get($stockId);
            if (!$stock) {
                continue;
            }
            if ($stock->remaining_stock < $qty) {
                return "Not enough {$stock->item_name} remaining ({$stock->remaining_stock} {$stock->unit} left, {$qty} requested).";
            }
            $requested[$stockId] = $qty;
        }

        if (empty($requested)) {
            return 'Enter at least one quantity.';
        }

        // All-or-nothing: without this, a failure while writing the recipient
        // row would leave the stock deducted with nobody recorded as having
        // received it — and no row left to reverse it from.
        try {
            DB::transaction(function () use ($requested, $stockItems, $activity, $attributes) {
                $quantities = [];
                $transactionIds = [];

                foreach ($requested as $stockId => $qty) {
                    $stock = $stockItems->get($stockId);

                    $transaction = \App\Models\StockTransaction::create([
                        'stock_id' => $stock->id,
                        'type' => 'release',
                        'quantity' => $qty,
                        'recipient' => $attributes['farmer_name'],
                        'farmer_id' => $attributes['farmer_id'],
                        'notes' => "Program activity: {$activity->name} ({$activity->program->name})",
                        'processed_by' => Auth::id(),
                    ]);

                    $stock->remaining_stock -= $qty;
                    $stock->released_stock += $qty;
                    $stock->updateStatus(); // updateStatus() saves the row itself

                    $quantities[$stockId] = $qty;
                    $transactionIds[] = $transaction->id;
                }

                $activity->recipients()->create(array_merge($attributes, [
                    'quantities' => $quantities,
                    'transaction_ids' => $transactionIds,
                ]));
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                "Activity #{$activity->id}: release to {$attributes['farmer_name']} failed and was rolled back: {$e->getMessage()}"
            );
            return "Could not record {$attributes['farmer_name']} — nothing was deducted. Please try again.";
        }

        return null;
    }

    /**
     * Put back exactly what was deducted for one recipient and delete the
     * transactions that recorded it, so the audit trail shows no phantom
     * release. Shared by removing one farmer and deleting a whole activity.
     */
    private function reverseRecipientStock(\App\Models\ProgramActivityRecipient $recipient): void
    {
        foreach ($recipient->quantities ?? [] as $stockId => $qty) {
            $stock = Stock::find($stockId);
            if (!$stock) {
                continue;
            }
            $stock->remaining_stock += $qty;
            $stock->released_stock -= $qty;
            $stock->updateStatus(); // updateStatus() saves the row itself
        }

        if (!empty($recipient->transaction_ids)) {
            \App\Models\StockTransaction::whereIn('id', $recipient->transaction_ids)->delete();
        }
    }

    /** Removing a farmer puts back exactly what was deducted for them. */
    public function destroyRecipient(\App\Models\ProgramActivityRecipient $recipient)
    {
        $activity = $recipient->activity;
        $this->authorizeManage($activity->program);

        $this->reverseRecipientStock($recipient);
        $recipient->delete();

        return redirect()->route('programs.show', $activity->program)
            ->with('success', 'Farmer removed and stock restored.');
    }

    /** Printable distribution list with a blank signature column. */
    public function printRecipients(\App\Models\ProgramActivity $activity)
    {
        $this->authorizeView($activity->program);

        $activity->load(['recipients.barangay', 'program']);
        $stockItems = $activity->stockItems();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.activity-recipients-pdf', compact('activity', 'stockItems'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Distribution-List-' . $activity->id . '.pdf');
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


