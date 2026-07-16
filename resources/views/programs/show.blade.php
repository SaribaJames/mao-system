@extends('layouts.app')

@section('content')

@php
    // Admins can always see program data. The assigned staff member must
    // unlock with their PIN first — no enrollment data shows until then.
    $canViewData = Auth::user()->isAdmin() || ($isAssignedUser && $isUnlocked);
@endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('programs.index') }}" class="text-xs text-gray-400 hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to Programs
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $program->name }}</h2>
        <p class="text-gray-500 text-sm mt-1">
            Assigned Personnel: {{ $program->assignedUser?->name ?? 'Unassigned' }}
        </p>
    </div>

    @if($isAssignedUser && $isUnlocked)
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

@if(!$canViewData)

    {{-- LOCKED STATE — this is ALL the assigned staff sees until PIN is entered.
         No filter, no table, no farmer data of any kind. --}}
    <div class="max-w-sm mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mt-10 text-center">
        <div class="w-14 h-14 rounded-full bg-primary-light flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-lock text-primary text-xl"></i>
        </div>
        <h3 class="font-semibold text-gray-800 mb-1">Enter Your PIN</h3>
        <p class="text-xs text-gray-400 mb-4">This program's data is hidden until you unlock it</p>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 text-xs rounded-md p-2 mb-3">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('programs.unlock', $program) }}" id="pinForm">
            @csrf
            <div class="flex items-center justify-center gap-2 mb-5" id="pinBoxes">
                @for ($i = 0; $i < 6; $i++)
                <input type="password" maxlength="1" inputmode="numeric" pattern="[0-9]"
                       class="pin-box w-10 h-12 text-center text-lg font-bold border-2 border-gray-200 rounded-lg
                              focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary-light transition"
                       {{ $i === 0 ? 'autofocus' : '' }} />
                @endfor
            </div>
            <input type="hidden" name="pin" id="pinHidden">
            <button type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                Unlock
            </button>
        </form>
    </div>

    <script>
    (function () {
        const boxes = document.querySelectorAll('.pin-box');
        const hidden = document.getElementById('pinHidden');
        const form = document.getElementById('pinForm');

        function updateHidden() {
            hidden.value = Array.from(boxes).map(b => b.value).join('');
        }

        boxes.forEach((box, i) => {
            box.addEventListener('input', () => {
                box.value = box.value.replace(/[^0-9]/g, '');
                if (box.value && i < boxes.length - 1) {
                    boxes[i + 1].focus();
                }
                updateHidden();
            });

            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !box.value && i > 0) {
                    boxes[i - 1].focus();
                }
            });

            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const digits = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').split('');
                digits.forEach((d, idx) => {
                    if (boxes[i + idx]) boxes[i + idx].value = d;
                });
                const next = boxes[Math.min(i + digits.length, boxes.length - 1)];
                next.focus();
                updateHidden();
            });
        });

        form.addEventListener('submit', updateHidden);
    })();
    </script>

@else

    {{-- UNLOCKED (or admin) — full data visible --}}

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
                    @if($isAssignedUser && $isUnlocked)
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
                    @if($isAssignedUser && $isUnlocked)
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

    @if($isAssignedUser && $isUnlocked)
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
    @endif

@endif

@endsection