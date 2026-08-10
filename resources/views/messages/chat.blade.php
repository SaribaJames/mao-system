@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('dashboard') }}"
       class="text-gray-400 hover:text-gray-600 transition">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div>
        <h2 class="text-base font-bold text-gray-800">MAO Support</h2>
        <p class="text-xs text-gray-400">Municipal Agriculture Office — Guinobatan</p>
    </div>
</div>

{{-- Messages --}}
<div class="bg-white rounded border border-gray-300 overflow-hidden">
    <div class="h-96 overflow-y-auto p-5 space-y-3" id="messageContainer">
        @forelse($messages as $message)
        @php $isMine = $message->sender_id === Auth::id(); @endphp
        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-xs lg:max-w-md">
                <div class="px-4 py-2.5 rounded text-sm border
                    {{ $isMine
                        ? 'bg-primary text-white border-primary-dark'
                        : 'bg-gray-100 text-gray-800 border-gray-200' }}">
                    {{ $message->message }}
                </div>
                <p class="text-xs text-gray-400 mt-1 {{ $isMine ? 'text-right' : 'text-left' }}">
                    {{ $isMine ? 'You' : $message->sender->name }}
                    · {{ $message->created_at->format('M d, h:i A') }}
                </p>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-400 py-8">
            <i class="fa-solid fa-comments text-2xl mb-2"></i>
            <p class="text-sm">No messages yet.</p>
            <p class="text-xs mt-1">Send a message to MAO staff for assistance!</p>
        </div>
        @endforelse
    </div>

    {{-- Message Input --}}
    <div class="border-t border-gray-200 p-4">
        <form method="POST" action="{{ route('messages.send') }}" class="flex gap-3">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $admin->id }}">
            <input type="text" name="message" required
                   placeholder="Type your message to MAO..."
                   autocomplete="off"
                   class="flex-1 border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
            <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded text-sm font-medium transition">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script>
    const container = document.getElementById('messageContainer');
    container.scrollTop = container.scrollHeight;
</script>

@endsection