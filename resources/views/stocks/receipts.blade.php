@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Receiving History</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Every batch of resources received from a partner, donor, or other source</p>
    </div>
    <a href="{{ route('stocks.index') }}" class="text-sm text-primary hover:underline font-medium flex items-center gap-1">
        <i class="fa-solid fa-arrow-left"></i> Back to Stocks
    </a>
</div>

{{-- Filter --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Partner / Source</label>
            <input type="text" name="partner" value="{{ request('partner') }}" placeholder="e.g. DA Regional Office"
                   class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Category</label>
            <select name="category" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">All Categories</option>
                @foreach(['seeds' => 'Seeds', 'fertilizer' => 'Fertilizer', 'pesticide' => 'Pesticide', 'equipment' => 'Equipment', 'tools' => 'Tools', 'others' => 'Others'] as $value => $label)
                    <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary-dark transition">
            Filter
        </button>
        @if(request()->anyFilled(['partner', 'category', 'date_from', 'date_to']))
            <a href="{{ route('stocks.receipts.index') }}" class="text-sm text-gray-500 hover:underline px-2 py-2">Clear</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Date Received</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Item</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Quantity</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Partner / Source</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Reference No.</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Logged By</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($receipts as $receipt)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                    {{ $receipt->received_date ? $receipt->received_date->format('M d, Y') : $receipt->created_at->format('M d, Y') }}
                </td>
                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">
                    {{ $receipt->stock->item_name ?? '—' }}
                    <span class="text-xs text-gray-400 capitalize">({{ $receipt->stock->category ?? '—' }})</span>
                </td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                    {{ number_format($receipt->quantity, 2) }} {{ $receipt->stock->unit ?? '' }}
                </td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $receipt->partner_name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $receipt->reference_number ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $receipt->processedBy?->name ?? '—' }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('stocks.receipts.print', $receipt) }}" target="_blank"
                       class="text-primary hover:underline text-xs font-medium">
                        <i class="fa-solid fa-print"></i> Print Receipt
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">No resources received yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-border-soft dark:border-gray-700">
        {{ $receipts->links() }}
    </div>
</div>

@endsection
