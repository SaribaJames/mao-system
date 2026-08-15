@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Messages</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Municipal Agriculture Office — Guinobatan</p>
</div>

@if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="mb-4">
    <div class="relative">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" id="conversationSearch" placeholder="Search conversations..."
               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden">
    @forelse($threads as $thread)
    <a href="{{ route('messages.rep-conversation', $thread['user']) }}"
       data-search="{{ strtolower($thread['user']->name . ' ' . $thread['label']) }}"
       class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-50 dark:border-gray-700 last:border-0">
        {{-- Avatar --}}
        @if($thread['user']->photo)
            <img src="{{ asset('storage/' . $thread['user']->photo) }}"
                 class="w-10 h-10 rounded-full object-cover flex-shrink-0"/>
        @else
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                <span class="text-white text-sm font-bold">{{ strtoupper(substr($thread['user']->name, 0, 1)) }}</span>
            </div>
        @endif

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $thread['user']->name }}</p>
            <p class="text-xs text-gray-400">{{ $thread['label'] }}</p>
        </div>

        {{-- Unread badge --}}
        @if($thread['unread'] > 0)
        <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
            {{ $thread['unread'] }}
        </span>
        @endif

        <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
    </a>
    @empty
    <div class="px-5 py-12 text-center text-gray-400">
        <i class="fa-solid fa-comments text-3xl mb-3"></i>
        <p class="text-sm">No conversations yet.</p>
        <p class="text-xs mt-1">MAO Admin will appear here once you send a message.</p>
    </div>
    @endforelse
</div>

<script>
    const conversationSearchInput = document.getElementById('conversationSearch');
    if (conversationSearchInput) {
        conversationSearchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('[data-search]').forEach(function (row) {
                row.style.display = row.dataset.search.includes(query) ? '' : 'none';
            });
        });
    }
</script>

@endsection