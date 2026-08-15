<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Municipal Agriculture Office</title>
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
                        'accent-dark': '#a97e11',
                        'border-soft': '#D8DFD8',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }

        @media print {
            header.topnav {
                display: none !important;
            }

            .fixed.bottom-6,
            button,
            form {
                display: none !important;
            }

            canvas {
                max-width: 100% !important;
            }
        }
    </style>

</head>

<body class="bg-primary-light dark:bg-gray-900 min-h-screen">

    {{-- Top Navigation (replaces sidebar entirely) --}}
    <header class="topnav sticky top-0 z-20 shadow-md">

        {{-- Single row: branding + nav links + user, all in one bar --}}
        <div class="bg-gradient-to-r from-primary to-primary-dark px-6 py-2 flex items-center gap-4 border-b-4 border-accent">

            {{-- Logo + Title --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <img src="{{ asset('images/mao-logo.png') }}" alt="MAO Logo" class="w-8 h-8 rounded-full object-cover bg-white p-0.5 flex-shrink-0 ring-2 ring-accent">
                <h1 class="text-sm font-bold text-white leading-tight whitespace-nowrap">Municipal Agriculture Office</h1>
            </div>

            {{-- Separator --}}
            <div class="w-px h-6 bg-white/20 flex-shrink-0"></div>

            {{-- Nav links: standalone items + grouped dropdowns --}}
            <nav class="flex-1 flex items-center gap-1">

                <a href="{{ route('dashboard') }}"
                    class="px-4 py-2.5 text-sm font-medium transition
                          {{ request()->routeIs('dashboard') ? 'bg-accent text-primary-dark font-bold' : 'text-white/90 hover:bg-white/10' }}">
                    Dashboard
                </a>

                {{-- Records dropdown: Farmers, Service Records, Requests --}}
                @php $recordsActive = request()->routeIs('farmers.*') || request()->routeIs('service-records.*') || request()->routeIs('requests.*'); @endphp
                <div class="relative nav-dropdown">
                    <button type="button" onclick="toggleNavGroup('navGroupRecords')"
                        class="px-4 py-2.5 rounded text-sm font-medium transition flex items-center gap-1.5
                              {{ $recordsActive ? 'bg-accent text-primary-dark font-bold' : 'text-white/90 hover:bg-white/10' }}">
                        Records
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                    <div id="navGroupRecordsPanel"
                        class="nav-dropdown-panel hidden absolute left-0 mt-1 min-w-[180px] bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-border-soft dark:border-gray-700 z-50 overflow-hidden">
                        <a href="{{ route('farmers.index') }}"
                            class="block px-4 py-2.5 text-sm transition {{ request()->routeIs('farmers.*') ? 'font-semibold text-primary bg-primary-light dark:bg-gray-700' : 'text-gray-700 dark:text-gray-300 hover:bg-primary-light dark:hover:bg-gray-700 hover:text-primary-dark' }}">
                            Farmers
                        </a>
                        @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
                            <a href="{{ route('service-records.index') }}"
                                class="block px-4 py-2.5 text-sm transition {{ request()->routeIs('service-records.*') ? 'font-semibold text-primary bg-primary-light dark:bg-gray-700' : 'text-gray-700 dark:text-gray-300 hover:bg-primary-light dark:hover:bg-gray-700 hover:text-primary-dark' }}">
                                Service Records
                            </a>
                        @endif
                        @unless(Auth::user()->role?->name === 'staff' && Auth::user()->hasAssignedProgram())
                            <a href="{{ route('requests.index') }}"
                                class="block px-4 py-2.5 text-sm transition {{ request()->routeIs('requests.*') ? 'font-semibold text-primary bg-primary-light dark:bg-gray-700' : 'text-gray-700 dark:text-gray-300 hover:bg-primary-light dark:hover:bg-gray-700 hover:text-primary-dark' }}">
                                Requests
                            </a>
                        @endunless
                    </div>
                </div>

                {{-- Operations dropdown: Stocks, Programs, Activities --}}
                @php $operationsActive = request()->routeIs('stocks.*') || request()->routeIs('programs.*') || request()->routeIs('activities.*'); @endphp
                <div class="relative nav-dropdown">
                    <button type="button" onclick="toggleNavGroup('navGroupOperations')"
                        class="px-4 py-2.5 rounded text-sm font-medium transition flex items-center gap-1.5
                              {{ $operationsActive ? 'bg-accent text-primary-dark font-bold' : 'text-white/90 hover:bg-white/10' }}">
                        Operations
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                    <div id="navGroupOperationsPanel"
                        class="nav-dropdown-panel hidden absolute left-0 mt-1 min-w-[180px] bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-border-soft dark:border-gray-700 z-50 overflow-hidden">
                        @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
                            <a href="{{ route('stocks.index') }}"
                                class="block px-4 py-2.5 text-sm transition {{ request()->routeIs('stocks.*') ? 'font-semibold text-primary bg-primary-light dark:bg-gray-700' : 'text-gray-700 dark:text-gray-300 hover:bg-primary-light dark:hover:bg-gray-700 hover:text-primary-dark' }}">
                                Stocks
                            </a>
                        @endif
                        <a href="{{ route('programs.index') }}"
                            class="block px-4 py-2.5 text-sm transition {{ request()->routeIs('programs.*') ? 'font-semibold text-primary bg-primary-light dark:bg-gray-700' : 'text-gray-700 dark:text-gray-300 hover:bg-primary-light dark:hover:bg-gray-700 hover:text-primary-dark' }}">
                            Programs
                        </a>
                        <a href="{{ route('activities.index') }}"
                            class="block px-4 py-2.5 text-sm transition {{ request()->routeIs('activities.*') ? 'font-semibold text-primary bg-primary-light dark:bg-gray-700' : 'text-gray-700 dark:text-gray-300 hover:bg-primary-light dark:hover:bg-gray-700 hover:text-primary-dark' }}">
                            Activities
                        </a>
                    </div>
                </div>

                {{-- Reports & Forms dropdown: admin/staff only --}}
                @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
                    @php $reportsFormsActive = request()->routeIs('reports.*') || request()->routeIs('forms.*'); @endphp
                    <div class="relative nav-dropdown">
                        <button type="button" onclick="toggleNavGroup('navGroupReportsForms')"
                            class="px-4 py-2.5 rounded text-sm font-medium transition flex items-center gap-1.5
                                  {{ $reportsFormsActive ? 'bg-accent text-primary-dark font-bold' : 'text-white/90 hover:bg-white/10' }}">
                            Reports & Forms
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </button>
                        <div id="navGroupReportsFormsPanel"
                            class="nav-dropdown-panel hidden absolute left-0 mt-1 min-w-[180px] bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-border-soft dark:border-gray-700 z-50 overflow-hidden">
                            <a href="{{ route('reports.index') }}"
                                class="block px-4 py-2.5 text-sm transition {{ request()->routeIs('reports.*') ? 'font-semibold text-primary bg-primary-light dark:bg-gray-700' : 'text-gray-700 dark:text-gray-300 hover:bg-primary-light dark:hover:bg-gray-700 hover:text-primary-dark' }}">
                                Reports
                            </a>
                            <a href="{{ route('forms.index') }}"
                                class="block px-4 py-2.5 text-sm transition {{ request()->routeIs('forms.*') ? 'font-semibold text-primary bg-primary-light dark:bg-gray-700' : 'text-gray-700 dark:text-gray-300 hover:bg-primary-light dark:hover:bg-gray-700 hover:text-primary-dark' }}">
                                Forms & Documents
                            </a>
                        </div>
                    </div>
                @endif

                @if(Auth::user()->role?->name === 'barangay_user')
                    <a href="{{ route('messages.chat') }}"
                        class="px-4 py-2.5 text-sm font-medium transition
                              {{ request()->routeIs('messages.*') ? 'bg-accent text-primary-dark font-bold' : 'text-white/90 hover:bg-white/10' }}">
                        Messages
                    </a>
                @endif

                @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
                    @php $unreadMessages = \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count(); @endphp
                    <a href="{{ route('messages.index') }}"
                        class="px-4 py-2.5 text-sm font-medium transition flex items-center gap-2
                              {{ request()->routeIs('messages.*') ? 'bg-accent text-primary-dark font-bold' : 'text-white/90 hover:bg-white/10' }}">
                        Messages
                        @if($unreadMessages > 0)
                            <span class="bg-white text-primary-dark text-xs font-bold px-1.5 py-0.5 rounded-full">
                                {{ $unreadMessages }}
                            </span>
                        @endif
                    </a>
                @endif

                @if(Auth::user()->isBarangayUser())
                    <a href="{{ route('endorsements.index') }}"
                        class="px-4 py-2.5 text-sm font-medium transition
                              {{ request()->routeIs('endorsements.*') ? 'bg-accent text-primary-dark font-bold' : 'text-white/90 hover:bg-white/10' }}">
                        Endorsements
                    </a>
                @endif

                @if(Auth::user()->isAdmin())
                    <a href="{{ route('users.index') }}"
                        class="px-4 py-2.5 text-sm font-medium transition
                              {{ request()->routeIs('users.*') ? 'bg-accent text-primary-dark font-bold' : 'text-white/90 hover:bg-white/10' }}">
                        User Management
                    </a>
                @endif

            </nav>

            <div class="flex items-center gap-3 flex-shrink-0">

                {{-- Notification Bell --}}
                @php
                    $notifications = collect();

                    if (Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff') {
                        $newFarmers = \App\Models\Farmer::whereDate('created_at', today())->count();
                        if ($newFarmers > 0)
                            $notifications->push(['icon' => 'fa-person', 'color' => 'text-green-500', 'bg' => 'bg-green-100', 'text' => "{$newFarmers} new farmer(s) registered today", 'link' => route('farmers.index')]);

                        $pendingReqs = \App\Models\FarmerRequest::where('status', 'pending')->count();
                        if ($pendingReqs > 0)
                            $notifications->push(['icon' => 'fa-file-lines', 'color' => 'text-yellow-500', 'bg' => 'bg-yellow-100', 'text' => "{$pendingReqs} pending request(s) need attention", 'link' => route('requests.index')]);

                        $newReqs = \App\Models\FarmerRequest::whereDate('created_at', today())->count();
                        if ($newReqs > 0)
                            $notifications->push(['icon' => 'fa-file-circle-plus', 'color' => 'text-blue-500', 'bg' => 'bg-blue-100', 'text' => "{$newReqs} new request(s) submitted today", 'link' => route('requests.index')]);

                        $newServices = \App\Models\ServiceRecord::whereDate('created_at', today())->count();
                        if ($newServices > 0)
                            $notifications->push(['icon' => 'fa-clipboard-list', 'color' => 'text-purple-500', 'bg' => 'bg-purple-100', 'text' => "{$newServices} service record(s) created today", 'link' => route('service-records.index')]);

                        $newEnrollments = \App\Models\ProgramEnrollment::whereDate('created_at', today())->count();
                        if ($newEnrollments > 0)
                            $notifications->push(['icon' => 'fa-seedling', 'color' => 'text-primary', 'bg' => 'bg-green-100', 'text' => "{$newEnrollments} farmer(s) enrolled in programs today", 'link' => route('programs.index')]);

                        $unreadMsgs = \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count();
                        if ($unreadMsgs > 0)
                            $notifications->push(['icon' => 'fa-comments', 'color' => 'text-primary', 'bg' => 'bg-green-100', 'text' => "{$unreadMsgs} unread message(s)", 'link' => route('messages.index')]);

                        $pendingUsers = \App\Models\User::where('status', 'inactive')->whereHas('barangayAccount', fn($q) => $q->where('approval_status', 'pending'))->count();
                        if ($pendingUsers > 0)
                            $notifications->push(['icon' => 'fa-user-clock', 'color' => 'text-orange-500', 'bg' => 'bg-orange-100', 'text' => "{$pendingUsers} pending user registration(s)", 'link' => route('users.index')]);

                    } elseif (Auth::user()->role?->name === 'barangay_user') {
                        $barangayId = Auth::user()->barangayAccount?->barangay_id;

                        $myPending = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->where('status', 'pending')->count();
                        if ($myPending > 0)
                            $notifications->push(['icon' => 'fa-clock', 'color' => 'text-yellow-500', 'bg' => 'bg-yellow-100', 'text' => "{$myPending} request(s) still pending", 'link' => route('requests.index')]);

                        $myApproved = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->where('status', 'approved')->count();
                        if ($myApproved > 0)
                            $notifications->push(['icon' => 'fa-circle-check', 'color' => 'text-green-500', 'bg' => 'bg-green-100', 'text' => "{$myApproved} request(s) approved", 'link' => route('requests.index')]);

                        $adminUser = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
                        if ($adminUser) {
                            $unreadMsgs = \App\Models\Message::where('sender_id', $adminUser->id)->where('receiver_id', Auth::id())->where('is_read', false)->count();
                            if ($unreadMsgs > 0)
                                $notifications->push(['icon' => 'fa-comments', 'color' => 'text-primary', 'bg' => 'bg-green-100', 'text' => "{$unreadMsgs} unread message(s) from MAO", 'link' => route('messages.chat')]);
                        }

                        $newActivities = \App\Models\Activity::whereDate('created_at', today())->where('status', 'active')->count();
                        if ($newActivities > 0)
                            $notifications->push(['icon' => 'fa-bullhorn', 'color' => 'text-red-500', 'bg' => 'bg-red-100', 'text' => "{$newActivities} new announcement(s) posted today", 'link' => route('activities.index')]);
                    }
                @endphp

                <div class="relative" id="notifDropdown">
                    <button onclick="toggleNotif()"
                        class="relative text-white hover:text-accent transition p-2 rounded-full hover:bg-white/10">
                        <i class="fa-solid fa-bell text-lg"></i>
                        @if($notifications->count() > 0)
                            <span id="notifBadge"
                                class="absolute top-0 right-0 w-4 h-4 bg-accent text-primary-dark text-xs font-bold rounded-full flex items-center justify-center">
                                {{ $notifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div id="notifPanel"
                        class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-border-soft dark:border-gray-700 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-border-soft dark:border-gray-700 flex items-center justify-between bg-primary-light dark:bg-gray-900">
                            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">Notifications</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ now()->format('M d, Y') }}</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse($notifications as $notif)
                                <a href="{{ $notif['link'] }}"
                                    class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-50 dark:border-gray-700 last:border-0 transition">
                                    <div class="{{ $notif['bg'] }} p-2 rounded-lg flex-shrink-0">
                                        <i class="fa-solid {{ $notif['icon'] }} {{ $notif['color'] }} text-sm"></i>
                                    </div>
                                    <p class="text-xs text-gray-700 dark:text-gray-300 mt-1">{{ $notif['text'] }}</p>
                                </a>
                            @empty
                                <div class="px-4 py-8 text-center">
                                    <i class="fa-solid fa-bell-slash text-gray-300 dark:text-gray-600 text-2xl mb-2"></i>
                                    <p class="text-xs text-gray-400">No new notifications</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="px-4 py-2 border-t border-border-soft dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <p class="text-xs text-gray-400 text-center">{{ $notifications->count() }} notification(s)</p>
                        </div>
                    </div>
                </div>

                {{-- Dark Mode Toggle --}}
                <button type="button" onclick="toggleTheme()" id="themeToggle"
                    class="text-white hover:text-accent transition p-2 rounded-full hover:bg-white/10" title="Toggle dark mode">
                    <i class="fa-solid fa-moon text-lg" id="themeToggleIconMoon"></i>
                    <i class="fa-solid fa-sun text-lg hidden" id="themeToggleIconSun"></i>
                </button>

                {{-- Profile: avatar + name/role, wrapped in one clickable link --}}
                <a href="{{ route('profile.show') }}"
                    class="hidden sm:flex items-center gap-2 hover:bg-white/10 rounded-lg px-2 py-1 transition">
                    @if(Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Profile photo"
                            class="w-8 h-8 rounded-full object-cover flex-shrink-0" />
                    @else
                        <div class="w-8 h-8 rounded-full bg-accent flex items-center justify-center flex-shrink-0">
                            <span class="text-primary-dark text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-semibold text-white leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-accent font-medium capitalize leading-tight">
                            @if(Auth::user()->role?->name === 'barangay_user')
                                {{ Auth::user()->barangayAccount?->barangay?->name ?? 'Barangay Rep' }}
                            @else
                                {{ ucfirst(str_replace('_', ' ', Auth::user()->role?->name ?? 'User')) }}
                            @endif
                        </p>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-white hover:text-accent transition" title="Logout">
                        <i class="fa-solid fa-right-from-bracket text-lg"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- Page Content --}}
    <main class="p-6 max-w-[1400px] mx-auto">
        @yield('content')
    </main>

    {{-- Floating Chat Bubble for Barangay Reps --}}
    @if(Auth::user()->role?->name === 'barangay_user')
        @php
            $adminUser = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
            $unreadFromAdmin = $adminUser ? \App\Models\Message::where('sender_id', $adminUser->id)->where('receiver_id', Auth::id())->where('is_read', false)->count() : 0;
            $chatMessages = $adminUser ? \App\Models\Message::where(function ($q) use ($adminUser) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $adminUser->id);
            })->orWhere(function ($q) use ($adminUser) {
                $q->where('sender_id', $adminUser->id)->where('receiver_id', Auth::id());
            })->orderBy('created_at')->take(50)->get() : collect();
        @endphp

        <div id="chatBubble" class="fixed bottom-6 right-6 z-50">
            <button onclick="toggleChat()"
                class="bg-accent hover:bg-accent-dark text-primary-dark w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition relative border-2 border-white">
                <i class="fa-solid fa-comments text-xl"></i>
                @if($unreadFromAdmin > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                        {{ $unreadFromAdmin }}
                    </span>
                @endif
            </button>
        </div>

        <div id="chatWindow"
            class="fixed bottom-24 right-6 z-50 hidden w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-border-soft dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-primary-dark px-4 py-3 flex items-center justify-between border-b-2 border-accent">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-accent flex items-center justify-center">
                        <i class="fa-solid fa-headset text-primary-dark text-sm"></i>
                    </div>
                    <div>
                        <p class="text-white text-sm font-semibold">MAO Support</p>
                        <p class="text-white text-xs opacity-75">Municipal Agriculture Office</p>
                    </div>
                </div>
                <button onclick="toggleChat()" class="text-white hover:text-accent">
                    <i class="fa-solid fa-x text-sm"></i>
                </button>
            </div>

            <div class="h-64 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900" id="chatMessages">
                @forelse($chatMessages as $msg)
                    @php $isMine = $msg->sender_id === Auth::id(); @endphp
                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-48">
                            @if($msg->message)
                                <div class="px-3 py-2 rounded-2xl text-xs
                                    {{ $isMine ? 'bg-primary text-white rounded-br-sm' : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-bl-sm shadow-sm' }}">
                                    {{ $msg->message }}
                                </div>
                            @endif

                            @if($msg->attachment_path)
                                <div class="mt-1">
                                    @if($msg->attachment_type === 'image')
                                        <a href="{{ asset('storage/' . $msg->attachment_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $msg->attachment_path) }}"
                                                class="rounded border border-gray-300 dark:border-gray-600 max-w-full max-h-32 object-cover">
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $msg->attachment_path) }}" target="_blank"
                                            class="flex items-center gap-1.5 px-2 py-1.5 rounded border {{ $isMine ? 'bg-primary-dark border-primary-dark text-white' : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }} text-xs hover:opacity-80 transition">
                                            <i class="fa-solid {{ $msg->attachment_type === 'pdf' ? 'fa-file-pdf' : 'fa-file-lines' }}"></i>
                                            <span class="truncate max-w-[100px]">{{ $msg->attachment_name }}</span>
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <p class="text-xs text-gray-400 mt-0.5 {{ $isMine ? 'text-right' : 'text-left' }}">
                                {{ $msg->created_at->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <i class="fa-solid fa-comments text-gray-300 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-400">No messages yet.</p>
                        <p class="text-xs text-gray-400">Send a message to MAO staff!</p>
                    </div>
                @endforelse
            </div>

            <div class="p-3 border-t border-border-soft dark:border-gray-700 bg-white dark:bg-gray-800">
                @if($adminUser)
                    <form method="POST" action="{{ route('messages.send') }}" enctype="multipart/form-data" id="widgetChatForm" class="space-y-2">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $adminUser->id }}">

                        <div id="widgetFilePreview" class="hidden items-center gap-1.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-xs text-gray-600 dark:text-gray-300 w-fit">
                            <i class="fa-solid fa-paperclip"></i>
                            <span id="widgetFileName" class="truncate max-w-[120px]"></span>
                            <button type="button" onclick="clearWidgetFile()" class="text-red-500 hover:text-red-700 ml-1">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div class="flex gap-2">
                            <label for="widgetAttachmentInput" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 w-8 h-8 flex items-center justify-center cursor-pointer transition">
                                <i class="fa-solid fa-paperclip text-sm"></i>
                            </label>
                            <input type="file" name="attachment" id="widgetAttachmentInput" class="hidden"
                                accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx">

                            <input type="text" name="message" placeholder="Type a message..." autocomplete="off"
                                class="flex-1 border border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-500 rounded-full px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-primary" />
                            <button type="submit"
                                class="bg-primary hover:bg-primary-dark text-white w-8 h-8 rounded-full flex items-center justify-center transition">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                            </button>
                        </div>
                    </form>
                @else
                    <p class="text-xs text-gray-400 text-center">No admin available.</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Floating Notification Bubble for Admin/Staff --}}
    @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
        @php $adminUnread = \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count(); @endphp
        <div class="fixed bottom-6 right-6 z-50">
            <a href="{{ route('messages.index') }}"
                class="bg-accent hover:bg-accent-dark text-primary-dark w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition relative border-2 border-white">
                <i class="fa-solid fa-comments text-xl"></i>
                @if($adminUnread > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center animate-bounce">
                        {{ $adminUnread }}
                    </span>
                @endif
            </a>
        </div>
    @endif

    <script>
        function updateThemeIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            const moonIcon = document.getElementById('themeToggleIconMoon');
            const sunIcon = document.getElementById('themeToggleIconSun');
            if (moonIcon && sunIcon) {
                moonIcon.classList.toggle('hidden', isDark);
                sunIcon.classList.toggle('hidden', !isDark);
            }
        }

        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateThemeIcon();
        }

        document.addEventListener('DOMContentLoaded', updateThemeIcon);

        function closeAllNavGroups() {
            document.querySelectorAll('.nav-dropdown-panel').forEach(function (p) {
                p.classList.add('hidden');
            });
        }

        function toggleNotif() {
            const panel = document.getElementById('notifPanel');
            closeAllNavGroups();
            panel.classList.toggle('hidden');
            const badge = document.getElementById('notifBadge');
            if (badge) badge.classList.add('hidden');
        }

        function toggleNavGroup(groupId) {
            const panel = document.getElementById(groupId + 'Panel');
            const isHidden = panel.classList.contains('hidden');
            closeAllNavGroups();
            const notifPanel = document.getElementById('notifPanel');
            if (notifPanel) notifPanel.classList.add('hidden');
            if (isHidden) panel.classList.remove('hidden');
        }

        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('notifDropdown');
            const panel = document.getElementById('notifPanel');
            if (dropdown && !dropdown.contains(e.target)) {
                panel.classList.add('hidden');
            }

            document.querySelectorAll('.nav-dropdown').forEach(function (group) {
                if (!group.contains(e.target)) {
                    const groupPanel = group.querySelector('.nav-dropdown-panel');
                    if (groupPanel) groupPanel.classList.add('hidden');
                }
            });
        });

        function toggleChat() {
            const chatWindow = document.getElementById('chatWindow');
            const isHidden = chatWindow.classList.contains('hidden');
            chatWindow.classList.toggle('hidden');
            if (!isHidden) return;
            setTimeout(() => {
                const msgs = document.getElementById('chatMessages');
                if (msgs) msgs.scrollTop = msgs.scrollHeight;
            }, 100);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const msgs = document.getElementById('chatMessages');
            if (msgs) msgs.scrollTop = msgs.scrollHeight;
        });

        const widgetAttachmentInput = document.getElementById('widgetAttachmentInput');
        const widgetFilePreview = document.getElementById('widgetFilePreview');
        const widgetFileName = document.getElementById('widgetFileName');

        if (widgetAttachmentInput) {
            widgetAttachmentInput.addEventListener('change', function () {
                if (this.files.length > 0) {
                    widgetFileName.textContent = this.files[0].name;
                    widgetFilePreview.classList.remove('hidden');
                    widgetFilePreview.classList.add('flex');
                }
            });
        }

        function clearWidgetFile() {
            widgetAttachmentInput.value = '';
            widgetFilePreview.classList.add('hidden');
            widgetFilePreview.classList.remove('flex');
        }
    </script>

</body>

</html>