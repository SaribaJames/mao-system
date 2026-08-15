@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Stocks & Inventory</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Manage agricultural supplies inventory</p>
    </div>
    @if($canAccess)
    <button onclick="document.getElementById('addStockModal').classList.remove('hidden')"
            class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
        <i class="fa-solid fa-plus"></i> Add Stock
    </button>
    @endif
</div>

@if(!$canAccess)

    {{-- Restricted note — shown instead of any stock data --}}
    <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-border-soft dark:border-gray-700 p-6 mt-10 text-center">
        <div class="w-14 h-14 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-lock text-yellow-600 dark:text-yellow-400 text-xl"></i>
        </div>
        <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">Restricted</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Only the registered/assigned Stocks personnel (or an admin) can access this module.</p>
    </div>

@else

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm rounded-md p-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-border-soft dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg">
                    <i class="fa-solid fa-boxes-stacked text-blue-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Stock</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($totalStock, 0) }}</p>
                    <p class="text-xs text-gray-400">Units across all categories</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-border-soft dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-100 dark:bg-yellow-900/30 p-2 rounded-lg">
                    <i class="fa-solid fa-arrow-up-from-bracket text-yellow-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Released Stock</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($releasedStock, 0) }}</p>
                    <p class="text-xs text-gray-400">Units distributed to farmers</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-border-soft dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-lg">
                    <i class="fa-solid fa-warehouse text-green-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Remaining Stock</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($remainingStock, 0) }}</p>
                    <p class="text-xs text-gray-400">Units available for distribution</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-4 mb-4">
        <form method="GET" class="flex gap-3">
            <select name="category" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">All Categories</option>
                @foreach(['seeds' => 'Seeds', 'fertilizer' => 'Fertilizer', 'pesticide' => 'Pesticide', 'equipment' => 'Equipment', 'tools' => 'Tools', 'others' => 'Others'] as $value => $label)
                    <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary-dark transition">
                Filter
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Category</th>
                    <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Item Name</th>
                    <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Unit</th>
                    <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Total Stock</th>
                    <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Released</th>
                    <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Remaining</th>
                    <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($stocks as $stock)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 capitalize">{{ $stock->category }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $stock->item_name }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $stock->unit }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ number_format($stock->total_stock, 0) }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ number_format($stock->released_stock, 0) }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-100">{{ number_format($stock->remaining_stock, 0) }}</td>
                    <td class="px-4 py-3">
                        @php
                            $statusColors = [
                                'available'    => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                                'medium'       => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                                'low'          => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',
                                'out_of_stock' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$stock->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                            {{ ucfirst(str_replace('_', ' ', $stock->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex items-center gap-2">
                        <button onclick="openReleaseModal({{ $stock->id }}, '{{ $stock->item_name }}', {{ $stock->remaining_stock }}, '{{ $stock->unit }}')"
                                class="text-primary hover:underline text-xs font-medium">
                            Release
                        </button>
                        <form method="POST" action="{{ route('stocks.destroy', $stock) }}"
                              onsubmit="return confirm('Delete this stock item?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">No stock items yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-border-soft dark:border-gray-700">
            {{ $stocks->links() }}
        </div>
    </div>

    {{-- Add Stock Modal --}}
    <div id="addStockModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">Add Stock</h3>
                <button onclick="document.getElementById('addStockModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fa-solid fa-x"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('stocks.store') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Item Name</label>
                        <input type="text" name="item_name" required
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Category</label>
                        <select name="category" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Select Category</option>
                            <option value="seeds">Seeds</option>
                            <option value="fertilizer">Fertilizer</option>
                            <option value="pesticide">Pesticide</option>
                            <option value="equipment">Equipment</option>
                            <option value="tools">Tools</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Quantity</label>
                            <input type="number" step="0.01" name="quantity" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Unit</label>
                            <input type="text" name="unit" value="kg" required
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Notes</label>
                        <textarea name="notes" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="flex-1 bg-primary hover:bg-primary-dark text-white font-semibold py-2 rounded-md transition text-sm">
                        Add Stock
                    </button>
                    <button type="button" onclick="document.getElementById('addStockModal').classList.add('hidden')"
                            class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 font-medium py-2 rounded-md transition text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Release Stock Modal --}}
    <div id="releaseStockModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">Release Stock</h3>
                <button onclick="document.getElementById('releaseStockModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fa-solid fa-x"></i>
                </button>
            </div>
            <form id="releaseForm" method="POST">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Item</label>
                        <input type="text" id="releaseItemName" readonly
                               class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-gray-100 rounded-md px-3 py-2 text-sm"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Recipient Name</label>
                        <input type="text" name="recipient" required
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Quantity <span id="releaseMax" class="text-gray-400"></span></label>
                        <input type="number" step="0.01" name="quantity" id="releaseQty" required
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Notes</label>
                        <textarea name="notes" rows="2"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="flex-1 bg-primary hover:bg-primary-dark text-white font-semibold py-2 rounded-md transition text-sm">
                        Release Stock
                    </button>
                    <button type="button" onclick="document.getElementById('releaseStockModal').classList.add('hidden')"
                            class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 font-medium py-2 rounded-md transition text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openReleaseModal(id, name, remaining, unit) {
        document.getElementById('releaseItemName').value = name;
        document.getElementById('releaseMax').textContent = '(Max: ' + remaining + ' ' + unit + ')';
        document.getElementById('releaseQty').max = remaining;
        document.getElementById('releaseForm').action = '/stocks/' + id + '/release';
        document.getElementById('releaseStockModal').classList.remove('hidden');
    }
    </script>

@endif

@endsection