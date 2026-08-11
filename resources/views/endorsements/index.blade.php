@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Program Endorsements</h2>
    <p class="text-gray-500 text-sm mt-1">Endorse your farmers for program enrollment</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 mb-6">
    <h3 class="text-base font-semibold text-gray-800 mb-4">Endorse a Farmer</h3>
    <form method="POST" action="{{ route('endorsements.store') }}" class="grid grid-cols-3 gap-3">
        @csrf
        <div>
            <label class="block text-xs text-gray-500 mb-1">Farmer</label>
            <select name="farmer_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select farmer...</option>
                @foreach($farmers as $farmer)
                <option value="{{ $farmer->id }}">{{ $farmer->first_name }} {{ $farmer->surname }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Program</label>
            <select name="program_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select program...</option>
                @foreach($programs as $program)
                <option value="{{ $program->id }}">{{ $program->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Notes (optional)</label>
            <input type="text" name="notes" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="col-span-3">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                Submit Endorsement
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Farmer</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Program</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Notes</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Submitted</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($endorsements as $endorsement)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">{{ $endorsement->farmer->first_name }} {{ $endorsement->farmer->surname }}</td>
                <td class="px-4 py-3">{{ $endorsement->program->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $endorsement->notes ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $endorsement->status === 'approved' ? 'bg-green-100 text-green-700' : ($endorsement->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($endorsement->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $endorsement->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No endorsements yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $endorsements->links() }}
    </div>
</div>

@endsection