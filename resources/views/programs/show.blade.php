@extends('layouts.app')

@section('content')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>

    @php
        $canViewData = Auth::user()->isAdmin() || ($isAssignedUser && $isUnlocked);
    @endphp

    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('programs.index') }}" class="text-xs text-gray-400 hover:text-primary">
                <i class="fa-solid fa-arrow-left"></i> Back to Programs
            </a>
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 mt-1">{{ $program->name }}</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
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

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm rounded-md p-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(!$canViewData)

        <div class="max-w-sm mx-auto bg-white rounded-2xl shadow-lg border border-border-soft p-6 mt-10 text-center">
            <div class="w-14 h-14 rounded-full bg-primary-light flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-lock text-primary text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">Enter Your PIN</h3>
            <p class="text-xs text-gray-400 mb-4">This program's data is hidden until you unlock it</p>

            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-xs rounded-md p-2 mb-3">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('programs.unlock', $program) }}" id="pinForm">
                @csrf
                <div class="flex items-center justify-center gap-2 mb-5" id="pinBoxes">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="password" data-no-toggle maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="pin-box w-10 h-12 text-center text-lg font-bold border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg
                                                      focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary-light transition" {{ $i === 0 ? 'autofocus' : '' }} />
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

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-4 mb-4">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Farmer Name..."
                    class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                <select name="status"
                    class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                </select>
                <button type="submit"
                    class="bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary-dark transition">
                    Search
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden mb-6">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Farmer</th>
                        <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Enrollment Date</th>
                        <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Processed By</th>
                        <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Remarks</th>
                        <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Status</th>
                        @if($isAssignedUser && $isUnlocked)
                            <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $enrollment->farmer->first_name }}
                                    {{ $enrollment->farmer->surname }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $enrollment->farmer->barangay?->name }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $enrollment->enrollment_date?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 text-xs">{{ $enrollment->processedBy?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $enrollment->remarks ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $enrollment->status_color }}">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                            @if($isAssignedUser && $isUnlocked)
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('program-enrollments.status', $enrollment) }}"
                                        class="flex items-center gap-2">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()"
                                            class="border border-gray-300 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-primary">
                                            <option value="active" {{ $enrollment->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="completed" {{ $enrollment->status == 'completed' ? 'selected' : '' }}>Completed
                                            </option>
                                            <option value="dropped" {{ $enrollment->status == 'dropped' ? 'selected' : '' }}>Dropped
                                            </option>
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
            <div class="px-4 py-3 border-t border-border-soft dark:border-gray-700">
                {{ $enrollments->links() }}
            </div>
        </div>

        @if($program->activities->count() > 0)
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Target vs. Achieved</h3>
                    <canvas id="performanceChart" height="220"></canvas>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Budget by Year</h3>
                    <canvas id="budgetChart" height="220"></canvas>
                </div>
            </div>
        @endif

        @php
            $pendingEndorsements = \App\Models\ProgramEndorsement::where('program_id', $program->id)
                ->where('status', 'pending')
                ->with(['farmer.barangay', 'endorser'])
                ->get();
        @endphp

        @if($pendingEndorsements->count() > 0)
            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-5 mb-6">
                <h3 class="text-base font-semibold text-yellow-800 dark:text-yellow-300 mb-3">
                    <i class="fa-solid fa-clock mr-1"></i>
                    Pending Endorsements ({{ $pendingEndorsements->count() }})
                </h3>
                <div class="space-y-3">
                    @foreach($pendingEndorsements as $endorsement)
                        <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-md border border-yellow-100 dark:border-yellow-800 px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">
                                    {{ $endorsement->farmer->first_name }} {{ $endorsement->farmer->surname }}
                                    @if($endorsement->farmer->barangay)
                                        <span class="ml-1 text-xs font-normal px-2 py-0.5 rounded-full bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300">
                                            <i class="fa-solid fa-location-dot"></i>
                                            Brgy. {{ $endorsement->farmer->barangay->name }}
                                        </span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400">
                                    Endorsed by {{ $endorsement->endorser->name }}
                                    · {{ $endorsement->created_at->format('M d, Y') }}
                                </p>
                                @if($endorsement->notes)
                                    <div class="mt-2 bg-gray-50 dark:bg-gray-900/40 border-l-2 border-primary rounded-r px-3 py-2">
                                        <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold mb-0.5">Reason for Endorsement</p>
                                        <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">{{ $endorsement->notes }}</p>
                                    </div>
                                @endif
                            </div>
                            @if($isAssignedUser && $isUnlocked)
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <form method="POST" action="{{ route('endorsements.approve', $endorsement) }}">
                                        @csrf
                                        <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-md transition">
                                            Approve
                                        </button>
                                    </form>
                                    <button type="button"
                                        onclick="document.getElementById('reject-{{ $endorsement->id }}').classList.toggle('hidden')"
                                        class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-md transition">
                                        Reject
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if($isAssignedUser && $isUnlocked)
                            {{-- Hidden until Reject is clicked. A reason is required so the
                                 barangay rep learns what was lacking instead of guessing. --}}
                            <div id="reject-{{ $endorsement->id }}" class="hidden bg-white dark:bg-gray-800 rounded-md border border-red-200 dark:border-red-800 px-4 py-3 -mt-2">
                                <form method="POST" action="{{ route('endorsements.reject', $endorsement) }}">
                                    @csrf
                                    <label class="block text-xs font-semibold text-red-700 dark:text-red-300 mb-1">
                                        Reason for rejecting {{ $endorsement->farmer->first_name }} {{ $endorsement->farmer->surname }} <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="rejection_reason" rows="2" required minlength="10" maxlength="500"
                                              placeholder="e.g. Farmer is already enrolled in a similar program this cropping season, or landholding is outside the program's coverage area."
                                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-red-400"></textarea>
                                    @error('rejection_reason')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                    <div class="flex items-center gap-2 mt-2">
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-md transition">
                                            Confirm Rejection
                                        </button>
                                        <button type="button"
                                            onclick="document.getElementById('reject-{{ $endorsement->id }}').classList.add('hidden')"
                                            class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        @if($program->id === 7)
            @php
                $dispersalRecords = \App\Models\SwineDispersalRecord::where('program_id', $program->id)
                    ->with('farmer')
                    ->latest()
                    ->get();
                $allFarmers = \App\Models\Farmer::orderBy('surname')->get();
            @endphp

            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Swine Dispersal Records</h3>
                @if($isAssignedUser && $isUnlocked)
                    <button onclick="document.getElementById('add-dispersal-modal').classList.remove('hidden')"
                        class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
                        <i class="fa-solid fa-plus"></i> Add Record
                    </button>
                @endif
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-primary">{{ $dispersalRecords->sum('piglets_received') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Piglets Dispersed</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $dispersalRecords->sum('piglets_returned') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Piglets Returned</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600">{{ $dispersalRecords->where('status', 'waitlisted')->count() }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Waitlisted Farmers</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Farmer</th>
                            <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Piglets Received</th>
                            <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Date Received</th>
                            <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Piglets Returned</th>
                            <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Date Returned</th>
                            <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Status</th>
                            <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Notes</th>
                            @if($isAssignedUser && $isUnlocked)
                                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($dispersalRecords as $record)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $record->farmer->first_name }}
                                        {{ $record->farmer->surname }}</p>
                                    <p class="text-xs text-gray-400">{{ $record->farmer->barangay?->name }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $record->piglets_received }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $record->date_received?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $record->piglets_returned }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $record->date_returned?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium
                                        {{ $record->status === 'compliant' ? 'bg-green-100 text-green-700' :
                                ($record->status === 'received' ? 'bg-blue-100 text-blue-700' : 'bg-accent/10 text-accent border border-accent/30') }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $record->notes ?? '—' }}</td>
                                @if($isAssignedUser && $isUnlocked)
                                    <td class="px-4 py-3">
                                        <button
                                            onclick="document.getElementById('edit-dispersal-{{ $record->id }}').classList.remove('hidden')"
                                            class="text-primary hover:underline text-xs font-medium">Edit</button>
                                        <form method="POST" action="{{ route('programs.dispersal.destroy', $record) }}" class="inline"
                                            onsubmit="return confirm('Delete this record?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-red-500 hover:underline text-xs font-medium ml-2">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>

                            @if($isAssignedUser && $isUnlocked)
                                {{-- Edit Modal --}}
                                <tr class="hidden">
                                    <td>
                                        <div id="edit-dispersal-{{ $record->id }}"
                                            class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6">
                                                <div class="flex items-center justify-between mb-4">
                                                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Edit Record — {{ $record->farmer->first_name }}
                                                        {{ $record->farmer->surname }}</h3>
                                                    <button
                                                        onclick="document.getElementById('edit-dispersal-{{ $record->id }}').classList.add('hidden')"
                                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark"></i></button>
                                                </div>
                                                <form method="POST" action="{{ route('programs.dispersal.update', $record) }}">
                                                    @csrf @method('PUT')
                                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                                        <div>
                                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Piglets Received</label>
                                                            <input type="number" name="piglets_received"
                                                                value="{{ $record->piglets_received }}" min="0"
                                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Date Received</label>
                                                            <input type="date" name="date_received"
                                                                value="{{ $record->date_received?->format('Y-m-d') }}"
                                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Piglets Returned</label>
                                                            <input type="number" name="piglets_returned"
                                                                value="{{ $record->piglets_returned }}" min="0"
                                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Date Returned</label>
                                                            <input type="date" name="date_returned"
                                                                value="{{ $record->date_returned?->format('Y-m-d') }}"
                                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                                        </div>
                                                    </div>
                                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
                                                    <select name="status"
                                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">
                                                        <option value="waitlisted" {{ $record->status === 'waitlisted' ? 'selected' : '' }}>
                                                            Waitlisted</option>
                                                        <option value="received" {{ $record->status === 'received' ? 'selected' : '' }}>
                                                            Received</option>
                                                        <option value="compliant" {{ $record->status === 'compliant' ? 'selected' : '' }}>
                                                            Compliant</option>
                                                    </select>
                                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Notes</label>
                                                    <textarea name="notes" rows="2"
                                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-primary">{{ $record->notes }}</textarea>
                                                    <button type="submit"
                                                        class="w-full bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                                                        Save Changes
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-400">No dispersal records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($isAssignedUser && $isUnlocked)
                {{-- Add Dispersal Record Modal --}}
                <div id="add-dispersal-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">Add Dispersal Record</h3>
                            <button onclick="document.getElementById('add-dispersal-modal').classList.add('hidden')"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <form method="POST" action="{{ route('programs.dispersal.store', $program) }}">
                            @csrf
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Farmer</label>
                            <select name="farmer_id" required
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Select farmer...</option>
                                @foreach($allFarmers as $f)
                                    <option value="{{ $f->id }}">{{ $f->surname }}, {{ $f->first_name }} — {{ $f->barangay?->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Piglets Received</label>
                                    <input type="number" name="piglets_received" value="0" min="0"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Date Received</label>
                                    <input type="date" name="date_received"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Piglets Returned</label>
                                    <input type="number" name="piglets_returned" value="0" min="0"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Date Returned</label>
                                    <input type="date" name="date_returned"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                            </div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
                            <select name="status"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="waitlisted">Waitlisted</option>
                                <option value="received">Received</option>
                                <option value="compliant">Compliant</option>
                            </select>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Notes</label>
                            <textarea name="notes" rows="2"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                            <button type="submit"
                                class="w-full bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                                Add Record
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        @endif
        {{-- End Swine Dispersal only section --}}

        {{-- Program Achievements — bulletin board, also shown publicly on the landing page --}}
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Achievements Bulletin Board</h3>
            @if($isAssignedUser && $isUnlocked)
                <button onclick="document.getElementById('add-achievement-modal').classList.remove('hidden')"
                    class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
                    <i class="fa-solid fa-camera"></i> Post Photo
                </button>
            @endif
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6">
            @forelse($program->achievements as $achievement)
                @continue(!$achievement->photo_url)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden">
                <img src="{{ $achievement->photo_url }}" class="w-full h-40 object-cover cursor-pointer hover:opacity-90 transition"
                     onclick="openLightbox('{{ $achievement->photo_url }}', '{{ addslashes($achievement->caption ?: 'No caption') }}')">
                <div class="p-3">
                    <p class="text-sm text-gray-700 dark:text-gray-200">{{ $achievement->caption ?: 'No caption' }}</p>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-xs text-gray-400">{{ $achievement->created_at->format('M d, Y') }}</p>
                        @if($isAssignedUser && $isUnlocked)
                        <form method="POST" action="{{ route('programs.achievements.destroy', $achievement) }}"
                              onsubmit="return confirm('Remove this photo?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs font-medium">Remove</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-8 text-center text-gray-400 text-sm">
                No achievement photos posted yet.
            </div>
            @endforelse
        </div>

        @if($isAssignedUser && $isUnlocked)
        <div id="add-achievement-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Post Achievement Photo</h3>
                    <button onclick="document.getElementById('add-achievement-modal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('programs.achievements.store', $program) }}" enctype="multipart/form-data">
                    @csrf
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Photo</label>
                    <input type="file" name="photo" accept="image/*" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Caption (optional)</label>
                    <textarea name="caption" rows="3" placeholder="e.g. Piglet distribution day at Barangay Poblacion, August 2026"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                        Post Photo
                    </button>
                </form>
            </div>
        </div>
        @endif

        <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Activities & Budget Planning</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('programs.report', $program) }}" target="_blank"
                    class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-md flex items-center gap-2 transition">
                    <i class="fa-solid fa-file-pdf"></i> Generate Report
                </a>
                @if($isAssignedUser && $isUnlocked)
                    <button onclick="document.getElementById('add-activity-modal').classList.remove('hidden')"
                        class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
                        <i class="fa-solid fa-plus"></i> Add Activity
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-6">
            @forelse($program->activities as $activity)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                    <div class="flex items-start justify-between mb-3">
                        <h4 class="font-semibold text-gray-800">{{ $activity->name }}</h4>
                        @if($isAssignedUser && $isUnlocked)
                            <div class="flex items-center gap-3 text-xs">
                                <button
                                    onclick="document.getElementById('edit-activity-modal-{{ $activity->id }}').classList.remove('hidden')"
                                    class="text-primary hover:text-primary-dark font-medium">Edit</button>
                                <form method="POST" action="{{ route('programs.activities.destroy', $activity) }}"
                                    onsubmit="return confirm('Delete this activity? This cannot be undone.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Performance Achieved</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $activity->performance_achieved ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Challenges Encountered</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $activity->challenges_encountered ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Proposed Intervention</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $activity->proposed_intervention ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Target Performance</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $activity->target_performance ?: '—' }}</p>
                        </div>
                    </div>

                    @if($activity->stockUsages->count() > 0)
                        <div class="border-t border-border-soft pt-3 mb-3">
                            <p class="text-xs text-gray-400 mb-1">Stock Used</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($activity->stockUsages as $usage)
                                    <span class="px-2 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-md text-xs">
                                        {{ $usage->stock?->item_name ?? 'Deleted item' }} — {{ $usage->quantity_used }} {{ $usage->stock?->unit }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-border-soft pt-3 flex items-center justify-between">
                        <p class="text-xs text-gray-500">
                            <span class="font-medium text-gray-600">Expenditure:</span>
                            {{ $activity->expenditure_item ?: '—' }}
                        </p>
                        <div class="flex items-center gap-3 text-xs">
                            @forelse($activity->budget_breakdown ?? [] as $year => $amount)
                                <span class="px-2 py-1 bg-gray-50 dark:bg-gray-900 rounded-md text-gray-600">
                                    {{ $year }}: ₱{{ number_format($amount, 2) }}
                                </span>
                            @empty
                                <span class="text-gray-400">No budget set</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Distribution List: farmers who actually received resources during
                         this activity. Releasing here deducts stock once, and the printed
                         list is the coordinator's proof of distribution. --}}
                    @php
                        $actStocks = $activity->stockItems();
                        $actRecipients = $activity->recipients;
                        $actHeadcount = $actRecipients->groupBy(fn($r) => $r->barangay?->name ?? 'Unspecified')->map->count()->sortDesc();
                        $actAddedIds = $actRecipients->pluck('farmer_id')->filter()->all();
                        $actAvailable = $enrolledFarmers->reject(fn($f) => in_array($f->id, $actAddedIds));
                    @endphp

                    <div class="border-t border-border-soft dark:border-gray-700 pt-3 mt-3">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                                <i class="fa-solid fa-people-group text-primary"></i>
                                Distribution List
                                <span class="font-normal text-gray-400">({{ $actRecipients->count() }} farmer{{ $actRecipients->count() == 1 ? '' : 's' }})</span>
                            </p>
                            <div class="flex items-center gap-3 text-xs">
                                @if($actRecipients->count() > 0)
                                    <a href="{{ route('programs.activities.recipients.print', $activity) }}" target="_blank"
                                       class="text-primary hover:text-primary-dark font-medium">
                                        <i class="fa-solid fa-print"></i> Print List
                                    </a>
                                @endif
                                @if($isAssignedUser && $isUnlocked)
                                    <button onclick="document.getElementById('dist-{{ $activity->id }}').classList.toggle('hidden')"
                                            class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium">
                                        <i class="fa-solid fa-sliders"></i> Manage
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if($actHeadcount->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-2">
                                @foreach($actHeadcount as $brgy => $count)
                                    <span class="px-2 py-1 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-md text-xs">
                                        {{ $brgy }}: {{ $count }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if($actRecipients->count() > 0)
                            <div class="overflow-x-auto border border-border-soft dark:border-gray-700 rounded-md">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400">
                                        <tr>
                                            <th class="px-2 py-1.5 text-left font-medium">#</th>
                                            <th class="px-2 py-1.5 text-left font-medium">Name</th>
                                            <th class="px-2 py-1.5 text-left font-medium">Barangay</th>
                                            @foreach($actStocks as $st)
                                                <th class="px-2 py-1.5 text-left font-medium">{{ $st->item_name }}</th>
                                            @endforeach
                                            @if($isAssignedUser && $isUnlocked)
                                                <th class="px-2 py-1.5"></th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border-soft dark:divide-gray-700">
                                        @foreach($actRecipients as $i => $rcp)
                                            <tr class="text-gray-700 dark:text-gray-300">
                                                <td class="px-2 py-1.5 text-gray-400">{{ $i + 1 }}</td>
                                                <td class="px-2 py-1.5">{{ $rcp->farmer_name }}</td>
                                                <td class="px-2 py-1.5">{{ $rcp->barangay?->name ?? '—' }}</td>
                                                @foreach($actStocks as $st)
                                                    <td class="px-2 py-1.5">
                                                        {{ ($rcp->quantities[$st->id] ?? 0) > 0 ? ($rcp->quantities[$st->id] . ' ' . $st->unit) : '—' }}
                                                    </td>
                                                @endforeach
                                                @if($isAssignedUser && $isUnlocked)
                                                    <td class="px-2 py-1.5 text-right">
                                                        <form method="POST" action="{{ route('programs.recipients.destroy', $rcp) }}"
                                                              onsubmit="return confirm('Remove this farmer? The stock released to them will be returned to inventory.');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="text-red-500 hover:text-red-700">Remove</button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-xs text-gray-400">No farmers recorded yet.</p>
                        @endif

                        @if($isAssignedUser && $isUnlocked)
                        <div id="dist-{{ $activity->id }}" class="hidden mt-3 space-y-4 bg-gray-50 dark:bg-gray-900/40 rounded-md p-3">

                            {{-- Step 1: which items does this activity hand out --}}
                            <form method="POST" action="{{ route('programs.activities.items', $activity) }}">
                                @csrf
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">1. Items being distributed</p>
                                <div class="max-h-32 overflow-y-auto border border-border-soft dark:border-gray-700 rounded-md p-2 bg-white dark:bg-gray-800 space-y-1">
                                    @forelse($stocks as $stock)
                                        <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300">
                                            <input type="checkbox" name="stock_ids[]" value="{{ $stock->id }}"
                                                   {{ in_array($stock->id, $activity->stock_ids ?? []) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                                            {{ $stock->item_name }}
                                            <span class="text-gray-400">({{ $stock->remaining_stock }} {{ $stock->unit }} left)</span>
                                        </label>
                                    @empty
                                        <p class="text-xs text-gray-400">No stock items available.</p>
                                    @endforelse
                                </div>
                                <button type="submit" class="mt-2 text-xs bg-gray-600 hover:bg-gray-700 text-white font-semibold px-3 py-1.5 rounded-md">
                                    Save Items
                                </button>
                            </form>

                            @if($actStocks->count() > 0)
                                {{-- Step 2: tick enrolled farmers, same quantity each --}}
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">2. Add enrolled farmers</p>
                                    @if($actAvailable->count() > 0)
                                        <input type="text" onkeyup="filterList{{ $activity->id }}(this.value)"
                                               placeholder="Search farmer name..."
                                               class="w-full mb-1 px-2 py-1 text-xs border border-border-soft dark:border-gray-700 rounded-md dark:bg-gray-800 dark:text-gray-200">
                                        <div id="flist-{{ $activity->id }}"
                                             class="max-h-36 overflow-y-auto border border-border-soft dark:border-gray-700 rounded-md p-2 bg-white dark:bg-gray-800 space-y-1">
                                            @foreach($actAvailable as $f)
                                                <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300" data-name="{{ strtolower($f->full_name) }}">
                                                    <input type="checkbox" form="bulk-{{ $activity->id }}" name="farmer_ids[]" value="{{ $f->id }}"
                                                           class="rounded border-gray-300 text-primary focus:ring-primary">
                                                    {{ $f->full_name }}
                                                    <span class="text-gray-400">{{ $f->barangay?->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        <form method="POST" action="{{ route('programs.activities.recipients.bulk', $activity) }}" id="bulk-{{ $activity->id }}" class="mt-2">
                                            @csrf
                                            <p class="text-xs text-gray-500 mb-1">Quantity for each ticked farmer:</p>
                                            <div class="grid grid-cols-2 gap-2 mb-2">
                                                @foreach($actStocks as $st)
                                                    <div>
                                                        <label class="block text-xs text-gray-500 mb-0.5">{{ $st->item_name }} ({{ $st->unit }})</label>
                                                        <input type="number" step="0.01" min="0" name="quantities[{{ $st->id }}]"
                                                               class="w-full px-2 py-1 text-xs border border-border-soft dark:border-gray-700 rounded-md dark:bg-gray-800 dark:text-gray-200">
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="submit" class="text-xs bg-primary hover:bg-primary-dark text-white font-semibold px-3 py-1.5 rounded-md">
                                                Add Selected &amp; Release Stock
                                            </button>
                                        </form>
                                    @else
                                        <p class="text-xs text-gray-400">All enrolled farmers are already on this list.</p>
                                    @endif
                                </div>

                                {{-- Step 3: walk-in, not an enrolled farmer --}}
                                <form method="POST" action="{{ route('programs.activities.recipients.store', $activity) }}">
                                    @csrf
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">3. Or add a walk-in by hand</p>
                                    <div class="grid grid-cols-2 gap-2 mb-2">
                                        <input type="text" name="farmer_name" placeholder="Full name" required
                                               class="px-2 py-1 text-xs border border-border-soft dark:border-gray-700 rounded-md dark:bg-gray-800 dark:text-gray-200">
                                        <select name="barangay_id" class="px-2 py-1 text-xs border border-border-soft dark:border-gray-700 rounded-md dark:bg-gray-800 dark:text-gray-200">
                                            <option value="">— Barangay —</option>
                                            @foreach($barangays as $b)
                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="address" placeholder="Address (optional)"
                                               class="px-2 py-1 text-xs border border-border-soft dark:border-gray-700 rounded-md dark:bg-gray-800 dark:text-gray-200">
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="number" name="age" placeholder="Age" min="1" max="120"
                                                   class="px-2 py-1 text-xs border border-border-soft dark:border-gray-700 rounded-md dark:bg-gray-800 dark:text-gray-200">
                                            <select name="sex" class="px-2 py-1 text-xs border border-border-soft dark:border-gray-700 rounded-md dark:bg-gray-800 dark:text-gray-200">
                                                <option value="">Sex</option>
                                                <option value="M">M</option>
                                                <option value="F">F</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 mb-2">
                                        @foreach($actStocks as $st)
                                            <div>
                                                <label class="block text-xs text-gray-500 mb-0.5">{{ $st->item_name }} ({{ $st->unit }})</label>
                                                <input type="number" step="0.01" min="0" name="quantities[{{ $st->id }}]"
                                                       class="w-full px-2 py-1 text-xs border border-border-soft dark:border-gray-700 rounded-md dark:bg-gray-800 dark:text-gray-200">
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="submit" class="text-xs bg-gray-600 hover:bg-gray-700 text-white font-semibold px-3 py-1.5 rounded-md">
                                        Add Farmer &amp; Release Stock
                                    </button>
                                </form>

                                <script>
                                function filterList{{ $activity->id }}(term) {
                                    term = term.toLowerCase();
                                    document.querySelectorAll('#flist-{{ $activity->id }} label').forEach(function (el) {
                                        el.style.display = el.dataset.name.includes(term) ? 'flex' : 'none';
                                    });
                                }
                                </script>
                            @else
                                <p class="text-xs text-gray-400">Pick at least one item above, then you can add farmers.</p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                @if($isAssignedUser && $isUnlocked)
                    <div id="edit-activity-modal-{{ $activity->id }}"
                        class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">Edit Activity</h3>
                                <button
                                    onclick="document.getElementById('edit-activity-modal-{{ $activity->id }}').classList.add('hidden')"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <form method="POST" action="{{ route('programs.activities.update', $activity) }}">
                                @csrf @method('PUT')
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Activity / Project Name</label>
                                <input type="text" name="name" value="{{ $activity->name }}" required
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">

                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Performance Achieved</label>
                                <textarea name="performance_achieved" rows="2"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">{{ $activity->performance_achieved }}</textarea>

                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Challenges Encountered</label>
                                <textarea name="challenges_encountered" rows="2"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">{{ $activity->challenges_encountered }}</textarea>

                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Proposed Intervention</label>
                                <textarea name="proposed_intervention" rows="2"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">{{ $activity->proposed_intervention }}</textarea>

                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Target Performance</label>
                                <textarea name="target_performance" rows="2"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">{{ $activity->target_performance }}</textarea>

                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Target Value (for chart)</label>
                                        <input type="number" step="0.01" name="target_value" value="{{ $activity->target_value }}"
                                            placeholder="e.g. 1"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Achieved Value (for chart)</label>
                                        <input type="number" step="0.01" name="achieved_value" value="{{ $activity->achieved_value }}"
                                            placeholder="e.g. 1"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>
                                </div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Unit (optional, e.g. "trainings", "farmers")</label>
                                <input type="text" name="value_unit" value="{{ $activity->value_unit }}"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">

                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Item of Expenditure</label>
                                <input type="text" name="expenditure_item" value="{{ $activity->expenditure_item }}"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">

                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Budget Breakdown by Year</label>
                                <div id="budget-rows-edit-{{ $activity->id }}" class="space-y-2 mb-2">
                                    @forelse($activity->budget_breakdown ?? [] as $year => $amount)
                                        <div class="flex gap-2">
                                            <input type="text" name="budget_years[]" value="{{ $year }}" placeholder="Year"
                                                class="w-24 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                            <input type="text" name="budget_amounts[]" value="{{ $amount }}" placeholder="Amount"
                                                class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                        </div>
                                    @empty
                                        <div class="flex gap-2">
                                            <input type="text" name="budget_years[]" placeholder="Year"
                                                class="w-24 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                            <input type="text" name="budget_amounts[]" placeholder="Amount"
                                                class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                        </div>
                                    @endforelse
                                </div>
                                <button type="button" onclick="addBudgetRow('budget-rows-edit-{{ $activity->id }}')"
                                    class="text-xs text-primary hover:text-primary-dark font-medium mb-4">+ Add Year</button>

                                <button type="submit"
                                    class="w-full bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                                    Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-8 text-center text-gray-400 text-sm">
                    No activities recorded yet.
                </div>
            @endforelse
        </div>

        @if($isAssignedUser && $isUnlocked)
            <div id="enroll-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Enroll Farmer — {{ $program->name }}</h3>
                        <button onclick="document.getElementById('enroll-modal').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('programs.enroll', $program) }}">
                        @csrf
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Farmer</label>
                        <select name="farmer_id" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Select farmer...</option>
                            @foreach($farmers as $farmer)
                                <option value="{{ $farmer->id }}">{{ $farmer->first_name }} {{ $farmer->surname }}</option>
                            @endforeach
                        </select>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Remarks (optional)</label>
                        <textarea name="remarks" rows="2"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                        <button type="submit"
                            class="w-full bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                            Enroll
                        </button>
                    </form>
                </div>
            </div>

            <div id="add-activity-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-5xl p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Add Activity — {{ $program->name }}</h3>
                        <button onclick="document.getElementById('add-activity-modal').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('programs.activities.store', $program) }}">
                        @csrf
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Activity / Project Name</label>
                        <input type="text" name="name" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">

                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Performance Achieved</label>
                        <textarea name="performance_achieved" rows="2"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>

                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Challenges Encountered</label>
                        <textarea name="challenges_encountered" rows="2"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>

                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Proposed Intervention</label>
                        <textarea name="proposed_intervention" rows="2"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>

                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Target Performance</label>
                        <textarea name="target_performance" rows="2"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Target Value (for chart)</label>
                                <input type="number" step="0.01" name="target_value" value="{{ old('target_value') }}"
                                    placeholder="e.g. 1"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Achieved Value (for chart)</label>
                                <input type="number" step="0.01" name="achieved_value" value="{{ old('achieved_value') }}"
                                    placeholder="e.g. 1"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Unit (optional, e.g. "trainings", "farmers")</label>
                        <input type="text" name="value_unit" value="{{ old('value_unit') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">

                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Item of Expenditure</label>
                        <input type="text" name="expenditure_item"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-primary">

                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Budget Breakdown by Year</label>
                        <div id="budget-rows-add" class="space-y-2 mb-2">
                            <div class="flex gap-2">
                                <input type="text" name="budget_years[]" placeholder="e.g. 2026"
                                    class="w-24 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                <input type="text" name="budget_amounts[]" placeholder="Amount"
                                    class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div class="flex gap-2">
                                <input type="text" name="budget_years[]" placeholder="e.g. 2027"
                                    class="w-24 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                <input type="text" name="budget_amounts[]" placeholder="Amount"
                                    class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div class="flex gap-2">
                                <input type="text" name="budget_years[]" placeholder="e.g. 2028"
                                    class="w-24 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                <input type="text" name="budget_amounts[]" placeholder="Amount"
                                    class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        <button type="button" onclick="addBudgetRow('budget-rows-add')"
                            class="text-xs text-primary hover:text-primary-dark font-medium mb-4">+ Add Year</button>

                        <button type="submit"
                            class="w-full bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                            Add Activity
                        </button>
                    </form>
                </div>
            </div>

            <script>
                function addBudgetRow(containerId) {
                    const container = document.getElementById(containerId);
                    const row = document.createElement('div');
                    row.className = 'flex gap-2';
                    row.innerHTML = `
                                    <input type="text" name="budget_years[]" placeholder="Year"
                                           class="w-24 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                    <input type="text" name="budget_amounts[]" placeholder="Amount"
                                           class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                `;
                    container.appendChild(row);
                }
            </script>
        @endif

        @php
            $chartActivities = [];
            foreach ($program->activities as $a) {
                $chartActivities[] = [
                    'name' => $a->name,
                    'target' => $a->target_value,
                    'achieved' => $a->achieved_value,
                    'unit' => $a->value_unit,
                    'budget' => $a->budget_breakdown,
                ];
            }
        @endphp

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const activities = @json($chartActivities);

                const perfData = activities.filter(a => a.target !== null || a.achieved !== null);
                if (perfData.length && document.getElementById('performanceChart')) {
                    new Chart(document.getElementById('performanceChart'), {
                        type: 'bar',
                        data: {
                            labels: perfData.map(a => a.name),
                            datasets: [
                                { label: 'Target', data: perfData.map(a => a.target), backgroundColor: '#93c5fd' },
                                { label: 'Achieved', data: perfData.map(a => a.achieved), backgroundColor: '#16a34a' },
                            ]
                        },
                        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                    });
                }

                const yearTotals = {};
                activities.forEach(a => {
                    if (!a.budget) return;
                    Object.entries(a.budget).forEach(([year, amount]) => {
                        yearTotals[year] = (yearTotals[year] || 0) + parseFloat(amount || 0);
                    });
                });
                const years = Object.keys(yearTotals).sort();
                if (years.length && document.getElementById('budgetChart')) {
                    new Chart(document.getElementById('budgetChart'), {
                        type: 'line',
                        data: {
                            labels: years,
                            datasets: [{ label: 'Total Budget (₱)', data: years.map(y => yearTotals[y]), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.1)', fill: true, tension: 0.3 }]
                        },
                        options: { responsive: true, plugins: { legend: { display: false } } }
                    });
                }
            });
        </script>

    @endif

    {{-- Achievement photo lightbox --}}
    <div id="lightbox" class="hidden fixed inset-0 bg-black/90 z-50 items-center justify-center p-6" onclick="closeLightbox()">
        <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white text-2xl hover:text-accent transition">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="max-w-4xl w-full" onclick="event.stopPropagation()">
            <img id="lightboxImg" src="" class="w-full max-h-[75vh] object-contain rounded-lg">
            <p id="lightboxCaption" class="text-white text-base text-center mt-4"></p>
        </div>
    </div>

    <script>
        function openLightbox(src, caption) {
            document.getElementById('lightboxImg').src = src;
            document.getElementById('lightboxCaption').textContent = caption;
            document.getElementById('lightbox').classList.remove('hidden');
            document.getElementById('lightbox').classList.add('flex');
        }
        function closeLightbox() {
            document.getElementById('lightbox').classList.add('hidden');
            document.getElementById('lightbox').classList.remove('flex');
        }
    </script>

@endsection