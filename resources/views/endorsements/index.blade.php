@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h2 class="text-3xl font-bold tracking-tight text-gray-900">Program Endorsements</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Endorse your farmers for program enrollment</p>
</div>

@if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5 mb-6">
    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4">Endorse a Farmer</h3>
    <form method="POST" action="{{ route('endorsements.store') }}" class="grid grid-cols-3 gap-3">
        @csrf
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Farmer</label>
            <select name="farmer_id" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select farmer...</option>
                @foreach($farmers as $farmer)
                <option value="{{ $farmer->id }}">{{ $farmer->first_name }} {{ $farmer->surname }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Program</label>
            <select name="program_id" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select program...</option>
                @foreach($programs as $program)
                <option value="{{ $program->id }}">{{ $program->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Notes (optional)</label>
            <input type="text" name="notes" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="col-span-3">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                Submit Endorsement
            </button>
        </div>
    </form>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Farmer</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Program</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Notes</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Status</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Submitted</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($endorsements as $endorsement)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $endorsement->farmer->first_name }} {{ $endorsement->farmer->surname }}</td>
                <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $endorsement->program->name }}</td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $endorsement->notes ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $endorsement->status === 'approved' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : ($endorsement->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-accent/10 text-accent border border-accent/30') }}">
                        {{ ucfirst($endorsement->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $endorsement->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No endorsements yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-border-soft dark:border-gray-700">
        {{ $endorsements->links() }}
    </div>
</div>

@endsection