@extends('layouts.app')

@section('content')

    @php
        // Get event dates for calendar highlighting
        $eventDates = \App\Models\Activity::whereNotNull('event_date')
            ->where('status', 'active')
            ->whereMonth('event_date', now()->month)
            ->whereYear('event_date', now()->year)
            ->pluck('event_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
            ->toArray();
    @endphp

    <div class="mb-6 relative h-64 rounded border border-gray-300 overflow-hidden">
        <img src="{{ asset('images/guinobatan-plaza.jpg') }}" alt="Guinobatan Town Plaza"
            class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-6">
            <h2 class="text-2xl font-bold text-white">Dashboard</h2>
            <p class="text-white/90 text-sm mt-1">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- Left & Center: Main Content (2/3) --}}
            <div class="col-span-1 lg:col-span-2 space-y-4">

                {{-- Stat Cards --}}
                @if(Auth::user()->role?->name === 'barangay_user')
                    @php
                        $barangayId = Auth::user()->barangayAccount?->barangay_id;
                        $myRequests = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->count();
                        $myPending = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->where('status', 'pending')->count();
                        $myApproved = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->where('status', 'approved')->count();
                        $myCompleted = \App\Models\FarmerRequest::whereHas('farmer', fn($q) => $q->where('barangay_id', $barangayId))->where('status', 'completed')->whereMonth('updated_at', now()->month)->count();
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white rounded p-5 border border-gray-300">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-500">My Submitted Requests</p>
                                <div class="bg-green-100 p-2 rounded border border-green-200">
                                    <i class="fa-solid fa-file-lines text-primary text-sm"></i>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-gray-800">{{ $myRequests }}</p>
                        </div>
                        <div class="bg-white rounded p-5 border border-gray-300">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-500">Pending Requests</p>
                                <div class="bg-yellow-100 p-2 rounded border border-yellow-200">
                                    <i class="fa-solid fa-clock text-yellow-600 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-yellow-600">{{ $myPending }}</p>
                        </div>
                        <div class="bg-white rounded p-5 border border-gray-300">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-500">Approved Requests</p>
                                <div class="bg-blue-100 p-2 rounded border border-blue-200">
                                    <i class="fa-solid fa-circle-check text-blue-600 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-primary">{{ $myApproved }}</p>
                        </div>
                        <div class="bg-white rounded p-5 border border-gray-300">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-500">Completed This Month</p>
                                <div class="bg-green-100 p-2 rounded border border-green-200">
                                    <i class="fa-solid fa-check-double text-primary text-sm"></i>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-blue-600">{{ $myCompleted }}</p>
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
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-white rounded p-5 border border-gray-300">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-500">Total Farmers</p>
                                <div class="bg-green-100 p-2 rounded border border-green-200">
                                    <i class="fa-solid fa-person text-primary text-sm"></i>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-gray-800">{{ $totalFarmers }}</p>
                            <p class="text-xs text-green-600 mt-1">+{{ $thisMonthFarmers }} this month</p>
                        </div>
                        <div class="bg-white rounded p-5 border border-gray-300">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-500">Pending Requests</p>
                                <div class="bg-yellow-100 p-2 rounded border border-yellow-200">
                                    <i class="fa-solid fa-clock text-yellow-600 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-yellow-600">{{ $pendingRequests }}</p>
                            <p class="text-xs text-gray-400 mt-1">Needs attention</p>
                        </div>
                        <div class="bg-white rounded p-5 border border-gray-300">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-500">Completed Requests</p>
                                <div class="bg-blue-100 p-2 rounded border border-blue-200">
                                    <i class="fa-solid fa-circle-check text-blue-600 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-primary">{{ $completedTrans }}</p>
                            <p class="text-xs text-gray-400 mt-1">All time</p>
                        </div>
                        <div class="bg-white rounded p-5 border border-gray-300">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-500">Available Stocks</p>
                                <div class="bg-orange-100 p-2 rounded border border-orange-200">
                                    <i class="fa-solid fa-boxes-stacked text-orange-600 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-orange-600">{{ number_format($availableStocks, 0) }}</p>
                            <p class="text-xs text-gray-400 mt-1">Units remaining</p>
                        </div>
                        <div class="bg-white rounded p-5 border border-gray-300">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-500">Total Services</p>
                                <div class="bg-purple-100 p-2 rounded border border-purple-200">
                                    <i class="fa-solid fa-clipboard-list text-purple-600 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-purple-600">{{ $totalServices }}</p>
                            <p class="text-xs text-gray-400 mt-1">Rendered</p>
                        </div>
                        <div class="bg-white rounded p-5 border border-gray-300">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-500">Announcements</p>
                                <div class="bg-red-100 p-2 rounded border border-red-200">
                                    <i class="fa-solid fa-bullhorn text-red-600 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-red-600">
                                {{ \App\Models\Activity::where('status', 'active')->count() }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">Active posts</p>
                        </div>
                    </div>
                @endif

                {{-- Recent Activities --}}
                <div class="bg-white rounded border border-gray-300 p-5">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-800">Recent Activities</h3>
                        <a href="{{ route('requests.index') }}" class="text-xs text-primary hover:underline">View all →</a>
                    </div>
                    @php
                        $recentRequests = \App\Models\FarmerRequest::with('farmer')->latest()->take(5)->get();
                    @endphp
                    @forelse($recentRequests as $req)
                                <div class="flex items-center justify-between py-2.5 border-b border-gray-100 last:border-0">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full
                                                                                        {{ $req->status === 'pending' ? 'bg-yellow-500' :
                        ($req->status === 'approved' ? 'bg-blue-500' :
                            ($req->status === 'completed' ? 'bg-green-600' : 'bg-red-500')) }}">
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-700 font-medium">
                                                {{ $req->request_type_label ?? ucfirst(str_replace('_', ' ', $req->request_type)) }}
                                            </p>
                                            <p class="text-xs text-gray-400">{{ $req->farmer->first_name }} {{ $req->farmer->surname }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="text-xs px-2 py-0.5 rounded font-medium border
                                                                                        {{ $req->status === 'pending' ? 'bg-yellow-100 text-yellow-700 border-yellow-200' :
                        ($req->status === 'approved' ? 'bg-blue-100 text-blue-700 border-blue-200' :
                            ($req->status === 'completed' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200')) }}">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ \Carbon\Carbon::parse($req->created_at)->format('M d') }}
                                        </p>
                                    </div>
                                </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No recent activities yet.</p>
                    @endforelse
                </div>

                {{-- Latest Announcements --}}
                @php
                    $latestAnnouncements = \App\Models\Activity::where('type', 'announcement')
                        ->where('status', 'active')->latest()->take(3)->get();
                @endphp
                @if($latestAnnouncements->count() > 0)
                    <div class="bg-white rounded border border-gray-300 p-5">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                            <h3 class="text-base font-semibold text-gray-800">Latest Announcements</h3>
                            <a href="{{ route('activities.index') }}" class="text-xs text-primary hover:underline">View all
                                →</a>
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
                                <div class="flex gap-3 p-3 rounded bg-gray-50 border border-gray-200">
                                    <div class="w-1 rounded-full {{ $annColor }} flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $ann->title }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $ann->content }}</p>
                                    </div>
                                    <span class="text-xs text-gray-400 flex-shrink-0">{{ $ann->created_at->format('M d') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- Right: Clock + Calendar --}}
            <div class="col-span-1 space-y-4">

                {{-- Live Clock --}}
                <div class="bg-primary rounded p-5 text-white text-center border border-primary-dark">
                    <p class="text-xs font-medium opacity-70 mb-1 uppercase tracking-widest">Current Time</p>
                    <div id="liveClock" class="text-4xl font-bold tracking-tight mb-1">00:00:00</div>
                    <div id="liveDate" class="text-sm opacity-80"></div>
                    <div class="mt-3 pt-3 border-t border-white border-opacity-20">
                        <p class="text-xs opacity-60">Guinobatan, Albay</p>
                    </div>
                </div>

                {{-- Calendar --}}
                <div class="bg-white rounded border border-gray-300 p-5">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                        <button onclick="prevMonth()" class="text-gray-500 hover:text-gray-700 transition">
                            <i class="fa-solid fa-chevron-left text-sm"></i>
                        </button>
                        <h3 id="calendarTitle" class="text-sm font-bold text-gray-800"></h3>
                        <button onclick="nextMonth()" class="text-gray-500 hover:text-gray-700 transition">
                            <i class="fa-solid fa-chevron-right text-sm"></i>
                        </button>
                    </div>

                    {{-- Day Labels --}}
                    <div class="grid grid-cols-7 mb-2">
                        @foreach(['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $day)
                            <div class="text-center text-xs font-medium text-gray-500 py-1">{{ $day }}</div>
                        @endforeach
                    </div>

                    {{-- Calendar Grid --}}
                    <div id="calendarGrid" class="grid grid-cols-7 gap-y-1"></div>

                    {{-- Legend --}}
                    <div class="mt-4 pt-3 border-t border-gray-200 space-y-1.5">
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <div class="w-3 h-3 rounded-full bg-primary"></div>
                            <span>Today</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <span>Regular Holiday</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <div class="w-3 h-3 rounded-full bg-orange-200"></div>
                            <span>Special Non-Working Day</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <div class="w-3 h-3 rounded-full bg-red-100"></div>
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
                    <div class="bg-white rounded border border-gray-300 p-5">
                        <h3 class="text-sm font-bold text-gray-800 mb-3 pb-3 border-b border-gray-200">Upcoming Events</h3>
                        <div class="space-y-2">
                            @foreach($upcomingEvents as $event)
                                <div class="flex gap-3 items-start">
                                    <div
                                        class="bg-primary-light rounded p-2 text-center min-w-10 flex-shrink-0 border border-primary/20">
                                        <p class="text-xs font-bold text-primary">{{ $event->event_date->format('d') }}</p>
                                        <p class="text-xs text-primary opacity-70">{{ $event->event_date->format('M') }}</p>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $event->title }}</p>
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
            // Official Philippine Holidays 2026 (Proclamation No. 1006)
            // Official Philippine Holidays 2026
            const regularHolidays = {
                '2026-01-01': "New Year's Day",
                '2026-04-02': 'Maundy Thursday',
                '2026-04-03': 'Good Friday',
                '2026-04-09': 'Araw ng Kagitingan',
                '2026-05-01': 'Labor Day',
                '2026-05-27': "Eid'l Adha",
                '2026-06-12': 'Independence Day',
                '2026-08-31': 'National Heroes Day',
                '2026-11-30': 'Bonifacio Day',
                '2026-12-25': 'Christmas Day',
                '2026-12-30': 'Rizal Day',
            };

            const specialHolidays = {
                '2026-02-17': 'Chinese New Year',
                '2026-04-04': 'Black Saturday',
                '2026-08-21': 'Ninoy Aquino Day',
                '2026-11-01': "All Saints' Day",
                '2026-11-02': "All Souls' Day",
                '2026-12-08': 'Feast of the Immaculate Conception',
                '2026-12-24': 'Christmas Eve',
                '2026-12-31': 'Last Day of the Year',
            };
            // Live Clock
            function updateClock() {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const s = String(now.getSeconds()).padStart(2, '0');
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

                document.getElementById('liveClock').textContent = `${h}:${m}:${s}`;
                document.getElementById('liveDate').textContent =
                    `${days[now.getDay()]}, ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`;
            }
            setInterval(updateClock, 1000);
            updateClock();

            // Calendar
            const eventDates = @json($eventDates);
            let currentYear = new Date().getFullYear();
            let currentMonth = new Date().getMonth();

            function renderCalendar() {
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                document.getElementById('calendarTitle').textContent = `${monthNames[currentMonth]} ${currentYear}`;

                const grid = document.getElementById('calendarGrid');
                grid.innerHTML = '';

                const firstDay = new Date(currentYear, currentMonth, 1).getDay();
                const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
                const today = new Date();

                // Empty cells
                for (let i = 0; i < firstDay; i++) {
                    const cell = document.createElement('div');
                    grid.appendChild(cell);
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const cell = document.createElement('div');
                    const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const isToday = today.getDate() === day && today.getMonth() === currentMonth && today.getFullYear() === currentYear;
                    const hasEvent = eventDates.includes(dateStr);
                    const regularHolidayName = regularHolidays[dateStr];
                    const specialHolidayName = specialHolidays[dateStr];

                    cell.className = 'relative text-center py-1 text-xs rounded-full cursor-default transition';

                    if (isToday) {
                        cell.className += ' bg-primary text-white font-bold';
                        cell.title = 'Today';
                    } else if (hasEvent) {
                        cell.className += ' bg-red-100 text-red-700 font-semibold';
                        cell.title = 'Scheduled Event/Activity';
                    } else if (regularHolidayName) {
                        cell.className += ' bg-red-500 text-white font-semibold cursor-pointer';
                        cell.title = '🇵🇭 ' + regularHolidayName + ' (Regular Holiday)';
                    } else if (specialHolidayName) {
                        cell.className += ' bg-orange-200 text-orange-700 font-semibold cursor-pointer';
                        cell.title = '📅 ' + specialHolidayName + ' (Special Non-Working Day)';
                    } else {
                        cell.className += ' text-gray-600 hover:bg-gray-100';
                    }

                    cell.textContent = day;
                    grid.appendChild(cell);
                }
            }

            function prevMonth() {
                currentMonth--;
                if (currentMonth < 0) { currentMonth = 11; currentYear--; }
                renderCalendar();
            }

            function nextMonth() {
                currentMonth++;
                if (currentMonth > 11) { currentMonth = 0; currentYear++; }
                renderCalendar();
            }

            renderCalendar();
        </script>

@endsection