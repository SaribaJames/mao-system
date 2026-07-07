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
            @php $unreadMessages = \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count(); @endphp
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
            <div class="flex items-center gap-4">

                {{-- Notification Bell --}}
                @php
                    $notifications = collect();

                    if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff') {
                        $newFarmers = \App\Models\Farmer::whereDate('created_at', today())->count();
                        if($newFarmers > 0)
                            $notifications->push(['icon' => 'fa-person', 'color' => 'text-green-500', 'bg' => 'bg-green-100', 'text' => "{$newFarmers} new farmer(s) registered today", 'link' => route('farmers.index')]);

                        $pendingReqs = \App\Models\FarmerRequest::where('status', 'pending')->count();
                        if($pendingReqs > 0)
                            $notifications->push(['icon' => 'fa-file-lines', 'color' => 'text-yellow-500', 'bg' => 'bg-yellow-100', 'text' => "{$pendingReqs} pending request(s) need attention", 'link' => route('requests.index')]);

                        $newReqs = \App\Models\FarmerRequest::whereDate('created_at', today())->count();
                        if($newReqs > 0)
                            $notifications->push(['icon' => 'fa-file-circle-plus', 'color' => 'text-blue-500', 'bg' => 'bg-blue-100', 'text' => "{$newReqs} new request(s) submitted today", 'link' => route('requests.index')]);

                        $newServices = \App\Models\ServiceRecord::whereDate('created_at', today())->count();
                        if($newServices > 0)
                            $notifications->push(['icon' => 'fa-clipboard-list', 'color' => 'text-purple-500', 'bg' => 'bg-purple-100', 'text' => "{$newServices} service record(s) created today", 'link' => route('service-records.index')]);

                        $unreadMsgs = \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count();
                        if($unreadMsgs > 0)
                            $notifications->push(['icon' => 'fa-comments', 'color' => 'text-primary', 'bg' => 'bg-green-100', 'text' => "{$unreadMsgs} unread message(s)", 'link' => route('messages.index')]);

                        $pendingUsers = \App\Models\User::where('status', 'inactive')->whereHas('barangayAccount', fn($q) => $q->where('approval_status', 'pending'))->count();
                        if($pendingUsers > 0)
                            $notifications->push(['icon' => 'fa-user-clock', 'color' => 'text-orange-500', 'bg' => 'bg-orange-100', 'text' => "{$pendingUsers} pending user registration(s)", 'link' => route('users.index')]);

                    } elseif(Auth::user()->role?->name === 'barangay_user') {
                        $barangayId = Auth::user()->barangayAccount?->barangay_id;

                        $myPending = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->where('status', 'pending')->count();
                        if($myPending > 0)
                            $notifications->push(['icon' => 'fa-clock', 'color' => 'text-yellow-500', 'bg' => 'bg-yellow-100', 'text' => "{$myPending} request(s) still pending", 'link' => route('requests.index')]);

                        $myApproved = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->where('status', 'approved')->count();
                        if($myApproved > 0)
                            $notifications->push(['icon' => 'fa-circle-check', 'color' => 'text-green-500', 'bg' => 'bg-green-100', 'text' => "{$myApproved} request(s) approved", 'link' => route('requests.index')]);

                        $adminUser = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
                        if($adminUser) {
                            $unreadMsgs = \App\Models\Message::where('sender_id', $adminUser->id)->where('receiver_id', Auth::id())->where('is_read', false)->count();
                            if($unreadMsgs > 0)
                                $notifications->push(['icon' => 'fa-comments', 'color' => 'text-primary', 'bg' => 'bg-green-100', 'text' => "{$unreadMsgs} unread message(s) from MAO", 'link' => route('messages.chat')]);
                        }

                        $newActivities = \App\Models\Activity::whereDate('created_at', today())->where('status', 'active')->count();
                        if($newActivities > 0)
                            $notifications->push(['icon' => 'fa-bullhorn', 'color' => 'text-red-500', 'bg' => 'bg-red-100', 'text' => "{$newActivities} new announcement(s) posted today", 'link' => route('activities.index')]);
                    }
                @endphp

                <div class="relative" id="notifDropdown">
                    <button onclick="toggleNotif()"
                            class="relative text-gray-400 hover:text-gray-600 transition p-2 rounded-full hover:bg-gray-100">
                        <i class="fa-solid fa-bell text-lg"></i>
                        @if($notifications->count() > 0)
                        <span id="notifBadge" class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                            {{ $notifications->count() }}
                        </span>
                        @endif
                    </button>

                    {{-- Notification Dropdown --}}
                    <div id="notifPanel" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-800">Notifications</h3>
                            <span class="text-xs text-gray-400">{{ now()->format('M d, Y') }}</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse($notifications as $notif)
                            <a href="{{ $notif['link'] }}"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 transition">
                                <div class="{{ $notif['bg'] }} p-2 rounded-lg flex-shrink-0">
                                    <i class="fa-solid {{ $notif['icon'] }} {{ $notif['color'] }} text-sm"></i>
                                </div>
                                <p class="text-xs text-gray-700 mt-1">{{ $notif['text'] }}</p>
                            </a>
                            @empty
                            <div class="px-4 py-8 text-center">
                                <i class="fa-solid fa-bell-slash text-gray-300 text-2xl mb-2"></i>
                                <p class="text-xs text-gray-400">No new notifications</p>
                            </div>
                            @endforelse
                        </div>
                        <div class="px-4 py-2 border-t border-gray-100 bg-gray-50">
                            <p class="text-xs text-gray-400 text-center">{{ $notifications->count() }} notification(s)</p>
                        </div>
                    </div>
                </div>

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

    {{-- Floating Chat Bubble for Barangay Reps --}}
    @if(Auth::user()->role?->name === 'barangay_user')
    @php
        $adminUser = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        $unreadFromAdmin = $adminUser ? \App\Models\Message::where('sender_id', $adminUser->id)->where('receiver_id', Auth::id())->where('is_read', false)->count() : 0;
        $chatMessages = $adminUser ? \App\Models\Message::where(function($q) use ($adminUser) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $adminUser->id);
        })->orWhere(function($q) use ($adminUser) {
            $q->where('sender_id', $adminUser->id)->where('receiver_id', Auth::id());
        })->orderBy('created_at')->take(50)->get() : collect();
    @endphp

    <div id="chatBubble" class="fixed bottom-6 right-6 z-50">
        <button onclick="toggleChat()"
                class="bg-primary hover:bg-primary-dark text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition relative">
            <i class="fa-solid fa-comments text-xl"></i>
            @if($unreadFromAdmin > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                {{ $unreadFromAdmin }}
            </span>
            @endif
        </button>
    </div>

    <div id="chatWindow" class="fixed bottom-24 right-6 z-50 hidden w-80 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
        <div class="bg-primary px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                    <i class="fa-solid fa-headset text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-white text-sm font-semibold">MAO Support</p>
                    <p class="text-white text-xs opacity-75">Municipal Agriculture Office</p>
                </div>
            </div>
            <button onclick="toggleChat()" class="text-white hover:text-gray-200">
                <i class="fa-solid fa-x text-sm"></i>
            </button>
        </div>

        <div class="h-64 overflow-y-auto p-4 space-y-3 bg-gray-50" id="chatMessages">
            @forelse($chatMessages as $msg)
            @php $isMine = $msg->sender_id === Auth::id(); @endphp
            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-48">
                    <div class="px-3 py-2 rounded-2xl text-xs
                        {{ $isMine ? 'bg-primary text-white rounded-br-sm' : 'bg-white text-gray-800 rounded-bl-sm shadow-sm' }}">
                        {{ $msg->message }}
                    </div>
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

        <div class="p-3 border-t border-gray-100 bg-white">
            @if($adminUser)
            <form method="POST" action="{{ route('messages.send') }}" class="flex gap-2">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $adminUser->id }}">
                <input type="text" name="message" required placeholder="Type a message..." autocomplete="off"
                       class="flex-1 border border-gray-200 rounded-full px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-primary"/>
                <button type="submit"
                        class="bg-primary hover:bg-primary-dark text-white w-8 h-8 rounded-full flex items-center justify-center transition">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
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
           class="bg-primary hover:bg-primary-dark text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition relative">
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
    function toggleNotif() {
        const panel = document.getElementById('notifPanel');
        panel.classList.toggle('hidden');
        const badge = document.getElementById('notifBadge');
        if (badge) badge.classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notifDropdown');
        const panel = document.getElementById('notifPanel');
        if (dropdown && !dropdown.contains(e.target)) {
            panel.classList.add('hidden');
        }
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

    document.addEventListener('DOMContentLoaded', function() {
        const msgs = document.getElementById('chatMessages');
        if (msgs) msgs.scrollTop = msgs.scrollHeight;
    });
    </script>

</body>
</html>