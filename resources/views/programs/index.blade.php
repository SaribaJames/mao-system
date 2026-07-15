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
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 mb-1">Total Programs</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalPrograms }}</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 mb-1">Active Enrollments</p>
        <p class="text-2xl font-bold text-primary">{{ $totalActiveEnrollments }}</p>
    </div>
</div>

{{-- Program Cards --}}
<div class="grid grid-cols-3 gap-4">
    @foreach($programs as $program)
    <a href="{{ route('programs.show', $program) }}"
       class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 hover:shadow-md transition block">
        <div class="flex items-start justify-between mb-3">
            <i class="fa-solid fa-seedling text-primary text-lg"></i>
            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $program->status_color }}">
                {{ ucfirst($program->status) }}
            </span>
        </div>
        <p class="font-semibold text-gray-800 mb-1">{{ $program->name }}</p>
        <p class="text-xs text-gray-400 mb-3">Coordinator: {{ $program->coordinator_name }}</p>
        <p class="text-xs text-gray-500">
            <i class="fa-solid fa-users w-4 text-center"></i>
            {{ $program->active_enrollments_count }} active farmer(s)
        </p>
    </a>
    @endforeach
</div>

@endsection
