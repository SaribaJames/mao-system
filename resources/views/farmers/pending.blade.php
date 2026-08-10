@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Pending Farmer Registrations</h2>
        <p class="text-gray-500 text-sm mt-1">Review and approve/reject farmer registrations submitted by barangay reps</p>
    </div>
    <a href="{{ route('farmers.index') }}"
       class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded transition">
        ← Back to Farmers
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded border border-gray-300 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Farmer</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Barangay</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Submitted By</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Date Submitted</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($farmers as $farmer)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $farmer->first_name }} {{ $farmer->surname }}</p>
                    <p class="text-xs text-gray-400">{{ $farmer->reference_number }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $farmer->barangay?->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $farmer->registeredBy?->name }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $farmer->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('farmers.print', $farmer) }}" target="_blank"
                           class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-xs font-medium px-3 py-1.5 rounded transition">
                            <i class="fa-solid fa-print mr-1"></i> Print
                        </a>
                        <form method="POST" action="{{ route('farmers.approve', $farmer) }}">
                            @csrf
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition">
                                Approve
                            </button>
                        </form>
                        <button onclick="document.getElementById('reject-modal-{{ $farmer->id }}').classList.remove('hidden')"
                                class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded transition">
                            Reject
                        </button>
                    </div>

                    {{-- Reject Modal --}}
                    <div id="reject-modal-{{ $farmer->id }}" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                        <div class="bg-white rounded border border-gray-300 w-full max-w-md p-6">
                            <h3 class="font-semibold text-gray-800 mb-3">Reject Registration — {{ $farmer->first_name }} {{ $farmer->surname }}</h3>
                            <form method="POST" action="{{ route('farmers.reject', $farmer) }}">
                                @csrf
                                <label class="block text-xs text-gray-500 mb-1">Reason for rejection <span class="text-red-500">*</span></label>
                                <textarea name="reason" rows="3" required
                                          placeholder="Explain why this registration is being rejected..."
                                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                                <div class="flex gap-2">
                                    <button type="submit"
                                            class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded transition">
                                        Confirm Reject
                                    </button>
                                    <button type="button"
                                            onclick="document.getElementById('reject-modal-{{ $farmer->id }}').classList.add('hidden')"
                                            class="flex-1 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded transition">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-400">No pending registrations.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-200">
        {{ $farmers->links() }}
    </div>
</div>

@endsection