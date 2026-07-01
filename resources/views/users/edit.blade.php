@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Edit User</h2>
        <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
    </div>
    <a href="{{ route('users.index') }}"
       class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-md transition">
        ← Back
    </a>
</div>

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-md p-3 mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('users.update', $user) }}">
@csrf @method('PUT')

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 mb-4">
    <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">User Information</h3>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Role</label>
            <select name="role_id" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="active"    {{ $user->status == 'active'    ? 'selected' : '' }}>Active</option>
                <option value="inactive"  {{ $user->status == 'inactive'  ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">New Password <span class="text-gray-400">(leave blank to keep current)</span></label>
            <input type="password" name="password"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Confirm New Password</label>
            <input type="password" name="password_confirmation"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>
</div>

<div class="flex items-center gap-3">
    <button type="submit"
            class="bg-primary hover:bg-primary-dark text-white font-semibold px-6 py-2.5 rounded-md transition text-sm">
        Save Changes
    </button>
    <a href="{{ route('users.index') }}"
       class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-6 py-2.5 rounded-md transition text-sm">
        Cancel
    </a>
</div>

</form>
@endsection