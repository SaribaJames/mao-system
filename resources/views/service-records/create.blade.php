@extends('layouts.app')

@section('content')

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-gray-900">New Service Record</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Record an agricultural service rendered to a farmer</p>
        </div>
        <a href="{{ route('service-records.index') }}"
            class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium px-4 py-2 rounded-md transition">
            ← Back
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-sm rounded-md p-3 mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('service-records.store') }}">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5 mb-4">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Service Details</h3>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Farmer <span
                                class="text-red-500">*</span></label>
                        <select name="farmer_id" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Select Farmer</option>
                            @foreach($farmers as $farmer)
                                <option value="{{ $farmer->id }}" {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                    {{ $farmer->first_name }} {{ $farmer->surname }} — {{ $farmer->barangay?->name }}
                                </option>
                            @endforeach
                        </select>
                        {{-- Quick notice --}}
                        <div class="mt-2 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-md p-3 flex items-start gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-yellow-500 text-xs mt-0.5"></i>
                            <div>
                                <p class="text-xs text-yellow-700 dark:text-yellow-300 font-medium">Farmer not in the list?</p>
                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-0.5">
                                    The farmer must be registered in the RSBSA first before recording a service.
                                    <a href="{{ route('farmers.create') }}"
                                        class="text-primary font-semibold hover:underline ml-1">
                                        Register Farmer Now →
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Service Type <span
                                class="text-red-500">*</span></label>
                        <select name="service_type" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Select Type</option>
                            <option value="seed_distribution">Seed Distribution</option>
                            <option value="fertilizer_distribution">Fertilizer Distribution</option>
                            <option value="pesticide_distribution">Pesticide Distribution</option>
                            <option value="equipment_assistance">Equipment Assistance</option>
                            <option value="technical_assistance">Technical Assistance</option>
                            <option value="training_seminar">Training/Seminar</option>
                            <option value="farm_visit">Farm Visit</option>
                            <option value="crop_insurance_assistance">Crop Insurance Assistance</option>
                            <option value="financial_assistance">Financial Assistance</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Description</label>
                    <textarea name="description" rows="2"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Stock Item (if applicable)</label>
                        <select name="stock_id"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Select Stock Item</option>
                            @foreach($stocks as $stock)
                                <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>
                                    {{ $stock->item_name }} ({{ number_format($stock->remaining_stock, 0) }} {{ $stock->unit }}
                                    available)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Items Provided</label>
                        <input type="text" name="items_provided" value="{{ old('items_provided') }}"
                            placeholder="e.g. Rice Seeds, Organic Fertilizer"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Quantity</label>
                        <input type="number" step="0.01" name="quantity" value="{{ old('quantity') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Unit</label>
                        <input type="text" name="quantity_unit" value="{{ old('quantity_unit', 'kg') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="completed">Completed</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Remarks</label>
                    <textarea name="remarks" rows="2"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">{{ old('remarks') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white font-semibold px-6 py-2.5 rounded-md transition text-sm">
                    Save Service Record
                </button>
                <a href="{{ route('service-records.index') }}"
                    class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium px-6 py-2.5 rounded-md transition text-sm">
                    Cancel
                </a>
            </div>

    </form>
@endsection