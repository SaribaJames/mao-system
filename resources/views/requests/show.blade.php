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
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Program</p>
                    @if($request->program)
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $request->program->name }}
                            @if($request->program->assignedUser)
                                <span class="block text-xs font-normal text-gray-400">
                                    Coordinator: {{ $request->program->assignedUser->name }}
                                </span>
                            @endif
                        </p>
                    @else
                        <p class="font-medium text-gray-400">Not part of a program</p>
                    @endif
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
                        {{ $request->farmer?->first_name }} {{ $request->farmer?->surname ?? 'Deleted farmer' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Barangay</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $request->farmer?->barangay?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Contact</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $request->farmer?->mobile_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Reference No.</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100 font-mono text-xs">
                        {{ $request->farmer?->reference_number ?? '—' }}
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

        {{-- Update Status. A request tagged to a program belongs to that
             program's coordinator (or an admin) — other staff can see it but
             cannot decide it. --}}
        @php $canProcess = \App\Http\Controllers\RequestController::canProcess($request, Auth::user()); @endphp

        @if(!$canProcess && (Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff'))
            <div class="bg-gray-50 dark:bg-gray-900/40 rounded-lg border border-border-soft dark:border-gray-700 p-4 text-center">
                <i class="fa-solid fa-lock text-gray-400 mb-1"></i>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    This request belongs to <strong>{{ $request->program?->name }}</strong>.
                    @if($request->program?->assignedUser)
                        Only its coordinator ({{ $request->program->assignedUser->name }}) or an admin can act on it.
                    @else
                        It has no assigned coordinator yet — an admin can act on it.
                    @endif
                </p>
            </div>
        @endif

        @if($canProcess)
        @php
            // Only offer the moves that are actually legal from here, so a
            // pending request can't jump straight to Completed.
            $allowed = \App\Http\Controllers\RequestController::allowedTransitions($request->status);
            $labels = ['approved' => 'Approve', 'rejected' => 'Reject', 'completed' => 'Mark as Completed (releases stock)'];
        @endphp
        @if(count($allowed) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Update Status</h3>

            @if($request->status === 'approved' && $request->stock)
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 rounded-md p-2">
                    <i class="fa-solid fa-circle-info text-blue-500"></i>
                    Completing this will release <strong>{{ $request->quantity }} {{ $request->quantity_unit }}</strong>
                    of <strong>{{ $request->stock->item_name }}</strong> from inventory
                    ({{ $request->stock->remaining_stock }} {{ $request->stock->unit }} on hand).
                </p>
            @endif

            <form method="POST" action="{{ route('requests.status', $request) }}">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Action</label>
                    <select name="status" id="statusSelect" required onchange="toggleRemarksRequired()"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach($allowed as $option)
                            <option value="{{ $option }}">{{ $labels[$option] ?? ucfirst($option) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">
                        Remarks <span id="remarksRequired" class="text-red-500 hidden">* required when rejecting</span>
                    </label>
                    <textarea name="remarks" id="remarksBox" rows="2" maxlength="500"
                              placeholder="Reason or note for the barangay representative"
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">{{ old('remarks') }}</textarea>
                    @error('remarks')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2 rounded-md transition text-sm">
                    Update
                </button>
            </form>
        </div>
        <script>
            function toggleRemarksRequired() {
                var isReject = document.getElementById('statusSelect').value === 'rejected';
                document.getElementById('remarksBox').required = isReject;
                document.getElementById('remarksBox').minLength = isReject ? 10 : 0;
                document.getElementById('remarksRequired').classList.toggle('hidden', !isReject);
            }
            document.addEventListener('DOMContentLoaded', toggleRemarksRequired);
        </script>
        @else
        <div class="bg-gray-50 dark:bg-gray-900/40 rounded-lg border border-border-soft dark:border-gray-700 p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                This request is <strong>{{ $request->status }}</strong> and can no longer be changed.
            </p>
        </div>
        @endif
        @endif

        {{-- Withdraw (own pending request only) --}}
        @if(Auth::user()->isBarangayUser() && $request->status === 'pending' && $request->submitted_by === Auth::id())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5 mt-4">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 mb-1">Withdraw Request</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                You can withdraw this while it is still pending. Once MAO acts on it, it becomes part of the record.
            </p>
            <form method="POST" action="{{ route('requests.destroy', $request) }}"
                  onsubmit="return confirm('Withdraw request {{ $request->request_number }}? This cannot be undone.');">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full border border-red-300 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 font-semibold py-2 rounded-md transition text-sm">
                    <i class="fa-solid fa-trash text-xs"></i> Withdraw Request
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

@endsection