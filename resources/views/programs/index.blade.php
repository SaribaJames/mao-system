@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">MAO Programs</h2>
        <p class="text-gray-500 text-sm mt-1">Municipal Agriculture Office programs and enrolled farmers</p>
    </div>
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded p-5 border border-gray-300">
        <p class="text-xs text-gray-500 mb-1">Total Programs</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalPrograms }}</p>
    </div>
    <div class="bg-white rounded p-5 border border-gray-300">
        <p class="text-xs text-gray-500 mb-1">Active Enrollments</p>
        <p class="text-2xl font-bold text-primary">{{ $totalActiveEnrollments }}</p>
    </div>
</div>

{{-- Program Cards --}}
<div class="grid grid-cols-3 gap-4">
    @foreach($programs as $program)
        @php
            $isMine = $program->isManagedBy(Auth::user());
            $canOpen = Auth::user()->isAdmin() || $isMine;
        @endphp

        @if($canOpen)
        <a href="{{ route('programs.show', $program) }}"
           class="bg-white rounded p-5 transition block
                  {{ $isMine ? 'border-2 border-primary ring-1 ring-primary-light' : 'border border-gray-300' }}">
            <div class="flex items-start justify-between mb-3">
                <i class="fa-solid fa-seedling text-primary text-lg"></i>
                <div class="flex items-center gap-1">
                    @if($isMine)
                    <span class="px-2 py-1 rounded text-xs font-medium bg-primary text-white">
                        <i class="fa-solid fa-star text-[10px]"></i> Your Program
                    </span>
                    @endif
                    <span class="px-2 py-1 rounded border text-xs font-medium {{ $program->status_color }}">
                        {{ ucfirst($program->status) }}
                    </span>
                </div>
            </div>
            <p class="font-semibold text-gray-800 mb-1">{{ $program->name }}</p>
            <p class="text-xs text-gray-400 mb-3">
                <i class="fa-solid fa-user-shield w-4 text-center"></i>
                Assigned: {{ $program->assignedUser?->name ?? 'Unassigned' }}
            </p>
            <p class="text-xs text-gray-500">
                <i class="fa-solid fa-users w-4 text-center"></i>
                {{ $program->active_enrollments_count }} active farmer(s)
            </p>
        </a>
        @else
        <div class="bg-gray-50 rounded border border-gray-300 p-5 opacity-70 cursor-not-allowed relative"
             title="You are not assigned to this program">
            <div class="flex items-start justify-between mb-3">
                <i class="fa-solid fa-seedling text-gray-300 text-lg"></i>
                <span class="px-2 py-1 rounded border border-gray-200 text-xs font-medium bg-gray-100 text-gray-500">
                    <i class="fa-solid fa-lock text-[10px]"></i> Restricted
                </span>
            </div>
            <p class="font-semibold text-gray-500 mb-1">{{ $program->name }}</p>
            <p class="text-xs text-gray-400 mb-3">
                <i class="fa-solid fa-user-shield w-4 text-center"></i>
                Assigned: {{ $program->assignedUser?->name ?? 'Unassigned' }}
            </p>
            <p class="text-xs text-gray-400">Not assigned to you</p>
        </div>
        @endif
    @endforeach
</div>

@endsection