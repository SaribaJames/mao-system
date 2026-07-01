@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Request Details</h2>
        <p class="text-gray-500 text-sm mt-1 font-mono">{{ $request->request_number }}</p>
    </div>
    <a href="{{ route('requests.index') }}"
       class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-md transition">
        ← Back
    </a>
</div>

<div class="grid grid-cols-3 gap-4">

    {{-- Request Info --}}
    <div class="col-span-2 space-y-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Request Information</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Request Type</p>
                    <p class="font-medium text-gray-800">{{ $request->request_type_label }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Item/Service</p>
                    <p class="font-medium text-gray-800">{{ $request->item_service }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Quantity</p>
                    <p class="font-medium text-gray-800">
                        {{ $request->quantity ? $request->quantity . ' ' . $request->quantity_unit : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Date Submitted</p>
                    <p class="font-medium text-gray-800">
                        {{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->format('M d, Y') : '—' }}
                    </p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-1">Purpose</p>
                    <p class="font-medium text-gray-800">{{ $request->purpose ?? '—' }}</p>
                </div>
                @if($request->remarks)
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-1">Remarks</p>
                    <p class="font-medium text-gray-800">{{ $request->remarks }}</p>
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
                        {{ $request->farmer->first_name }} {{ $request->farmer->surname }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Barangay</p>
                    <p class="font-medium text-gray-800">{{ $request->farmer->barangay?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Contact</p>
                    <p class="font-medium text-gray-800">{{ $request->farmer->mobile_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Reference No.</p>
                    <p class="font-medium text-gray-800 font-mono text-xs">
                        {{ $request->farmer->reference_number }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Status & Actions --}}
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Status</h3>
            @php
                $colors = [
                    'pending'   => 'bg-yellow-100 text-yellow-700',
                    'approved'  => 'bg-blue-100 text-blue-700',
                    'rejected'  => 'bg-red-100 text-red-700',
                    'completed' => 'bg-green-100 text-green-700',
                ];
            @endphp
            <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $colors[$request->status] ?? 'bg-gray-100 text-gray-600' }}">
                {{ ucfirst($request->status) }}
            </span>

            @if($request->processedBy)
            <div class="mt-3 text-xs text-gray-400">
                <p>Processed by: {{ $request->processedBy->name }}</p>
                <p>{{ $request->processed_at ? \Carbon\Carbon::parse($request->processed_at)->format('M d, Y h:i A') : '' }}</p>
            </div>
            @endif
        </div>

        {{-- Update Status (Admin & Staff only) --}}
        @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
        @if($request->status === 'pending')
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Update Status</h3>
            <form method="POST" action="{{ route('requests.status', $request) }}">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Action</label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                        <option value="completed">Complete</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
                    <textarea name="remarks" rows="2"
                              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                </div>
                <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2 rounded-md transition text-sm">
                    Update
                </button>
            </form>
        </div>
        @endif
        @endif
    </div>
</div>

@endsection