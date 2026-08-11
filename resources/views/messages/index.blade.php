@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Messages</h2>
    <p class="text-gray-500 text-sm mt-1">Conversations with Barangay Representatives</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    @forelse($conversations as $user)
    <a href="{{ route('messages.conversation', $user) }}"
       class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
        {{-- Avatar --}}
        @if($user->photo)
            <img src="{{ asset('storage/' . $user->photo) }}"
                 class="w-10 h-10 rounded-full object-cover flex-shrink-0"/>
        @else
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                <span class="text-white text-sm font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
        @endif

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
            <p class="text-xs text-gray-400">
                {{ $user->barangayAccount?->barangay?->name ?? 'Barangay Rep' }}
            </p>
        </div>

        {{-- Unread badge --}}
        @if($user->unread_count > 0)
        <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
            {{ $user->unread_count }}
        </span>
        @endif

        <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
    </a>
    @empty
    <div class="px-5 py-12 text-center text-gray-400">
        <i class="fa-solid fa-comments text-3xl mb-3"></i>
        <p class="text-sm">No conversations yet.</p>
        <p class="text-xs mt-1">Barangay Reps can message you from their dashboard.</p>
    </div>
    @endforelse
</div>

@endsection