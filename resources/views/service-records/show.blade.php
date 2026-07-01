@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Service Record Details</h2>
        <p class="text-gray-500 text-sm mt-1 font-mono">{{ $serviceRecord->service_number }}</p>
    </div>
    <a href="{{ route('service-records.index') }}"
       class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-md transition">
        ← Back
    </a>
</div>

<div class="grid grid-cols-3 gap-4">

    {{-- Service Info --}}
    <div class="col-span-2 space-y-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Service Information</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Service Type</p>
                    <p class="font-medium text-gray-800">{{ $serviceRecord->service_type_label }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Service Date</p>
                    <p class="font-medium text-gray-800">{{ $serviceRecord->service_date->format('F d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Items Provided</p>
                    <p class="font-medium text-gray-800">{{ $serviceRecord->items_provided ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Quantity</p>
                    <p class="font-medium text-gray-800">
                        {{ $serviceRecord->quantity ? $serviceRecord->quantity . ' ' . $serviceRecord->quantity_unit : '—' }}
                    </p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-1">Description</p>
                    <p class="font-medium text-gray-800">{{ $serviceRecord->description ?? '—' }}</p>
                </div>
                @if($serviceRecord->remarks)
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-1">Remarks</p>
                    <p class="font-medium text-gray-800">{{ $serviceRecord->remarks }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Farmer Info --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Farmer Information</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Name</p>
                    <p class="font-medium text-gray-800">
                        {{ $serviceRecord->farmer->first_name }} {{ $serviceRecord->farmer->surname }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Barangay</p>
                    <p class="font-medium text-gray-800">{{ $serviceRecord->farmer->barangay?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Contact</p>
                    <p class="font-medium text-gray-800">{{ $serviceRecord->farmer->mobile_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Reference No.</p>
                    <p class="font-medium text-gray-800 font-mono text-xs">
                        {{ $serviceRecord->farmer->reference_number }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Side Info --}}
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-200">Status</h3>
            <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $serviceRecord->status_color }}">
                {{ ucfirst($serviceRecord->status) }}
            </span>
            <div class="mt-4 text-xs text-gray-400 space-y-1">
                <p>Processed by: {{ $serviceRecord->processedBy?->name ?? '—' }}</p>
                <p>Date: {{ $serviceRecord->service_date->format('M d, Y') }}</p>
            </div>
        </div>

        @if($serviceRecord->stock)
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-200">Stock Used</h3>
            <p class="text-sm font-medium text-gray-800">{{ $serviceRecord->stock->item_name }}</p>