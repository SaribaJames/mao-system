@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Farmers Registry</h2>
        <p class="text-gray-500 text-sm mt-1">Manage registered farmers and their profiles</p>
    </div>
    <a href="{{ route('farmers.create') }}"
       class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
        <i class="fa-solid fa-plus"></i> Register Farmer
    </a>
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 mb-1">Total Farmers</p>
        <p class="text-2xl font-bold text-gray-800">{{ $farmers->total() }}</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 mb-1">Active Farmers</p>
        <p class="text-2xl font-bold text-primary">{{ $farmers->where('status','active')->count() }}</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 mb-1">Total Farm Area</p>
        <p class="text-2xl font-bold text-blue-500">{{ number_format($farmers->sum('land_area_hectares'), 2) }} ha</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 mb-1">New This Month</p>
        <p class="text-2xl font-bold text-yellow-500">{{ $farmers->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
    </div>
</div>

{{-- Search --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name or ID..."
               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        <select name="barangay" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">All Barangays</option>
            @foreach(\App\Models\Barangay::orderBy('name')->get() as $barangay)
                <option value="{{ $barangay->id }}" {{ request('barangay') == $barangay->id ? 'selected' : '' }}>
                    {{ $barangay->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary-dark transition">
            Search
        </button>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Reference No.</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Name</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Barangay</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Contact</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Livelihood</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($farmers as $farmer)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $farmer->reference_number }}</td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $farmer->first_name }} {{ $farmer->surname }}</p>
                    <p class="text-xs text-gray-400">{{ $farmer->sex }} · {{ \Carbon\Carbon::parse($farmer->date_of_birth)->age }} yrs</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $farmer->barangay?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $farmer->mobile_number ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="capitalize text-gray-600">{{ str_replace('_', ' ', $farmer->main_livelihood ?? '—') }}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $farmer->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($farmer->status) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('farmers.show', $farmer) }}" class="text-primary hover:underline text-xs font-medium">View</a>
                    @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
                    <a href="{{ route('farmers.edit', $farmer) }}" class="text-blue-500 hover:underline text-xs font-medium ml-2">Edit</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">No farmers registered yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $farmers->links() }}
    </div>
</div>

@endsection