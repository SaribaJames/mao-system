@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Service Records</h2>
        <p class="text-gray-500 text-sm mt-1">Record and track agricultural services rendered to farmers</p>
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
    <a href="{{ route('service-records.create') }}"
       class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded flex items-center gap-2 transition">
        <i class="fa-solid fa-plus"></i> New Service Record
    </a>
    @endif
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded p-5 border border-gray-300">
        <p class="text-xs text-gray-500 mb-1">Total Records</p>
        <p class="text-2xl font-bold text-gray-800">{{ $total }}</p>
    </div>
    <div class="bg-white rounded p-5 border border-gray-300">
        <p class="text-xs text-gray-500 mb-1">Completed</p>
        <p class="text-2xl font-bold text-primary">{{ $completed }}</p>
    </div>
    <div class="bg-white rounded p-5 border border-gray-300">
        <p class="text-xs text-gray-500 mb-1">Ongoing</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $ongoing }}</p>
    </div>
    <div class="bg-white rounded p-5 border border-gray-300">
        <p class="text-xs text-gray-500 mb-1">This Month</p>
        <p class="text-2xl font-bold text-blue-600">{{ $thisMonth }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded border border-gray-300 p-4 mb-4">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by Service ID or Farmer Name..."
               class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        <select name="service_type" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">All Types</option>
            <option value="seed_distribution"         {{ request('service_type') == 'seed_distribution'         ? 'selected' : '' }}>Seed Distribution</option>
            <option value="fertilizer_distribution"   {{ request('service_type') == 'fertilizer_distribution'   ? 'selected' : '' }}>Fertilizer Distribution</option>
            <option value="pesticide_distribution"    {{ request('service_type') == 'pesticide_distribution'    ? 'selected' : '' }}>Pesticide Distribution</option>
            <option value="equipment_assistance"      {{ request('service_type') == 'equipment_assistance'      ? 'selected' : '' }}>Equipment Assistance</option>
            <option value="technical_assistance"      {{ request('service_type') == 'technical_assistance'      ? 'selected' : '' }}>Technical Assistance</option>
            <option value="training_seminar"          {{ request('service_type') == 'training_seminar'          ? 'selected' : '' }}>Training/Seminar</option>
            <option value="farm_visit"                {{ request('service_type') == 'farm_visit'                ? 'selected' : '' }}>Farm Visit</option>
            <option value="crop_insurance_assistance" {{ request('service_type') == 'crop_insurance_assistance' ? 'selected' : '' }}>Crop Insurance Assistance</option>
            <option value="financial_assistance"      {{ request('service_type') == 'financial_assistance'      ? 'selected' : '' }}>Financial Assistance</option>
            <option value="others"                    {{ request('service_type') == 'others'                    ? 'selected' : '' }}>Others</option>
        </select>
        <select name="status" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">All Status</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="ongoing"   {{ request('status') == 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded text-sm hover:bg-primary-dark transition">
            Search
        </button>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded border border-gray-300 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Service ID</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Farmer</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Service Type</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Items Provided</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Date</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Processed By</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($records as $record)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $record->service_number }}</td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $record->farmer->first_name }} {{ $record->farmer->surname }}</p>
                    <p class="text-xs text-gray-400">{{ $record->farmer->barangay?->name }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $record->service_type_label }}</td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $record->items_provided ?? '—' }}
                    @if($record->quantity)
                        <span class="text-xs text-gray-400">({{ $record->quantity }} {{ $record->quantity_unit }})</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $record->service_date->format('M d, Y') }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ $record->processedBy?->name ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded border text-xs font-medium {{ $record->status_color }}">
                        {{ ucfirst($record->status) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('service-records.show', $record) }}"
                       class="text-primary hover:underline text-xs font-medium">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400">No service records yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-200">
        {{ $records->links() }}
    </div>
</div>

@endsection