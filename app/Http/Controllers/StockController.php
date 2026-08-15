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
            'item_name' => 'required|string|max:100',
            'category'  => 'required|in:seeds,fertilizer,pesticide,equipment,tools,others',
            'unit'      => 'required|string|max:20',
            'quantity'  => 'required|numeric|min:0.01',
        ]);

        // Equipment and Tools are countable items — no fractional units
        if (in_array($request->category, ['equipment', 'tools']) && floor($request->quantity) != $request->quantity) {
            return back()->withErrors(['quantity' => 'Equipment and Tools must be added in whole numbers (no decimals).'])->withInput();
        }

        $stock = Stock::where('item_name', $request->item_name)
                      ->where('category', $request->category)
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

        StockTransaction::create([
            'stock_id'     => $stock->id,
            'type'         => 'add',
            'quantity'     => $request->quantity,
            'notes'        => $request->notes,
            'processed_by' => Auth::id(),
        ]);

        return redirect()->route('stocks.index')
            ->with('success', "Stock '{$stock->item_name}' added successfully!");
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

    protected function authorizeAccess(): void
    {
        abort_unless(
            Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff',
            403,
            'You are not assigned to manage Stocks.'
        );
    }
}