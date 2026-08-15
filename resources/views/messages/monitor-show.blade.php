@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('messages.monitor') }}"
       class="text-gray-400 hover:text-gray-600 transition">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="flex items-center -space-x-2 flex-shrink-0">
        <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center border-2 border-white dark:border-gray-800 z-10">
            <span class="text-white text-xs font-bold">{{ strtoupper(substr($rep->name, 0, 1)) }}</span>
        </div>
        <div class="w-9 h-9 rounded-full bg-accent flex items-center justify-center border-2 border-white dark:border-gray-800">
            <span class="text-primary-dark text-xs font-bold">{{ strtoupper(substr($coordinator->name, 0, 1)) }}</span>
        </div>
    </div>
    <div>
        <h2 class="text-base font-bold text-gray-800 dark:text-gray-100">
            {{ $rep->name }} <span class="text-gray-400 font-normal">↔</span> {{ $coordinator->name }}
        </h2>
        <p class="text-xs text-gray-400">Read-only — oversight view</p>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded border border-gray-300 dark:border-gray-700 overflow-hidden">
    <div class="h-[28rem] overflow-y-auto p-5 space-y-3" id="messageContainer">
        @forelse($messages as $message)
        @php $isRep = $message->sender_id === $rep->id; @endphp
        <div class="flex {{ $isRep ? 'justify-start' : 'justify-end' }}">
            <div class="max-w-xs lg:max-w-md">
                @if($message->message)
                <div class="px-4 py-2.5 rounded text-sm border
                    {{ $isRep
                        ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 border-gray-200 dark:border-gray-600'
                        : 'bg-accent/10 text-gray-800 dark:text-gray-100 border-accent/30' }}">
                    {{ $message->message }}
                </div>
                @endif

                @if($message->attachment_path)
                <div class="mt-1.5">
                    @if($message->attachment_type === 'image')
                        <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $message->attachment_path) }}"
                                 class="rounded border border-gray-300 max-w-full max-h-48 object-cover">
                        </a>
                    @else
                        <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank"
                           class="flex items-center gap-2 px-3 py-2 rounded border bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-xs hover:opacity-80 transition">
                            <i class="fa-solid {{ $message->attachment_type === 'pdf' ? 'fa-file-pdf' : 'fa-file-lines' }}"></i>
                            <span class="truncate max-w-[160px]">{{ $message->attachment_name }}</span>
                            <i class="fa-solid fa-download ml-auto"></i>
                        </a>
                    @endif
                </div>
                @endif

                <p class="text-xs text-gray-400 mt-1 {{ $isRep ? 'text-left' : 'text-right' }}">
                    {{ $message->sender->name }} · {{ $message->created_at->format('M d, h:i A') }}
                </p>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-400 py-8">
            <i class="fa-solid fa-comments text-2xl mb-2"></i>
            <p class="text-sm">No messages in this thread yet.</p>
        </div>
        @endforelse
    </div>

    <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900 text-center">
        <p class="text-xs text-gray-400">
            <i class="fa-solid fa-lock mr-1"></i>This is a read-only oversight view. You cannot reply here.
        </p>
    </div>
</div>

<script>
    const container = document.getElementById('messageContainer');
    container.scrollTop = container.scrollHeight;
</script>

@endsection