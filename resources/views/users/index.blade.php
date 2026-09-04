@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">User Management</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Manage system users and access permissions</p>
    </div>
    <button onclick="openAddUserModal()"
            class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
        <i class="fa-solid fa-plus"></i> Add User
    </button>
</div>

{{-- Messages --}}
@if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-sm rounded-md p-3 mb-4">
        {{ session('error') }}
    </div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-border-soft dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Users</p>
        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalUsers }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-border-soft dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Admin</p>
        <p class="text-2xl font-bold text-red-500">{{ $admins }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-border-soft dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Staff</p>
        <p class="text-2xl font-bold text-blue-500">{{ $staff }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-border-soft dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Barangay Reps</p>
        <p class="text-2xl font-bold text-primary">{{ $barangayReps }}</p>
    </div>
</div>

{{-- Search --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-4 mb-4">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name or email..."
               class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        <select name="role" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">All Roles</option>
            <option value="admin"         {{ request('role') == 'admin'         ? 'selected' : '' }}>Admin</option>
            <option value="staff"         {{ request('role') == 'staff'         ? 'selected' : '' }}>Staff</option>
            <option value="barangay_user" {{ request('role') == 'barangay_user' ? 'selected' : '' }}>Barangay Rep</option>
        </select>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary-dark transition">
            Search
        </button>
    </form>
</div>

{{-- Pending Approvals Notice --}}
@php $pendingCount = $users->filter(fn($u) => $u->status === 'inactive' && $u->barangayAccount?->approval_status === 'pending')->count(); @endphp
@if($pendingCount > 0)
<div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 text-yellow-700 dark:text-yellow-300 text-sm rounded-md p-3 mb-4 flex items-center gap-2">
    <i class="fa-solid fa-clock"></i>
    <span>There are <strong>{{ $pendingCount }}</strong> pending registration request(s) waiting for your approval.</span>
</div>
@endif

{{-- Table --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Name</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Email</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Role</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Status</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Date Added</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $user->status === 'inactive' && $user->barangayAccount?->approval_status === 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">
                    {{ $user->name }}
                    @if($user->status === 'inactive' && $user->barangayAccount?->approval_status === 'pending')
                        <span class="ml-1 px-1.5 py-0.5 bg-accent/10 text-accent border border-accent/30 text-xs rounded-full">Pending</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                <td class="px-4 py-3">
                    @php
                        $roleColors = [
                            'admin'         => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                            'superadmin'    => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
                            'staff'         => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                            'barangay_user' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                            'viewer'        => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                        ];
                        $roleName = $user->role?->name ?? 'none';
                        $roleLabels = [
                            'admin'         => 'Admin',
                            'superadmin'    => 'Super Admin',
                            'staff'         => 'Staff',
                            'barangay_user' => 'Barangay Rep',
                            'viewer'        => 'Viewer',
                        ];
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $roleColors[$roleName] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                        {{ $roleLabels[$roleName] ?? ucfirst($roleName) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $user->status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-accent/10 text-accent border border-accent/30' }}">
                        {{ $user->status === 'inactive' && $user->barangayAccount?->approval_status === 'pending' ? 'Pending Approval' : ucfirst($user->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2 flex-wrap">

                        {{-- Approve button for pending barangay reps --}}
                        @if($user->status === 'inactive' && $user->barangayAccount?->approval_status === 'pending')
                        <form method="POST" action="{{ route('users.approve', $user) }}">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Approve {{ $user->name }} as Barangay Representative?')"
                                    class="text-primary hover:underline text-xs font-bold">
                                ✓ Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                              onsubmit="return confirm('Reject and delete this registration?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs font-medium">
                                ✗ Reject
                            </button>
                        </form>
                        @else
                        <a href="{{ route('users.edit', $user) }}"
                           class="text-blue-500 hover:underline text-xs font-medium">Edit</a>
                        <form method="POST" action="{{ route('users.reset-password', $user) }}">
                            @csrf
                            <button type="submit" onclick="return confirm('Reset password to Password@123?')"
                                    class="text-yellow-500 hover:underline text-xs font-medium">Reset PW</button>
                        </form>
                        @if($user->id !== Auth::id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                              onsubmit="return confirm('Delete this user?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs font-medium">Delete</button>
                        </form>
                        @endif
                        @endif

                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-border-soft dark:border-gray-700">
        {{ $users->links() }}
    </div>
</div>

{{-- Add User Modal --}}
<div id="addUserModal" style="display:none;" class="fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">Add User</h3>
            <button onclick="closeAddUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Full Name</label>
                    <input type="text" name="name" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Email Address</label>
                    <input type="email" name="email" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Role</label>
                    <select name="role_id" id="roleSelect" required onchange="toggleRoleFields()"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Select Role</option>
                        @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->id }}" data-name="{{ $role->name }}">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="barangayField" style="display:none;">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Assign Barangay <span class="text-red-500">*</span></label>
                    <select name="barangay_id"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Select Barangay</option>
                        @foreach(\App\Models\Barangay::orderBy('name')->get() as $barangay)
                            <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Program Assignment: optional. Stocks & Activities are open to every staff member — no assignment needed. --}}
                <div id="moduleField" style="display:none;">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Assigned Program(s) <span class="text-gray-400 font-normal">(optional)</span></label>
                    <p class="text-xs text-gray-400 mb-1">Stocks and Activities are available to all staff automatically. Only check a program here if this person coordinates it specifically.</p>

                    <div id="programsCheckboxes" class="mt-1">
                        <div class="border border-gray-300 dark:border-gray-600 rounded-md p-2 max-h-36 overflow-y-auto space-y-1">
                            @foreach(\App\Models\Program::orderBy('name')->get() as $program)
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 py-0.5">
                                <input type="checkbox" name="assigned_programs[]" value="{{ $program->id }}"
                                       class="rounded border-gray-300 text-primary focus:ring-primary"/>
                                {{ $program->name }}
                                @if($program->assignedUser)
                                    <span class="text-xs text-gray-400">(currently: {{ $program->assignedUser->name }})</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Program Access PIN <span class="text-gray-400">(optional, 4-6 digits)</span></label>
                    <input type="password" name="pin" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="submit"
                        class="flex-1 bg-primary hover:bg-primary-dark text-white font-semibold py-2 rounded-md transition text-sm">
                    Add User
                </button>
                <button type="button" onclick="closeAddUserModal()"
                        class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 font-medium py-2 rounded-md transition text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddUserModal() {
    document.getElementById('addUserModal').style.display = 'flex';
}

function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
}

function toggleRoleFields() {
    const roleSelect = document.getElementById('roleSelect');
    const selectedOption = roleSelect.options[roleSelect.selectedIndex];
    const roleName = selectedOption.getAttribute('data-name');

    document.getElementById('barangayField').style.display = (roleName === 'barangay_user') ? 'block' : 'none';
    document.getElementById('moduleField').style.display = (roleName === 'staff') ? 'block' : 'none';
}

document.getElementById('addUserModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddUserModal();
    }
});
</script>

@endsection