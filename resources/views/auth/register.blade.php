<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Municipal Agriculture Office</title>
    {{-- Anti-flash-of-wrong-theme: must run before Tailwind CDN loads and before body paints --}}
    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#2D7A2D',
                        'primary-dark': '#1f5c1f',
                        'primary-light': '#e8f5e8',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md w-full max-w-md p-8">

        {{-- Header --}}
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Municipal Agriculture Office</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Barangay Representative Registration</p>
        </div>

        {{-- Info Box --}}
        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 text-yellow-700 dark:text-yellow-300 text-xs rounded-md p-3 mb-4">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
            Your registration will be reviewed by the MAO Admin before you can login.
            This is to ensure only authorized Barangay Representatives can access the system.
        </div>

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-sm rounded-md p-3 mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Register Form --}}
        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Juan Dela Cruz"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="example@barangay.gov.ph"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Barangay <span class="text-red-500">*</span></label>
                    <select name="barangay_id" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Select your Barangay</option>
                        @foreach(\App\Models\Barangay::orderBy('name')->get() as $barangay)
                            <option value="{{ $barangay->id }}" {{ old('barangay_id') == $barangay->id ? 'selected' : '' }}>
                                {{ $barangay->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required
                           placeholder="Minimum 8 characters"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required
                           placeholder="Re-enter your password"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                </div>
            </div>

            <button type="submit"
                    class="w-full mt-4 bg-primary hover:bg-primary-dark text-white font-semibold py-2 rounded-md transition text-sm">
                Submit Registration Request
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-primary hover:underline">
                ← Back to Login
            </a>
        </div>

    </div>

@include('partials.password-toggle')
</body>
</html>