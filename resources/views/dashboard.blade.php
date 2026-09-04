@extends('layouts.app')

@section('content')

    @php
        $eventDates = \App\Models\Activity::whereNotNull('event_date')
            ->where('status', 'active')
            ->whereMonth('event_date', now()->month)
            ->whereYear('event_date', now()->year)
            ->pluck('event_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
            ->toArray();
    @endphp

    {{-- Hero banner --}}
    <div class="mb-8 relative h-48 rounded-xl overflow-hidden">
        <img src="{{ asset('images/guinobatan-plaza.jpg') }}" alt="Guinobatan Town Plaza"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-primary-dark/90 via-primary-dark/60 to-transparent"></div>
        <div class="absolute inset-0 dark:bg-black/40"></div>
        <div class="absolute inset-0 flex flex-col justify-center px-8">
            <h2 class="text-4xl font-extrabold text-white tracking-tight">Dashboard</h2>
            <p class="text-green-50 text-sm mt-1.5 font-medium">
                @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'superadmin')
                    System monitoring and overview
                @elseif(Auth::user()->role?->name === 'barangay_user')
                    Your submitted requests and updates
                @else
                    Overview of MAO operations
                @endif
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left & Center --}}
        <div class="col-span-1 lg:col-span-2 space-y-5">

            @if(Auth::user()->role?->name === 'barangay_user')
                @php
                    $barangayId = Auth::user()->barangayAccount?->barangay_id;
                    $myRequests = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->count();
                    $myPending = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->where('status', 'pending')->count();
                    $myApproved = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->where('status', 'approved')->count();
                    $myCompleted = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->where('status', 'completed')->whereMonth('updated_at', now()->month)->count();
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border-l-4 border-primary shadow-sm">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">My Submitted Requests</p>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ $myRequests }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border-l-4 border-accent shadow-sm">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Pending Requests</p>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ $myPending }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border-l-4 border-blue-500 shadow-sm">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Approved Requests</p>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ $myApproved }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border-l-4 border-primary shadow-sm">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Completed This Month</p>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ $myCompleted }}</p>
                    </div>
                </div>
            @else
                @php
                    $totalFarmers = \App\Models\Farmer::count();
                    $pendingRequests = \App\Models\FarmerRequest::where('status', 'pending')->count();
                    $completedTrans = \App\Models\FarmerRequest::where('status', 'completed')->count();
                    $availableStocks = \App\Models\Stock::sum('remaining_stock');
                    $totalServices = \App\Models\ServiceRecord::count();
                    $thisMonthFarmers = \App\Models\Farmer::whereMonth('created_at', now()->month)->count();
                @endphp
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-5">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border-l-4 border-primary shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Farmers</p>
                            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center">
                                <i class="fa-solid fa-person text-primary text-sm"></i>
                            </div>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ $totalFarmers }}</p>
                        <p class="text-xs text-primary font-semibold mt-1">+{{ $thisMonthFarmers }} this month</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border-l-4 border-accent shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pending Requests</p>
                            <div class="w-9 h-9 rounded-full bg-accent/10 flex items-center justify-center">
                                <i class="fa-solid fa-clock text-accent text-sm"></i>
                            </div>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ $pendingRequests }}</p>
                        <p class="text-xs text-gray-400 font-medium mt-1">Needs attention</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border-l-4 border-blue-500 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Completed Requests</p>
                            <div class="w-9 h-9 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-circle-check text-blue-500 text-sm"></i>
                            </div>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ $completedTrans }}</p>
                        <p class="text-xs text-gray-400 font-medium mt-1">All time</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border-l-4 border-orange-500 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Available Stocks</p>
                            <div class="w-9 h-9 rounded-full bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-boxes-stacked text-orange-500 text-sm"></i>
                            </div>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ number_format($availableStocks, 0) }}</p>
                        <p class="text-xs text-gray-400 font-medium mt-1">Units remaining</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border-l-4 border-purple-500 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Services</p>
                            <div class="w-9 h-9 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-clipboard-list text-purple-500 text-sm"></i>
                            </div>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ $totalServices }}</p>
                        <p class="text-xs text-gray-400 font-medium mt-1">Rendered</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border-l-4 border-red-500 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Announcements</p>
                            <div class="w-9 h-9 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-bullhorn text-red-500 text-sm"></i>
                            </div>
                        </div>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">
                            {{ \App\Models\Activity::where('status', 'active')->count() }}
                        </p>
                        <p class="text-xs text-gray-400 font-medium mt-1">Active posts</p>
                    </div>
                </div>
            @endif

            {{-- Recent Activities --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Recent Activities</h3>
                    <a href="{{ route('requests.index') }}" class="text-xs font-semibold text-primary hover:text-primary-dark">View all →</a>
                </div>
                @php
                    $recentRequestsQuery = \App\Models\FarmerRequest::with('farmer')->latest();
                    if (Auth::user()->role?->name === 'barangay_user') {
                        $recentRequestsQuery->whereHas('farmer', fn($q) => $q->where('barangay_id', Auth::user()->barangayAccount?->barangay_id));
                    }
                    $recentRequests = $recentRequestsQuery->take(5)->get();
                @endphp
                @forelse($recentRequests as $req)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-2.5 h-2.5 rounded-full flex-shrink-0
                                    {{ $req->status === 'pending' ? 'bg-accent' :
                    ($req->status === 'approved' ? 'bg-blue-500' :
                        ($req->status === 'completed' ? 'bg-primary' : 'bg-red-400')) }}">
                                </div>
                                <div>
                                    <p class="text-sm text-gray-800 dark:text-gray-100 font-semibold">
                                        {{ $req->request_type_label ?? ucfirst(str_replace('_', ' ', $req->request_type)) }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $req->farmer->first_name }} {{ $req->farmer->surname }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                    {{ $req->status === 'pending' ? 'bg-accent/10 text-accent' :
                    ($req->status === 'approved' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300' :
                        ($req->status === 'completed' ? 'bg-primary/10 text-primary' : 'bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-300')) }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                                <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($req->created_at)->format('M d') }}</p>
                            </div>
                        </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6">No recent activities yet.</p>
                @endforelse
            </div>

            {{-- Latest Announcements --}}
            @php
                $latestAnnouncements = \App\Models\Activity::where('type', 'announcement')
                    ->where('status', 'active')->latest()->take(3)->get();
            @endphp
            @if($latestAnnouncements->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Latest Announcements</h3>
                        <a href="{{ route('activities.index') }}" class="text-xs font-semibold text-primary hover:text-primary-dark">View all →</a>
                    </div>
                    <div class="space-y-3">
                        @foreach($latestAnnouncements as $ann)
                            @php
                                $annColor = match ($ann->priority) {
                                    'high' => 'bg-red-500',
                                    'normal' => 'bg-primary',
                                    'low' => 'bg-blue-400',
                                    default => 'bg-gray-400',
                                };
                            @endphp
                            <div class="flex gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-900">
                                <div class="w-1 rounded-full {{ $annColor }} flex-shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $ann->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1">{{ $ann->content }}</p>
                                </div>
                                <span class="text-xs text-gray-400 flex-shrink-0">{{ $ann->created_at->format('M d') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Right: Clock + Calendar --}}
        <div class="col-span-1 space-y-5">

            {{-- Live Clock --}}
            <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl p-6 text-white text-center shadow-sm">
                <p class="text-xs font-semibold opacity-75 mb-1.5 uppercase tracking-widest">Current Time</p>
                <div id="liveClock" class="text-4xl font-extrabold tracking-tight mb-1">00:00:00</div>
                <div id="liveDate" class="text-sm opacity-85 font-medium"></div>
                <div class="mt-4 pt-4 border-t border-white/20">
                    <p class="text-xs opacity-70 font-medium">
                        <i class="fa-solid fa-location-dot mr-1"></i>Guinobatan, Albay
                    </p>
                </div>
            </div>

            {{-- Calendar --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <button onclick="prevMonth()" class="w-7 h-7 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <h3 id="calendarTitle" class="text-sm font-bold text-gray-900"></h3>
                    <button onclick="nextMonth()" class="w-7 h-7 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>

                <div class="grid grid-cols-7 mb-1">
                    @foreach(['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $day)
                        <div class="text-center text-xs font-semibold text-gray-400 py-1">{{ $day }}</div>
                    @endforeach
                </div>

                <div id="calendarGrid" class="grid grid-cols-7 gap-y-1"></div>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-2">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                        <div class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                        <span>Today</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                        <span>Regular Holiday</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                        <div class="w-2.5 h-2.5 rounded-full bg-accent"></div>
                        <span>Special Non-Working Day</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-200"></div>
                        <span>Scheduled Event/Activity</span>
                    </div>
                </div>
            </div>

            {{-- Upcoming Events --}}
            @php
                $upcomingEvents = \App\Models\Activity::whereNotNull('event_date')
                    ->where('status', 'active')
                    ->where('event_date', '>=', now()->toDateString())
                    ->orderBy('event_date')
                    ->take(4)
                    ->get();
            @endphp
            @if($upcomingEvents->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">Upcoming Events</h3>
                    <div class="space-y-3">
                        @foreach($upcomingEvents as $event)
                            <div class="flex gap-3 items-start">
                                <div class="bg-primary/10 rounded-lg p-2 text-center min-w-11 flex-shrink-0">
                                    <p class="text-xs font-bold text-primary">{{ $event->event_date->format('d') }}</p>
                                    <p class="text-xs text-primary/70">{{ $event->event_date->format('M') }}</p>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $event->title }}</p>
                                    @if($event->location)
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            <i class="fa-solid fa-location-dot mr-1"></i>{{ $event->location }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        const regularHolidays = {
            '2026-01-01': "New Year's Day", '2026-04-02': 'Maundy Thursday', '2026-04-03': 'Good Friday',
            '2026-04-09': 'Araw ng Kagitingan', '2026-05-01': 'Labor Day', '2026-05-27': "Eid'l Adha",
            '2026-06-12': 'Independence Day', '2026-08-31': 'National Heroes Day', '2026-11-30': 'Bonifacio Day',
            '2026-12-25': 'Christmas Day', '2026-12-30': 'Rizal Day',
        };
        const specialHolidays = {
            '2026-02-17': 'Chinese New Year', '2026-04-04': 'Black Saturday', '2026-08-21': 'Ninoy Aquino Day',
            '2026-11-01': "All Saints' Day", '2026-11-02': "All Souls' Day",
            '2026-12-08': 'Feast of the Immaculate Conception', '2026-12-24': 'Christmas Eve', '2026-12-31': 'Last Day of the Year',
        };

        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
            const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            document.getElementById('liveClock').textContent = `${h}:${m}:${s}`;
            document.getElementById('liveDate').textContent = `${days[now.getDay()]}, ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        const eventDates = @json($eventDates);
        let currentYear = new Date().getFullYear();
        let currentMonth = new Date().getMonth();

        function renderCalendar() {
            const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            document.getElementById('calendarTitle').textContent = `${monthNames[currentMonth]} ${currentYear}`;
            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '';
            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            const today = new Date();

            for (let i = 0; i < firstDay; i++) grid.appendChild(document.createElement('div'));

            for (let day = 1; day <= daysInMonth; day++) {
                const cell = document.createElement('div');
                const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const isToday = today.getDate() === day && today.getMonth() === currentMonth && today.getFullYear() === currentYear;
                const hasEvent = eventDates.includes(dateStr);
                const regularHolidayName = regularHolidays[dateStr];
                const specialHolidayName = specialHolidays[dateStr];

                cell.className = 'relative text-center py-1.5 text-xs rounded-full cursor-default transition font-medium';

                if (isToday) {
                    cell.className += ' bg-primary text-white font-bold';
                    cell.title = 'Today';
                } else if (hasEvent) {
                    cell.className += ' bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 font-semibold';
                    cell.title = 'Scheduled Event/Activity';
                } else if (regularHolidayName) {
                    cell.className += ' bg-red-500 text-white font-semibold cursor-pointer';
                    cell.title = '🇵🇭 ' + regularHolidayName + ' (Regular Holiday)';
                } else if (specialHolidayName) {
                    cell.className += ' bg-accent text-white font-semibold cursor-pointer';
                    cell.title = '📅 ' + specialHolidayName + ' (Special Non-Working Day)';
                } else {
                    cell.className += ' text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
                }

                cell.textContent = day;
                grid.appendChild(cell);
            }
        }

        function prevMonth() { currentMonth--; if (currentMonth < 0) { currentMonth = 11; currentYear--; } renderCalendar(); }
        function nextMonth() { currentMonth++; if (currentMonth > 11) { currentMonth = 0; currentYear++; } renderCalendar(); }
        renderCalendar();
    </script>

@endsection