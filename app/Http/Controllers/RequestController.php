<?php

namespace App\Http\Controllers;

use App\Models\FarmerRequest;
use App\Models\Farmer;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index()
    {
        $query = FarmerRequest::with(['farmer', 'farmer.barangay', 'submittedBy']);

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

        $requests = $query->latest()->paginate(15);

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

        return view('requests.index', compact(
            'requests', 'total', 'pending', 'approved', 'completed'
        ));
    }

    public function create()
    {
        $farmersQuery = Farmer::orderBy('surname');

        if (Auth::user()->isBarangayUser()) {
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
            $farmersQuery->where('barangay_id', $barangayId);
        }

        $farmers = $farmersQuery->get();
        $stocks  = Stock::where('remaining_stock', '>', 0)->orderBy('item_name')->get();
        return view('requests.create', compact('farmers', 'stocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'farmer_id'    => 'required|exists:farmers,id',
            'request_type' => 'required',
            'item_service' => 'required|string|max:100',
        ]);

        $reqNumber = 'REQ-' . date('Y') . '-' . str_pad(FarmerRequest::count() + 1, 5, '0', STR_PAD_LEFT);

        FarmerRequest::create([
            'request_number' => $reqNumber,
            'farmer_id'      => $request->farmer_id,
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
        $farmerRequest->load(['farmer', 'farmer.barangay', 'stock', 'submittedBy', 'processedBy']);
        return view('requests.show', ['request' => $farmerRequest]);
    }

    public function updateStatus(Request $req, FarmerRequest $farmerRequest)
    {
        $req->validate([
            'status'  => 'required|in:approved,rejected,completed',
            'remarks' => 'nullable|string',
        ]);

        $farmerRequest->update([
            'status'       => $req->input('status'),
            'remarks'      => $req->input('remarks'),
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('requests.index')
            ->with('success', "Request {$farmerRequest->request_number} has been {$req->input('status')}!");
    }
}