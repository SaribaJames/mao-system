@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Livestock Mortality Insurance Applications</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">LIV-UPI-01 applications on file</p>
    </div>
    <a href="{{ route('livestock-insurance.create') }}"
       class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
        <i class="fa-solid fa-plus"></i> New Application
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Farmer</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Cover Type</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Animal Type</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Total Heads</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Date Filed</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($applications as $app)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $app->farmer->first_name }} {{ $app->farmer->surname }}</p>
                    <p class="text-xs text-gray-400">{{ $app->farmer->barangay?->name }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $app->cover_type)) }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ ucfirst($app->animal_type) }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $app->total_heads ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $app->created_at->format('M d, Y') }}</td>
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
    <div class="px-4 py-3 border-t border-border-soft dark:border-gray-700">
        {{ $applications->links() }}
    </div>
</div>

@endsection