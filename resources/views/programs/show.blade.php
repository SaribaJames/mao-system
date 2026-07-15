@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('programs.index') }}" class="text-xs text-gray-400 hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to Programs
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $program->name }}</h2>
        <p class="text-gray-500 text-sm mt-1">Coordinator: {{ $program->coordinator_name }}</p>
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
    <button onclick="document.getElementById('enroll-modal').classList.remove('hidden')"
       class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
        <i class="fa-solid fa-plus"></i> Enroll Farmer
    </button>
    @endif
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Filter --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by Farmer Name..."
               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        <select name="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">All Status</option>
            <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Active</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="dropped"   {{ request('status') == 'dropped'   ? 'selected' : '' }}>Dropped</option>
        </select>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary-dark transition">
            Search
        </button>
    </form>
</div>

{{-- Enrollments Table --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Farmer</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Enrollment Date</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Processed By</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Remarks</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Action</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($enrollments as $enrollment)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-800">{{ $enrollment->farmer->first_name }} {{ $enrollment->farmer->surname }}</p>
                    <p class="text-xs text-gray-400">{{ $enrollment->farmer->barangay?->name }}</p>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $enrollment->enrollment_date->format('M d, Y') }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ $enrollment->processedBy?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $enrollment->remarks ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $enrollment->status_color }}">
                        {{ ucfirst($enrollment->status) }}
                    </span>
                </td>
                @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('program-enrollments.status', $enrollment) }}" class="flex items-center gap-2">
                        @csrf
                        <select name="status" onchange="this.form.submit()"
                                class="border border-gray-300 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="active"    {{ $enrollment->status == 'active'    ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ $enrollment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="dropped"   {{ $enrollment->status == 'dropped'   ? 'selected' : '' }}>Dropped</option>
                        </select>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">No farmers enrolled yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $enrollments->links() }}
    </div>
</div>

{{-- Enroll Modal --}}
<div id="enroll-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Enroll Farmer — {{ $program->name }}</h3>
            <button onclick="document.getElementById('enroll-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('programs.enroll', $program) }}">
            @csrf
            <label class="block text-xs text-gray-500 mb-1">Farmer</label>
            <select name="farmer_id" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select farmer...</option>
                @foreach($farmers as $farmer)
                <option value="{{ $farmer->id }}">{{ $farmer->first_name }} {{ $farmer->surname }}</option>
                @endforeach
            </select>
            <label class="block text-xs text-gray-500 mb-1">Remarks (optional)</label>
            <textarea name="remarks" rows="2"
                      class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
            <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                Enroll
            </button>
        </form>
    </div>
</div>

@endsection
