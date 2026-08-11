@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Requests Management</h2>
        <p class="text-gray-500 text-sm mt-1">Track and process farmer requests</p>
    </div>
    <a href="{{ route('requests.create') }}"
       class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
        <i class="fa-solid fa-plus"></i> New Request
    </a>
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 mb-1">Total Requests</p>
        <p class="text-2xl font-bold text-gray-800">{{ $total }}</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 mb-1">Pending</p>
        <p class="text-2xl font-bold text-yellow-500">{{ $pending }}</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 mb-1">Approved</p>
        <p class="text-2xl font-bold text-primary">{{ $approved }}</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 mb-1">Completed</p>
        <p class="text-2xl font-bold text-blue-500">{{ $completed }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by Request ID or Farmer Name..."
               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        <select name="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">All Status</option>
            <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
            <option value="approved"  {{ request('status') == 'approved'  ? 'selected' : '' }}>Approved</option>
            <option value="rejected"  {{ request('status') == 'rejected'  ? 'selected' : '' }}>Rejected</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
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
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Request ID</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Farmer Name</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Request Type</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Item/Service</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Quantity</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Date</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($requests as $req)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $req->request_number }}</td>
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $req->farmer->first_name }} {{ $req->farmer->surname }}</p>
                    <p class="text-xs text-gray-400">{{ $req->farmer->barangay?->name }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $req->request_type_label }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $req->item_service }}</td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $req->quantity ? $req->quantity . ' ' . $req->quantity_unit : '—' }}
                </td>
                <td class="px-4 py-3">
                    @php
                        $colors = [
                            'pending'   => 'bg-yellow-100 text-yellow-700',
                            'approved'  => 'bg-blue-100 text-blue-700',
                            'rejected'  => 'bg-red-100 text-red-700',
                            'completed' => 'bg-green-100 text-green-700',
                        ];
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$req->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($req->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $req->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('requests.show', $req) }}"
                       class="text-primary hover:underline text-xs font-medium">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400">No requests found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $requests->links() }}
    </div>
</div>

@endsection