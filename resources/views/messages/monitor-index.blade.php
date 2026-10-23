@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Coordinator Conversations</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Read-only oversight of Barangay Rep ↔ Program Coordinator threads</p>
</div>

<div class="mb-4">
    <div class="relative">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" id="conversationSearch" placeholder="Search by rep or coordinator name..."
               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden">
    @forelse($threads as $thread)
    <a href="{{ route('messages.monitor.show', [$thread['rep'], $thread['coordinator']]) }}"
       data-search="{{ strtolower($thread['rep']->name . ' ' . $thread['coordinator']->name) }}"
       class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-50 dark:border-gray-700 last:border-0">
        <div class="flex items-center -space-x-2 flex-shrink-0">
            <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center border-2 border-white dark:border-gray-800 z-10">
                <span class="text-white text-xs font-bold">{{ strtoupper(substr($thread['rep']->name, 0, 1)) }}</span>
            </div>
            <div class="w-9 h-9 rounded-full bg-accent flex items-center justify-center border-2 border-white dark:border-gray-800">
                <span class="text-primary-dark text-xs font-bold">{{ strtoupper(substr($thread['coordinator']->name, 0, 1)) }}</span>
            </div>
        </div>

        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                {{ $thread['rep']->name }}
                <span class="text-gray-400 font-normal mx-1"><i class="fa-solid fa-arrow-right-arrow-left text-xs"></i></span>
                {{ $thread['coordinator']->name }}
            </p>
            <p class="text-xs text-gray-400">
                {{ $thread['rep']->barangayAccount?->barangay?->name ?? 'Barangay Rep' }} — Program Coordinator
            </p>
        </div>

        <span class="text-xs px-2 py-1 rounded-full bg-primary-light dark:bg-gray-700 text-primary font-medium">
            <i class="fa-solid fa-eye mr-1"></i>View
        </span>
    </a>
    @empty
    <div class="px-5 py-12 text-center text-gray-400">
        <i class="fa-solid fa-people-arrows text-3xl mb-3"></i>
        <p class="text-sm">No coordinator conversations yet.</p>
        <p class="text-xs mt-1">These appear once a Barangay Rep @mentions a coordinator and receives a reply.</p>
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