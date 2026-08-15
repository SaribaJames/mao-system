@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Edit User</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $user->email }}</p>
    </div>
    <a href="{{ route('users.index') }}"
       class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium px-4 py-2 rounded-md transition">
        ← Back
    </a>
</div>

@if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-sm rounded-md p-3 mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $currentModule = $user->manages_stocks ? 'stocks' : (count($assignedProgramIds) > 0 ? 'programs' : '');
@endphp

<form method="POST" action="{{ route('users.update', $user) }}">
@csrf @method('PUT')

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5 mb-4">
    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">User Information</h3>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Role</label>
            <select name="role_id" id="roleSelect" required onchange="toggleModuleField()"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" data-name="{{ $role->name }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Status</label>
            <select name="status" required
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="active"    {{ $user->status == 'active'    ? 'selected' : '' }}>Active</option>
                <option value="inactive"  {{ $user->status == 'inactive'  ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>
    </div>

    {{-- Module Assignment: mutually exclusive Programs vs Stocks --}}
    <div id="moduleField" class="mb-4" style="display:none;">
        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Module Assignment</label>
        <select name="module_assignment" id="moduleSelect" onchange="toggleProgramsCheckboxes()"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="" {{ $currentModule === '' ? 'selected' : '' }}>None</option>
            <option value="programs" {{ $currentModule === 'programs' ? 'selected' : '' }}>Programs (choose specific program(s))</option>
            <option value="stocks" {{ $currentModule === 'stocks' ? 'selected' : '' }}>Stocks</option>
        </select>
        <p class="text-xs text-gray-400 mt-1">A staff member can be assigned to Programs OR Stocks, not both. Changing this reassigns them — e.g. if a program coordinator retired, pick the new person here.</p>

        <div id="programsCheckboxes" style="display:none;" class="mt-2">
            <div class="border border-gray-300 dark:border-gray-600 rounded-md p-2 max-h-40 overflow-y-auto space-y-1">
                @foreach($programs as $program)
                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 py-0.5">
                    <input type="checkbox" name="assigned_programs[]" value="{{ $program->id }}"
                           {{ in_array($program->id, $assignedProgramIds) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-primary focus:ring-primary"/>
                    {{ $program->name }}
                    @if($program->assignedUser && $program->assignedUser->id !== $user->id)
                        <span class="text-xs text-gray-400">(currently: {{ $program->assignedUser->name }})</span>
                    @endif
                </label>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">New Password <span class="text-gray-400">(leave blank to keep current)</span></label>
            <input type="password" name="password"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Confirm New Password</label>
            <input type="password" name="password_confirmation"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">
                Program Access PIN <span class="text-gray-400">(4-6 digits, leave blank to keep current)</span>
            </label>
            <input type="password" name="pin" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
            <p class="text-xs text-gray-400 mt-1">
                {{ $user->hasPin() ? 'A PIN is currently set.' : 'No PIN set yet.' }}
                Used to unlock program management for assigned personnel.
            </p>
        </div>
    </div>
</div>

<div class="flex items-center gap-3">
    <button type="submit"
            class="bg-primary hover:bg-primary-dark text-white font-semibold px-6 py-2.5 rounded-md transition text-sm">
        Save Changes
    </button>
    <a href="{{ route('users.index') }}"
       class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium px-6 py-2.5 rounded-md transition text-sm">
        Cancel
    </a>
</div>

</form>

<script>
function toggleModuleField() {
    const roleSelect = document.getElementById('roleSelect');
    const selectedOption = roleSelect.options[roleSelect.selectedIndex];
    const roleName = selectedOption.getAttribute('data-name');
    document.getElementById('moduleField').style.display = (roleName === 'staff') ? 'block' : 'none';
    toggleProgramsCheckboxes();
}

function toggleProgramsCheckboxes() {
    const moduleSelect = document.getElementById('moduleSelect');
    document.getElementById('programsCheckboxes').style.display = (moduleSelect.value === 'programs') ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', toggleModuleField);
</script>

@endsection