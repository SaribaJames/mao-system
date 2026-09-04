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

@if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm rounded-md p-3 mb-4">
        {{ session('error') }}
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
                {{-- Showing current programs here stops a rep endorsing someone
                     who is already enrolled in the program they're about to pick. --}}
                <option value="{{ $farmer->id }}">
                    {{ $farmer->first_name }} {{ $farmer->surname }}@if($farmer->activePrograms->count() > 0) — already in: {{ $farmer->activePrograms->pluck('name')->join(', ') }}@endif
                </option>
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
        <div class="col-span-3">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">
                Reason for Endorsement <span class="text-red-500">*</span>
            </label>
            <textarea name="notes" rows="3" required minlength="30" maxlength="1000"
                      placeholder="Explain why this farmer qualifies — e.g. actively cultivating 1.5 ha of riceland in Brgy. Maninila, has attended previous MAO trainings, and was affected by the recent typhoon."
                      class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">{{ old('notes') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">
                The coordinator uses this to decide. Mention the farmer's situation, landholding, or need — at least 30 characters.
            </p>
            @error('notes')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
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
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Barangay</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Program</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Reason / Feedback</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Status</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Submitted</th>
                <th class="text-right px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($endorsements as $endorsement)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $endorsement->farmer->first_name }} {{ $endorsement->farmer->surname }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $endorsement->farmer->barangay?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $endorsement->program?->name ?? 'Deleted program' }}</td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 max-w-xs">
                    <span class="block truncate" title="{{ $endorsement->notes }}">{{ $endorsement->notes ?? '—' }}</span>
                    @if($endorsement->status === 'rejected' && $endorsement->rejection_reason)
                        <span class="mt-1 block text-xs text-red-600 dark:text-red-400">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <strong>Rejected:</strong> {{ $endorsement->rejection_reason }}
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $endorsement->status === 'approved' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : ($endorsement->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-accent/10 text-accent border border-accent/30') }}">
                        {{ ucfirst($endorsement->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $endorsement->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3 text-right">
                    @if($endorsement->status === 'approved')
                        {{-- Farmer is already enrolled; this record is the reason why. --}}
                        <span class="text-gray-300 dark:text-gray-600" title="Approved endorsements cannot be removed">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                    @else
                        <form method="POST" action="{{ route('endorsements.destroy', $endorsement) }}" class="inline"
                              onsubmit="return confirm('{{ $endorsement->status === 'pending' ? 'Withdraw this endorsement before the coordinator reviews it?' : 'Remove this rejected endorsement from your list?' }}');">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-gray-400 hover:text-red-600 transition"
                                title="{{ $endorsement->status === 'pending' ? 'Withdraw endorsement' : 'Remove rejected endorsement' }}">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No endorsements yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-border-soft dark:border-gray-700">
        {{ $endorsements->links() }}
    </div>
</div>

@endsection