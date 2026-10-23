@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">My View Requests</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Status of your requests to view a farmer's full profile</p>
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
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Farmer</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Requested</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Status</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Access Window</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($requests as $req)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <td class="px-4 py-3 text-gray-800 dark:text-gray-100 font-medium">
                    {{ $req->farmer?->first_name }} {{ $req->farmer?->surname }}
                </td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $req->created_at->format('M d, Y g:i A') }}</td>
                <td class="px-4 py-3">
                    @php
                        $badge = match($req->status) {
                            'approved' => $req->isActive() ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500',
                            'denied' => 'bg-red-100 text-red-700',
                            default => 'bg-accent/10 text-accent border border-accent/30',
                        };
                        $label = $req->status === 'approved' && !$req->isActive() ? 'Expired' : ucfirst($req->status);
                    @endphp
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $badge }}">{{ $label }}</span>
                </td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs">
                    @if($req->status === 'approved' && $req->expires_at)
                        {{ $req->isActive() ? 'Expires ' . $req->expires_at->format('g:i:s A') : 'Expired ' . $req->expires_at->diffForHumans() }}
                    @else
                        —
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($req->status === 'approved' && $req->isActive() && $req->farmer)
                        <a href="{{ route('farmers.show', $req->farmer) }}" class="text-primary hover:underline text-xs font-medium">View Full Profile</a>
                    @elseif($req->farmer)
                        <a href="{{ route('farmers.show', $req->farmer) }}" class="text-gray-400 hover:underline text-xs font-medium">Open Farmer</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-400">You haven't requested to view any farmer's full profile yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-border-soft dark:border-gray-700">
        {{ $requests->links() }}
    </div>
</div>

@endsection
