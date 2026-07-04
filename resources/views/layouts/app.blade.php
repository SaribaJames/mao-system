<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Municipal Agriculture Office</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="bg-gray-100 min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-56 bg-white min-h-screen shadow-sm flex flex-col fixed top-0 left-0 z-10">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-gray-100">
            <h1 class="text-base font-bold text-gray-800 leading-tight">Municipal Agriculture Office</h1>
            <p class="text-xs text-gray-400 mt-0.5">Management System</p>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('dashboard') ? 'bg-primary-light text-primary' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fa-solid fa-gauge-high w-4 text-center"></i>
                Dashboard
            </a>

            @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
            <a href="{{ route('service-records.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('service-records.*') ? 'bg-primary-light text-primary' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fa-solid fa-clipboard-list w-4 text-center"></i>
                Service Records
            </a>
            @endif

            <a href="{{ route('farmers.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('farmers.*') ? 'bg-primary-light text-primary' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fa-solid fa-person w-4 text-center"></i>
                Farmers
            </a>

            <a href="{{ route('requests.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('requests.*') ? 'bg-primary-light text-primary' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fa-solid fa-file-lines w-4 text-center"></i>
                Requests
            </a>

            @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
            <a href="{{ route('stocks.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('stocks.*') ? 'bg-primary-light text-primary' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fa-solid fa-boxes-stacked w-4 text-center"></i>
                Stocks
            </a>
            @endif

            @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('reports.*') ? 'bg-primary-light text-primary' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fa-solid fa-chart-bar w-4 text-center"></i>
                Reports
            </a>
            @endif

            <a href="{{ route('activities.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('activities.*') ? 'bg-primary-light text-primary' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fa-solid fa-calendar-days w-4 text-center"></i>
                Activities
            </a>

            {{-- Messages for Barangay Reps --}}
            @if(Auth::user()->role?->name === 'barangay_user')
            <a href="{{ route('messages.chat') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('messages.*') ? 'bg-primary-light text-primary' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fa-solid fa-comments w-4 text-center"></i>
                Messages
            </a>
            @endif

            {{-- Messages for Admin/Staff --}}
            @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
            @php
                $unreadMessages = \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count();
            @endphp
            <a href="{{ route('messages.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('messages.*') ? 'bg-primary-light text-primary' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fa-solid fa-comments w-4 text-center"></i>
                Messages
                @if($unreadMessages > 0)
                <span class="bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full ml-auto">
                    {{ $unreadMessages }}
                </span>
                @endif
            </a>
            @endif

            @if(Auth::user()->isAdmin())
            <a href="{{ route('users.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition
                      {{ request()->routeIs('users.*') ? 'bg-primary-light text-primary' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fa-solid fa-users-gear w-4 text-center"></i>
                User Management
            </a>
            @endif

        </nav>

        {{-- User Profile at Bottom of Sidebar --}}
        <div class="border-t border-gray-100 p-3">
            <div class="flex items-center gap-3 px-2 py-2 rounded-md hover:bg-gray-50 cursor-pointer transition"
                 onclick="window.location.href='{{ route('profile.show') }}'">
                @if(Auth::user()->photo)
                    <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                         class="w-8 h-8 rounded-full object-cover flex-shrink-0 border-2 border-primary-light"/>
                @else
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate capitalize">
                        @if(Auth::user()->role?->name === 'barangay_user')
                            {{ Auth::user()->barangayAccount?->barangay?->name ?? 'Barangay Rep' }}
                        @else
                            {{ ucfirst(str_replace('_', ' ', Auth::user()->role?->name ?? 'User')) }}
                        @endif
                    </p>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
            </div>
        </div>

    </aside>

    {{-- Main Content --}}
    <div class="ml-56 flex-1 flex flex-col min-h-screen">

        {{-- Top Bar --}}
        <header class="bg-white shadow-sm px-6 py-3 flex items-center justify-end sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ Auth::user()->role?->name ?? 'User' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Logout">
                        <i class="fa-solid fa-right-from-bracket text-lg"></i>
                    </button>
                </form>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>

    </div>

</body>
</html>