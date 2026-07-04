<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\Farmer;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRecordController extends Controller
{
    public function index()
    {
        $query = ServiceRecord::with(['farmer', 'farmer.barangay', 'processedBy']);

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('service_number', 'like', '%' . $search . '%')
                    ->orWhereHas('farmer', function ($fq) use ($search) {
                        $fq->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('surname', 'like', '%' . $search . '%');
                    });
            });
        }

        if (request('service_type')) {
            $query->where('service_type', request('service_type'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $records = $query->latest()->paginate(15);
        $total = ServiceRecord::count();
        $completed = ServiceRecord::where('status', 'completed')->count();
        $ongoing = ServiceRecord::where('status', 'ongoing')->count();
        $thisMonth = ServiceRecord::whereMonth('service_date', now()->month)->count();

        return view('service-records.index', compact(
            'records',
            'total',
            'completed',
            'ongoing',
            'thisMonth'
        ));
    }

    public function create()
    {
        $farmers = Farmer::orderBy('surname')->get();
        $stocks = Stock::where('remaining_stock', '>', 0)->orderBy('item_name')->get();
        return view('service-records.create', compact('farmers', 'stocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'service_type' => 'required',
            'description' => 'nullable|string',
            'status' => 'required|in:completed,ongoing,cancelled',
        ]);

        $svcNumber = 'SVC-' . date('Y') . '-' . str_pad(ServiceRecord::count() + 1, 5, '0', STR_PAD_LEFT);

        ServiceRecord::create([
            'service_number' => $svcNumber,
            'farmer_id' => $request->farmer_id,
            'service_type' => $request->service_type,
            'description' => $request->input('description'),
            'items_provided' => $request->input('items_provided'),
            'quantity' => $request->quantity,
            'quantity_unit' => $request->quantity_unit,
            'stock_id' => $request->stock_id,
            'status' => $request->status,
            'remarks' => $request->input('remarks'),
            'processed_by' => Auth::id(),
            'service_date' => now(),
        ]);

        return redirect()->route('service-records.index')
            ->with('success', "Service Record {$svcNumber} created successfully!");
    }

    public function show(ServiceRecord $serviceRecord)
    {
        $serviceRecord->load(['farmer', 'farmer.barangay', 'stock', 'processedBy']);
        return view('service-records.show', compact('serviceRecord'));
    }

    public function update(Request $request, ServiceRecord $serviceRecord)
    {
        $request->validate([
            'status' => 'required|in:completed,ongoing,cancelled',
            'remarks' => 'nullable|string',
        ]);

        $serviceRecord->update([
            'status' => $request->status,
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->route('service-records.show', $serviceRecord)
            ->with('success', "Service Record {$serviceRecord->service_number} status updated to {$request->status}!");
    }


}