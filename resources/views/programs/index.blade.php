@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">MAO Programs</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Municipal Agriculture Office programs and enrolled farmers</p>
    </div>
    @if(Auth::user()->isAdmin())
    <button type="button" onclick="openProgramModal()"
            class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
        <i class="fa-solid fa-plus"></i> Add Program
    </button>
    @endif
</div>

@if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-sm rounded-md p-3 mb-4">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

{{-- Success Message --}}
@if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-border-soft dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Programs</p>
        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalPrograms }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-border-soft dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Active Enrollments</p>
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
        <div class="relative">
        <a href="{{ route('programs.show', $program) }}"
           class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5 hover:shadow-md transition block
                  {{ $isMine ? 'border-2 border-primary ring-1 ring-primary-light' : 'border border-border-soft dark:border-gray-700' }}">
            <div class="flex items-start justify-between mb-3">
                <i class="fa-solid fa-seedling text-primary text-lg"></i>
                <div class="flex items-center gap-1">
                    @if($isMine)
                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-primary text-white">
                        <i class="fa-solid fa-star text-[10px]"></i> Your Program
                    </span>
                    @endif
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $program->status_color }}">
                        {{ ucfirst($program->status) }}
                    </span>
                </div>
            </div>
            <p class="font-semibold text-gray-800 dark:text-gray-100 mb-1">{{ $program->name }}</p>
            <p class="text-xs text-gray-400 mb-3">
                <i class="fa-solid fa-user-shield w-4 text-center"></i>
                Assigned: {{ $program->assignedUser?->name ?? 'Unassigned' }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                <i class="fa-solid fa-users w-4 text-center"></i>
                {{ $program->active_enrollments_count }} active farmer(s)
            </p>
        </a>
        @if(Auth::user()->isAdmin())
            <button type="button"
                    onclick="openProgramModal({{ Js::from($program->only(['id', 'name', 'coordinator_name', 'assigned_user_id', 'description', 'status'])) }})"
                    class="absolute bottom-4 right-4 text-xs font-medium text-blue-500 hover:underline">
                <i class="fa-solid fa-pen"></i> Edit
            </button>
        @endif
        </div>
        @else
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg border border-border-soft dark:border-gray-700 p-5 opacity-70 cursor-not-allowed relative"
             title="You are not assigned to this program">
            <div class="flex items-start justify-between mb-3">
                <i class="fa-solid fa-seedling text-gray-300 dark:text-gray-600 text-lg"></i>
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                    <i class="fa-solid fa-lock text-[10px]"></i> Restricted
                </span>
            </div>
            <p class="font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ $program->name }}</p>
            <p class="text-xs text-gray-400 mb-3">
                <i class="fa-solid fa-user-shield w-4 text-center"></i>
                Assigned: {{ $program->assignedUser?->name ?? 'Unassigned' }}
            </p>
            <p class="text-xs text-gray-400">Not assigned to you</p>
        </div>
        @endif
    @endforeach
</div>

@if(Auth::user()->isAdmin())
{{-- Add / Edit Program modal (one form, switched between store and update) --}}
<div id="programModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border-soft dark:border-gray-700">
            <h3 id="programModalTitle" class="font-semibold text-gray-800 dark:text-gray-100">Add Program</h3>
            <button type="button" onclick="document.getElementById('programModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="programForm" method="POST" action="{{ route('programs.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="programFormMethod" value="POST">
            <input type="hidden" name="editing_id" id="programEditingId" value="{{ old('editing_id') }}">

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Program Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="programName" required maxlength="255" value="{{ old('name') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Program Coordinator <span class="text-red-500">*</span></label>
                <input type="text" name="coordinator_name" id="programCoordinator" required maxlength="255" value="{{ old('coordinator_name') }}"
                       placeholder="e.g. Engr. Juan Dela Cruz"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Assigned Personnel (staff account)</label>
                <select name="assigned_user_id" id="programAssigned"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">Unassigned</option>
                    @foreach($staffUsers as $staff)
                        <option value="{{ $staff->id }}" {{ old('assigned_user_id') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Only this staff member (and the admin) can open and manage the program.</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Description</label>
                <textarea name="description" id="programDescription" rows="3" maxlength="2000"
                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="programStatus"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Programs can't be deleted, because their records and reports depend on them. Set one to Inactive to retire it.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('programModal').classList.add('hidden')"
                        class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium px-4 py-2 rounded-md transition">
                    Cancel
                </button>
                <button type="submit" id="programSubmit"
                        class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                    Save Program
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const programUpdateUrl = @json(url('/programs')) + '/';

    // No argument = Add; a program object = Edit (pre-filled)
    function openProgramModal(program = null) {
        const form = document.getElementById('programForm');
        document.getElementById('programModalTitle').textContent = program ? 'Edit Program' : 'Add Program';
        document.getElementById('programSubmit').textContent = program ? 'Save Changes' : 'Save Program';
        form.action = program ? programUpdateUrl + program.id : @json(route('programs.store'));
        document.getElementById('programFormMethod').value = program ? 'PUT' : 'POST';
        document.getElementById('programEditingId').value = program ? program.id : '';
        if (program) {
            document.getElementById('programName').value = program.name ?? '';
            document.getElementById('programCoordinator').value = program.coordinator_name ?? '';
            document.getElementById('programAssigned').value = program.assigned_user_id ?? '';
            document.getElementById('programDescription').value = program.description ?? '';
            document.getElementById('programStatus').value = program.status ?? 'active';
        } else if (!@json($errors->any())) {
            form.reset();
        }
        document.getElementById('programModal').classList.remove('hidden');
    }

    // Re-open the form after a failed save so the error can be fixed
    @if($errors->any() && old('name') !== null)
        @if(old('editing_id'))
            openProgramModal({{ Js::from([
                'id' => old('editing_id'),
                'name' => old('name'),
                'coordinator_name' => old('coordinator_name'),
                'assigned_user_id' => old('assigned_user_id'),
                'description' => old('description'),
                'status' => old('status'),
            ]) }});
        @else
            openProgramModal();
        @endif
    @endif
</script>
@endif

@endsection