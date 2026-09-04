<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Municipal Agriculture Office</title>
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
                        'primary-light': '#F6F8F6',
                        accent: '#D4A017',
                        'border-soft': '#D8DFD8',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }
    </style>
</head>

<body class="min-h-screen relative flex items-center justify-center px-4 py-10">

    {{-- Full-page background: Guinobatan plaza photo with dark overlay --}}
    <div class="fixed inset-0 -z-10">
        <img src="{{ asset('images/guinobatan-plaza.jpg') }}" alt="Guinobatan Town Plaza"
            class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/55 dark:bg-black/70"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-black/60"></div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-border-soft dark:border-gray-700 w-full max-w-md p-8">

        {{-- Letterhead --}}
        <div class="text-center mb-6">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Republic of the Philippines</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Province of Albay</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Municipality of Guinobatan</p>
            <h1 class="text-2xl font-bold text-primary-dark mt-2">Municipal Agriculture Office</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Management System</p>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm rounded-md p-3 mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-sm rounded-md p-3 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Login As</label>
                <select name="role_hint"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="admin">Admin / Agriculturist</option>
                    <option value="staff">Staff Member</option>
                    <option value="barangay">Barangay Representative</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@mao.gov.ph"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <input type="password" name="password" required placeholder="Enter password"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
            </div>

            <button type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2 rounded-md transition duration-200">
                Sign In
            </button>
        </form>

        {{-- Sign Up --}}
        <div class="mt-4 border-t border-border-soft dark:border-gray-700 pt-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Are you a Barangay Representative?</p>
            <a href="{{ route('register') }}"
                class="w-full block border border-primary text-primary hover:bg-primary-light font-semibold py-2 rounded-md transition text-sm text-center">
                Request Access / Sign Up
            </a>
        </div>

        {{-- Demo Access Info --}}
        <div class="mt-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md p-4 text-xs text-gray-600 dark:text-gray-300">
            <p class="font-semibold mb-1">Demo Access:</p>
            <p><span class="font-medium">Admin:</span> admin@mao-guinobatan.gov.ph / Admin@1234</p>
            <p><span class="font-medium">Staff:</span> Process transactions, manage farmers</p>
            <p><span class="font-medium">Barangay Rep:</span> Submit requests, view activities</p>
        </div>

    </div>

@include('partials.password-toggle')
</body>

</html>