<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Municipal Agriculture Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
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

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-lg shadow-md w-full max-w-md p-8">

        {{-- Header --}}
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Municipal Agriculture Office</h1>
            <p class="text-gray-500 text-sm mt-1">Management System</p>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3 mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-md p-3 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Login As</label>
                <select name="role_hint"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="admin">Admin / Agriculturist</option>
                    <option value="staff">Staff Member</option>
                    <option value="barangay">Barangay Representative</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@mao.gov.ph"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required placeholder="Enter password"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
            </div>

            <button type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2 rounded-md transition duration-200">
                Sign In
            </button>
        </form>

        {{-- Sign Up --}}
        <div class="mt-4 border-t border-gray-100 pt-4 text-center">
            <p class="text-xs text-gray-500 mb-2">Are you a Barangay Representative?</p>
            <a href="{{ route('register') }}"
                class="w-full block border border-primary text-primary hover:bg-primary-light font-semibold py-2 rounded-md transition text-sm text-center">
                Request Access / Sign Up
            </a>
        </div>

        {{-- Demo Access Info --}}
        <div class="mt-4 bg-gray-50 border border-gray-200 rounded-md p-4 text-xs text-gray-600">
            <p class="font-semibold mb-1">Demo Access:</p>
            <p><span class="font-medium">Admin:</span> admin@mao-guinobatan.gov.ph / Admin@1234</p>
            <p><span class="font-medium">Staff:</span> Process transactions, manage farmers</p>
            <p><span class="font-medium">Barangay Rep:</span> Submit requests, view activities</p>
        </div>

    </div>

</body>

</html>