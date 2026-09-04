<?php

namespace App\Http\Controllers;

use App\Models\FarmerRequest;
use App\Models\Farmer;
use App\Models\Stock;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index()
    {
        $this->blockProgramStaff();

        $query = FarmerRequest::with(['farmer', 'farmer.barangay', 'submittedBy', 'program.assignedUser']);

        // Barangay rep can only see requests from their own barangay
        if (Auth::user()->isBarangayUser()) {
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
            if ($barangayId) {
                $query->whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId));
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filter by program — "mine" is the shortcut a coordinator wants:
        // just the requests belonging to the programs they run.
        if (request('program') === 'mine') {
            $myProgramIds = \App\Models\Program::where('assigned_user_id', Auth::id())->pluck('id');
            $query->whereIn('program_id', $myProgramIds);
        } elseif (request('program') === 'none') {
            $query->whereNull('program_id');
        } elseif (request('program')) {
            $query->where('program_id', request('program'));
        }

        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'like', '%' . $search . '%')
                  ->orWhereHas('farmer', function($fq) use ($search) {
                      $fq->where('first_name', 'like', '%' . $search . '%')
                         ->orWhere('surname', 'like', '%' . $search . '%');
                  });
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        // Stats also filtered by barangay for barangay reps
        if (Auth::user()->isBarangayUser()) {
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
            $baseQuery  = FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId));
            $total     = $baseQuery->count();
            $pending   = (clone $baseQuery)->where('status', 'pending')->count();
            $approved  = (clone $baseQuery)->where('status', 'approved')->count();
            $completed = (clone $baseQuery)->where('status', 'completed')->count();
        } else {
            $total     = FarmerRequest::count();
            $pending   = FarmerRequest::where('status', 'pending')->count();
            $approved  = FarmerRequest::where('status', 'approved')->count();
            $completed = FarmerRequest::where('status', 'completed')->count();
        }

        // Programs list powers the filter dropdown.
        $programs = \App\Models\Program::orderBy('name')->get();
        $hasOwnPrograms = \App\Models\Program::where('assigned_user_id', Auth::id())->exists();

        return view('requests.index', compact(
            'requests', 'total', 'pending', 'approved', 'completed',
            'programs', 'hasOwnPrograms'
        ));
    }

    public function create()
    {
        $this->blockProgramStaff();
        $this->authorizeSubmit();

        $farmersQuery = Farmer::with('activePrograms')->orderBy('surname');

        if (Auth::user()->isBarangayUser()) {
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
            $farmersQuery->where('barangay_id', $barangayId);
        }

        $farmers  = $farmersQuery->get();
        $stocks   = Stock::where('remaining_stock', '>', 0)->orderBy('item_name')->get();
        $programs = \App\Models\Program::orderBy('name')->get();

        return view('requests.create', compact('farmers', 'stocks', 'programs'));
    }

    public function store(Request $request)
    {
        $this->blockProgramStaff();
        $this->authorizeSubmit();

        $request->validate([
            'farmer_id'    => 'required|exists:farmers,id',
            'program_id'   => 'nullable|exists:programs,id',
            'request_type' => 'required',
            'item_service' => 'required|string|max:100',
        ]);

        $reqNumber = $this->nextRequestNumber();

        FarmerRequest::create([
            'request_number' => $reqNumber,
            'farmer_id'      => $request->farmer_id,
            'program_id'     => $request->program_id,
            'request_type'   => $request->request_type,
            'stock_id'       => $request->stock_id,
            'item_service'   => $request->input('item_service'),
            'quantity'       => $request->quantity,
            'quantity_unit'  => $request->quantity_unit,
            'purpose'        => $request->input('purpose'),
            'status'         => 'pending',
            'submitted_by'   => Auth::id(),
        ]);

        return redirect()->route('requests.index')
            ->with('success', "Request {$reqNumber} submitted successfully!");
    }

    public function show(FarmerRequest $farmerRequest)
    {
        $this->blockProgramStaff();

        $farmerRequest->load(['farmer', 'farmer.barangay', 'stock', 'submittedBy', 'processedBy', 'program.assignedUser']);
        return view('requests.show', ['request' => $farmerRequest]);
    }

    /**
     * Next reference number for the current year.
     *
     * Derived from the highest number already issued THIS YEAR, not from a
     * row count — a count breaks the moment any request is deleted, reissuing
     * a number that already exists, and it never resets in January.
     */
    private function nextRequestNumber(): string
    {
        $year = date('Y');
        $prefix = 'REQ-' . $year . '-';

        $last = FarmerRequest::where('request_number', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(request_number, ?) AS UNSIGNED) DESC', [strlen($prefix) + 1])
            ->value('request_number');

        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Who may approve, reject or complete this request.
     *
     * A request tagged to a program belongs to that program's coordinator —
     * they run it and hold the stock, so a staff member from another program
     * has no business deciding it. Admins can always act as the fallback so a
     * request never gets stuck while a coordinator is away.
     *
     * Untagged requests (certifications, technical assistance) stay open to
     * any staff member, as before.
     */
    public static function canProcess(FarmerRequest $request, $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->role?->name !== 'staff') {
            return false;
        }

        // Untagged request: any staff member may handle it.
        if (!$request->program_id) {
            return true;
        }

        // Tagged: only the coordinator assigned to that program.
        return $request->program && $request->program->assigned_user_id === $user->id;
    }

    /**
     * Which statuses a request can legally move to from where it is now.
     * Stops a pending request being marked Completed without anyone ever
     * approving it, and stops a finished request being reopened.
     */
    public static function allowedTransitions(string $current): array
    {
        return match ($current) {
            'pending'  => ['approved', 'rejected'],
            'approved' => ['completed', 'rejected'],
            default    => [], // completed and rejected are final
        };
    }

    public function updateStatus(Request $req, FarmerRequest $farmerRequest)
    {
        $this->blockProgramStaff();

        if (!self::canProcess($farmerRequest, Auth::user())) {
            $coordinator = $farmerRequest->program?->assignedUser?->name;
            return back()->with('error', $coordinator
                ? "Only the {$farmerRequest->program->name} coordinator ({$coordinator}) or an admin can act on this request."
                : "You are not allowed to act on this request.");
        }

        $allowed = self::allowedTransitions($farmerRequest->status);

        if (empty($allowed)) {
            return back()->with('error', "Request {$farmerRequest->request_number} is already {$farmerRequest->status} and can no longer be changed.");
        }

        $req->validate([
            'status'  => 'required|in:' . implode(',', $allowed),
            // A rejection without a reason tells the barangay rep nothing.
            'remarks' => $req->input('status') === 'rejected'
                ? 'required|string|min:10|max:500'
                : 'nullable|string|max:500',
        ], [
            'status.in'       => 'That action is not available for a request that is currently ' . $farmerRequest->status . '.',
            'remarks.required' => 'Please state why this request is being rejected.',
            'remarks.min'      => 'Please give a clearer reason so the barangay representative understands the decision.',
        ]);

        $status = $req->input('status');

        // Completing a goods request is the point where stock actually leaves
        // the office, so that is where inventory is deducted — approving alone
        // changes nothing physical.
        if ($status === 'completed' && $farmerRequest->stock_id && !$farmerRequest->stock_transaction_id) {
            $stock = Stock::find($farmerRequest->stock_id);
            $qty = (float) $farmerRequest->quantity;

            if (!$stock) {
                return back()->with('error', 'The requested item no longer exists in Stocks, so this request cannot be completed.');
            }

            if ($qty <= 0) {
                return back()->with('error', 'This request has no quantity recorded, so no stock can be released.');
            }

            if ($stock->remaining_stock < $qty) {
                return back()->with('error', "Not enough {$stock->item_name} to complete this request ({$stock->remaining_stock} {$stock->unit} left, {$qty} needed).");
            }

            try {
                DB::transaction(function () use ($stock, $qty, $farmerRequest, $req, $status) {
                    $transaction = StockTransaction::create([
                        'stock_id'     => $stock->id,
                        'type'         => 'release',
                        'quantity'     => $qty,
                        'recipient'    => $farmerRequest->farmer?->full_name ?? 'Farmer',
                        'farmer_id'    => $farmerRequest->farmer_id,
                        'notes'        => "Service request {$farmerRequest->request_number}",
                        'processed_by' => Auth::id(),
                    ]);

                    $stock->remaining_stock -= $qty;
                    $stock->released_stock  += $qty;
                    $stock->updateStatus(); // updateStatus() saves the row itself

                    $farmerRequest->update([
                        'status'               => $status,
                        'remarks'              => $req->input('remarks'),
                        'stock_transaction_id' => $transaction->id,
                        'released_quantity'    => $qty,
                        'processed_by'         => Auth::id(),
                        'processed_at'         => now(),
                    ]);
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Request {$farmerRequest->request_number}: completion rolled back: {$e->getMessage()}");
                return back()->with('error', 'Could not complete the request — nothing was deducted. Please try again.');
            }

            return redirect()->route('requests.index')
                ->with('success', "Request {$farmerRequest->request_number} completed and {$qty} {$stock->unit} of {$stock->item_name} released.");
        }

        $farmerRequest->update([
            'status'       => $status,
            'remarks'      => $req->input('remarks'),
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('requests.index')
            ->with('success', "Request {$farmerRequest->request_number} has been {$status}!");
    }

    /**
     * A barangay rep withdrawing their own request before anyone acts on it.
     * Only while still pending — once processed it is part of the record.
     */
    public function destroy(FarmerRequest $farmerRequest)
    {
        $this->blockProgramStaff();
        abort_unless(Auth::user()->isBarangayUser(), 403);
        abort_unless($farmerRequest->submitted_by === Auth::id(), 403, 'You can only withdraw requests you submitted.');

        if ($farmerRequest->status !== 'pending') {
            return back()->with('error', "Request {$farmerRequest->request_number} has already been {$farmerRequest->status} and can no longer be withdrawn.");
        }

        $number = $farmerRequest->request_number;
        $farmerRequest->delete();

        return redirect()->route('requests.index')
            ->with('success', "Request {$number} withdrawn.");
    }

    /**
     * No longer restricts anyone — see the body. Left in place so the
     * intent of the original design stays visible in history.
     */
    /**
     * Only barangay representatives raise requests — they submit on behalf of
     * a farmer in their barangay. MAO staff and admins process requests; when
     * they serve a farmer directly they log a Service Record or release stock
     * from Stocks, which is the same outcome without the request round-trip.
     */
    protected function authorizeSubmit(): void
    {
        abort_unless(
            Auth::user()->isBarangayUser(),
            403,
            'Only barangay representatives can submit requests. To serve a farmer directly, use Service Records or release stock from Stocks.'
        );
    }

    protected function blockProgramStaff(): void
    {
        // Previously this 403'd any staff member assigned to a program, which
        // meant the Rice coordinator could not see a request for rice seed.
        // Requests can now be tagged to a program, and coordinators, staff and
        // admins may all act on them — so there is nothing left to block.
        // Kept as a no-op so every call site stays readable.
    }
}