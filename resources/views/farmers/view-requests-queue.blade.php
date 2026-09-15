@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Farmer View Requests</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Staff/Brgy Rep requests to view a farmer's full profile — approving grants 10 minutes of access</p>
    </div>
    <a href="{{ route('farmers.index') }}"
       class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium px-4 py-2 rounded-md transition">
        ← Back to Farmers
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Requested By</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Farmer</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Requested</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($requests as $req)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $req->user?->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ $req->user?->role?->name ? str_replace('_', ' ', $req->user->role->name) : '' }}</p>
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                    {{ $req->farmer?->first_name }} {{ $req->farmer?->surname }}
                    <span class="text-xs text-gray-400">({{ $req->farmer?->barangay?->name ?? '—' }})</span>
                </td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $req->created_at->format('M d, Y g:i A') }}</td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('farmers.view-requests.approve', $req) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-green-600 hover:underline text-xs font-medium">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('farmers.view-requests.deny', $req) }}" class="inline ml-2">
                        @csrf
                        <button type="submit" class="text-red-500 hover:underline text-xs font-medium">Deny</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-gray-400">No pending view requests.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-border-soft dark:border-gray-700">
        {{ $requests->links() }}
    </div>
</div>

@endsection
