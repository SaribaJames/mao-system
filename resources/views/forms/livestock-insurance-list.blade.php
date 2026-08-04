@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Livestock Mortality Insurance Applications</h2>
        <p class="text-gray-500 text-sm mt-1">LIV-UPI-01 applications on file</p>
    </div>
    <a href="{{ route('livestock-insurance.create') }}"
       class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
        <i class="fa-solid fa-plus"></i> New Application
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Farmer</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Cover Type</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Animal Type</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Total Heads</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Date Filed</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($applications as $app)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $app->farmer->first_name }} {{ $app->farmer->surname }}</p>
                    <p class="text-xs text-gray-400">{{ $app->farmer->barangay?->name }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ ucfirst(str_replace('_', ' ', $app->cover_type)) }}</td>
                <td class="px-4 py-3 text-gray-600">{{ ucfirst($app->animal_type) }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $app->total_heads ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $app->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('livestock-insurance.print', $app) }}" target="_blank"
                       class="text-primary hover:underline text-xs font-medium">
                        <i class="fa-solid fa-print mr-1"></i> Print
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">No applications yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $applications->links() }}
    </div>
</div>

@endsection