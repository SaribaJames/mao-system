@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Request Details</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1 font-mono">{{ $request->request_number }}</p>
    </div>
    <a href="{{ route('requests.index') }}"
       class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium px-4 py-2 rounded-md transition">
        ← Back
    </a>
</div>

<div class="grid grid-cols-3 gap-4">

    {{-- Request Info --}}
    <div class="col-span-2 space-y-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Request Information</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Request Type</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $request->request_type_label }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Item/Service</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $request->item_service }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Quantity</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">
                        {{ $request->quantity ? $request->quantity . ' ' . $request->quantity_unit : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Date Submitted</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">
                        {{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->format('M d, Y') : '—' }}
                    </p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Purpose</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $request->purpose ?? '—' }}</p>
                </div>
                @if($request->remarks)
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Remarks</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $request->remarks }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Farmer Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Farmer Information</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Name</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">
                        {{ $request->farmer->first_name }} {{ $request->farmer->surname }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Barangay</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $request->farmer->barangay?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Contact</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $request->farmer->mobile_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Reference No.</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100 font-mono text-xs">
                        {{ $request->farmer->reference_number }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Status & Actions --}}
    <div class="space-y-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Status</h3>
            @php
                $colors = [
                    'pending'   => 'bg-accent/10 text-accent border border-accent/30',
                    'approved'  => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                    'rejected'  => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                    'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                ];
            @endphp
            <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $colors[$request->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Update Status</h3>
            <form method="POST" action="{{ route('requests.status', $request) }}">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Action</label>
                    <select name="status" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                        <option value="completed">Complete</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Remarks</label>
                    <textarea name="remarks" rows="2"
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
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