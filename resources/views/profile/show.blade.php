@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h2 class="text-3xl font-bold tracking-tight text-gray-900">My Profile</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">View and update your account information</p>
</div>

@if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-3 gap-4">

    {{-- Left: Profile Card --}}
    <div class="col-span-1 space-y-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5 text-center">

            {{-- Profile Photo --}}
            <div class="relative inline-block mb-3">
                @if($user->photo)
                    <img src="{{ Storage::url($user->photo) }}"
                         alt="{{ $user->name }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-primary-light mx-auto"/>
                @else
                    <div class="w-24 h-24 rounded-full bg-primary flex items-center justify-center mx-auto border-4 border-primary-light">
                        <span class="text-white text-3xl font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </div>

            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $user->name }}</h3>
            <p class="text-xs text-gray-400 mt-1 capitalize">
                {{ ucfirst(str_replace('_', ' ', $user->role?->name ?? 'User')) }}
            </p>

            {{-- Barangay Info for Barangay Reps --}}
            @if($user->isBarangayUser())
            <div class="mt-4 bg-primary-light dark:bg-gray-900 rounded-md p-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Assigned Barangay</p>
                <p class="text-sm font-semibold text-primary">
                    {{ $user->barangayAccount?->barangay?->name ?? 'Not assigned' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Guinobatan, Albay</p>
            </div>
            @endif

            <div class="mt-4 text-xs text-gray-400 space-y-1 text-left">
                <p><span class="font-medium text-gray-600 dark:text-gray-300">Email:</span> {{ $user->email }}</p>
                <p><span class="font-medium text-gray-600 dark:text-gray-300">Status:</span>
                    <span class="text-green-600 dark:text-green-400 font-medium capitalize">{{ $user->status }}</span>
                </p>
                <p><span class="font-medium text-gray-600 dark:text-gray-300">Member since:</span> {{ $user->created_at->format('M d, Y') }}</p>
            </div>

            {{-- Remove Photo Button --}}
            @if($user->photo)
            <form method="POST" action="{{ route('profile.photo.remove') }}" class="mt-3">
                @csrf @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Remove profile photo?')"
                        class="text-xs text-red-500 hover:underline">
                    Remove Photo
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Right: Edit Forms --}}
    <div class="col-span-2 space-y-4">

        {{-- Update Profile --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                Update Profile Information
            </h3>
            @if($errors->has('name') || $errors->has('email') || $errors->has('photo'))
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-sm rounded-md p-3 mb-4">
                    @if($errors->has('name'))<p>{{ $errors->first('name') }}</p>@endif
                    @if($errors->has('email'))<p>{{ $errors->first('email') }}</p>@endif
                    @if($errors->has('photo'))<p>{{ $errors->first('photo') }}</p>@endif
                </div>
            @endif
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Photo Upload --}}
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">
                        Profile Photo
                        <span class="text-gray-400 font-normal">(JPG, PNG, GIF, WEBP — max 10MB)</span>
                    </label>
                    <div class="flex items-center gap-3">
                        @if($user->photo)
                            <img src="{{ Storage::url($user->photo) }}"
                                 class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700"/>
                        @else
                            <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center">
                                <span class="text-white text-lg font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <input type="file" name="photo" accept="image/*"
                               class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                               onchange="previewPhoto(this)"/>
                    </div>
                    {{-- Preview --}}
                    <img id="photoPreview" src="#" alt="Preview"
                         class="hidden w-20 h-20 rounded-full object-cover border-4 border-primary-light mt-3"/>
                </div>

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
                <button type="submit"
                        class="bg-primary hover:bg-primary-dark text-white font-semibold px-5 py-2 rounded-md transition text-sm">
                    Save Changes
                </button>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                Change Password
            </h3>
            @if($errors->has('current_password') || $errors->has('password'))
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-sm rounded-md p-3 mb-4">
                    @if($errors->has('current_password'))<p>{{ $errors->first('current_password') }}</p>@endif
                    @if($errors->has('password'))<p>{{ $errors->first('password') }}</p>@endif
                </div>
            @endif
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf @method('PUT')
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Current Password</label>
                        <input type="password" name="current_password" required
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">New Password</label>
                        <input type="password" name="password" required
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                    </div>
                </div>
                <button type="submit"
                        class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-5 py-2 rounded-md transition text-sm">
                    Change Password
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function previewPhoto(input) {
    const preview = document.getElementById('photoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection