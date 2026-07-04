@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('messages.index') }}"
           class="text-gray-400 hover:text-gray-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        @if($user->photo)
            <img src="{{ asset('storage/' . $user->photo) }}"
                 class="w-9 h-9 rounded-full object-cover"/>
        @else
            <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center">
                <span class="text-white text-sm font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
        @endif
        <div>
            <h2 class="text-base font-bold text-gray-800">{{ $user->name }}</h2>
            <p class="text-xs text-gray-400">{{ $user->barangayAccount?->barangay?->name ?? 'Barangay Rep' }}</p>
        </div>
    </div>
</div>

{{-- Messages --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="h-96 overflow-y-auto p-5 space-y-3" id="messageContainer">
        @forelse($messages as $message)
        @php $isMine = $message->sender_id === Auth::id(); @endphp
        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-xs lg:max-w-md">
                <div class="px-4 py-2.5 rounded-2xl text-sm
                    {{ $isMine
                        ? 'bg-primary text-white rounded-br-sm'
                        : 'bg-gray-100 text-gray-800 rounded-bl-sm' }}">
                    {{ $message->message }}
                </div>
                <p class="text-xs text-gray-400 mt-1 {{ $isMine ? 'text-right' : 'text-left' }}">
                    {{ $message->created_at->format('M d, h:i A') }}
                </p>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-400 py-8">
            <i class="fa-solid fa-comments text-2xl mb-2"></i>
            <p class="text-sm">No messages yet. Start the conversation!</p>
        </div>
        @endforelse
    </div>

    {{-- Message Input --}}
    <div class="border-t border-gray-100 p-4">
        <form method="POST" action="{{ route('messages.send') }}" class="flex gap-3">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            <input type="text" name="message" required
                   placeholder="Type your message..."
                   autocomplete="off"
                   class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
            <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-full text-sm font-medium transition">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script>
    // Auto scroll to bottom
    const container = document.getElementById('messageContainer');
    container.scrollTop = container.scrollHeight;
</script>

@endsection