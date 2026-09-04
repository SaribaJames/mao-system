<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\Farmer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $canAccess = Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff';

        if (! $canAccess) {
            return view('stocks.index', [
                'canAccess' => false,
                'stocks' => collect(),
                'totalStock' => 0,
                'releasedStock' => 0,
                'remainingStock' => 0,
            ]);
        }

        $query = Stock::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $stocks = $query->latest()->paginate(15)->withQueryString();
        $totalStock     = Stock::sum('total_stock');
        $releasedStock  = Stock::sum('released_stock');
        $remainingStock = Stock::sum('remaining_stock');
        return view('stocks.index', compact('stocks', 'totalStock', 'releasedStock', 'remainingStock', 'canAccess'));
    }

    public function store(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'item_name'        => 'required|string|max:100',
            'category'         => 'required|in:seeds,fertilizer,pesticide,equipment,tools,others',
            'unit'             => 'required|string|max:20',
            'quantity'         => 'required|numeric|min:0.01',
            'partner_name'     => 'required|string|max:150',
            'reference_number' => 'nullable|string|max:100',
            'received_date'    => 'required|date',
            'attachment'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Equipment and Tools are countable items — no fractional units
        if (in_array($request->category, ['equipment', 'tools']) && floor($request->quantity) != $request->quantity) {
            return back()->withErrors(['quantity' => 'Equipment and Tools must be added in whole numbers (no decimals).'])->withInput();
        }

        // Match on unit too. Without it, 20 sacks added to an existing "kg" row
        // becomes 20 kg — the quantities are added together as if the units
        // were the same, and the row keeps showing the old unit.
        $stock = Stock::where('item_name', $request->item_name)
                      ->where('category', $request->category)
                      ->where('unit', $request->unit)
                      ->first();

        if ($stock) {
            $stock->total_stock     += $request->quantity;
            $stock->remaining_stock += $request->quantity;
            $stock->updateStatus();
        } else {
            $stock = Stock::create([
                'item_name'       => $request->item_name,
                'category'        => $request->category,
                'unit'            => $request->unit,
                'total_stock'     => $request->quantity,
                'released_stock'  => 0,
                'remaining_stock' => $request->quantity,
                'description'     => $request->description,
                'added_by'        => Auth::id(),
            ]);
            $stock->updateStatus();
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('stock-receipts', 'cloudinary');
        }

        $transaction = StockTransaction::create([
            'stock_id'         => $stock->id,
            'type'             => 'add',
            'quantity'         => $request->quantity,
            'notes'            => $request->notes,
            'processed_by'     => Auth::id(),
            'partner_name'     => $request->partner_name,
            'reference_number' => $request->reference_number,
            'received_date'    => $request->received_date,
            'attachment_path'  => $attachmentPath,
        ]);

        return redirect()->route('stocks.index')
            ->with('success', "Stock '{$stock->item_name}' added successfully!")
            ->with('newReceiptId', $transaction->id);
    }

    public function release(Request $request, Stock $stock)
    {
        $this->authorizeAccess();

        $request->validate([
            'quantity'  => 'required|numeric|min:0.01|max:' . $stock->remaining_stock,
            'recipient' => 'required|string|max:100',
        ]);

        // Equipment and Tools are countable items — no fractional units
        if (in_array($stock->category, ['equipment', 'tools']) && floor($request->quantity) != $request->quantity) {
            return back()->withErrors(['quantity' => 'Equipment and Tools must be released in whole numbers (no decimals).'])->withInput();
        }

        $stock->released_stock  += $request->quantity;
        $stock->remaining_stock -= $request->quantity;
        $stock->updateStatus();

        StockTransaction::create([
            'stock_id'     => $stock->id,
            'type'         => 'release',
            'quantity'     => $request->quantity,
            'recipient'    => $request->recipient,
            'farmer_id'    => $request->farmer_id,
            'notes'        => $request->notes,
            'processed_by' => Auth::id(),
        ]);

        return redirect()->route('stocks.index')
            ->with('success', "Stock released to {$request->recipient} successfully!");
    }

    public function destroy(Stock $stock)
    {
        $this->authorizeAccess();

        $stock->delete();
        return redirect()->route('stocks.index')
            ->with('success', 'Stock item deleted successfully!');
    }

    /**
     * Receiving history — every "add" transaction, i.e. every batch of
     * resources received from a partner/donor/source, filterable so the
     * admin can pull up everything received from a specific partnership.
     */
    public function receipts(Request $request)
    {
        $this->authorizeAccess();

        $query = StockTransaction::with(['stock', 'processedBy'])
            ->where('type', 'add');

        if ($request->filled('partner')) {
            $query->where('partner_name', 'like', '%' . $request->partner . '%');
        }

        if ($request->filled('category')) {
            $query->whereHas('stock', fn ($q) => $q->where('category', $request->category));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('received_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('received_date', '<=', $request->date_to);
        }

        $receipts = $query->latest('received_date')->paginate(15)->withQueryString();

        return view('stocks.receipts', compact('receipts'));
    }

    /**
     * Printable "Goods Received Report" for a single receiving transaction —
     * the documentation proving the office received a specific batch of
     * resources from a specific partner.
     */
    public function printReceipt(StockTransaction $transaction)
    {
        $this->authorizeAccess();

        abort_unless($transaction->type === 'add', 404);

        $transaction->load(['stock', 'processedBy']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.stock-receipt-pdf', compact('transaction'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('MAO-Goods-Received-' . $transaction->id . '.pdf');
    }

    protected function authorizeAccess(): void
    {
        abort_unless(
            Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff',
            403,
            'Only MAO staff and admins can manage Stocks.'
        );
    }
}
