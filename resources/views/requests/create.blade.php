@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">New Request</h2>
    <p class="text-gray-500 text-sm mt-1">Submit a farmer request</p>
</div>

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded p-3 mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('requests.store') }}">
@csrf

<div class="bg-white rounded border border-gray-300 p-5 mb-4">
    <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Request Details</h3>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Farmer <span class="text-red-500">*</span></label>
            <select name="farmer_id" required
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select Farmer</option>
                @foreach($farmers as $farmer)
                    <option value="{{ $farmer->id }}" {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}>
                        {{ $farmer->first_name }} {{ $farmer->surname }} — {{ $farmer->barangay?->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Request Type <span class="text-red-500">*</span></label>
            <select name="request_type" required
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select Type</option>
                <option value="seeds_distribution">Seeds Distribution</option>
                <option value="fertilizer_request">Fertilizer Request</option>
                <option value="pesticide_request">Pesticide Request</option>
                <option value="equipment_request">Equipment Request</option>
                <option value="training_seminar">Training/Seminar</option>
                <option value="technical_assistance">Technical Assistance</option>
                <option value="financial_assistance">Financial Assistance</option>
                <option value="others">Others</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Item/Service <span class="text-red-500">*</span></label>
            <input type="text" name="item_service" value="{{ old('item_service') }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Stock Item (if applicable)</label>
            <select name="stock_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select Stock Item</option>
                @foreach($stocks as $stock)
                    <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>
                        {{ $stock->item_name }} ({{ number_format($stock->remaining_stock, 0) }} {{ $stock->unit }} available)
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Quantity</label>
            <input type="number" step="0.01" name="quantity" value="{{ old('quantity') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Unit</label>
            <input type="text" name="quantity_unit" value="{{ old('quantity_unit', 'kg') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Purpose</label>
        <textarea name="purpose" rows="3"
                  class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">{{ old('purpose') }}</textarea>
    </div>
</div>

<div class="flex items-center gap-3">
    <button type="submit"
            class="bg-primary hover:bg-primary-dark text-white font-semibold px-6 py-2.5 rounded transition text-sm">
        Submit Request
    </button>
    <a href="{{ route('requests.index') }}"
       class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-6 py-2.5 rounded transition text-sm">
        Cancel
    </a>
</div>

</form>
@endsection