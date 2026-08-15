@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">My Mentions</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Messages from Barangay Reps that specifically asked for you</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden">
    @forelse($mentions as $message)
    <a href="{{ route('messages.conversation', $message->sender) }}"
       class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-50 dark:border-gray-700 last:border-0">
        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
            <span class="text-white text-sm font-bold">{{ strtoupper(substr($message->sender->name, 0, 1)) }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $message->sender->name }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full bg-accent/10 text-accent font-medium">
                    <i class="fa-solid fa-at mr-0.5"></i>mentioned you
                </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">{{ $message->message }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $message->created_at->format('M d, Y · h:i A') }}</p>
        </div>
    </a>
    @empty
    <div class="px-5 py-12 text-center text-gray-400">
        <i class="fa-solid fa-at text-3xl mb-3"></i>
        <p class="text-sm">No mentions yet.</p>
        <p class="text-xs mt-1">Barangay Reps can @mention you in their messages to ask program-specific questions.</p>
    </div>
    @endforelse
</div>

<div class="px-1 py-3">
    {{ $mentions->links() }}
</div>

@endsection