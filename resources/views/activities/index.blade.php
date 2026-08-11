@extends('layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Activities</h2>
        <p class="text-gray-500 text-sm mt-1">Announcements, schedules, and upcoming events</p>
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
    <button onclick="openAddModal()"
            class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
        <i class="fa-solid fa-plus"></i> Add New
    </button>
    @endif
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Tabs --}}
<div class="flex gap-2 mb-6">
    <a href="{{ route('activities.index', ['type' => 'announcement']) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition
              {{ $type === 'announcement' ? 'bg-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
        <i class="fa-solid fa-bullhorn"></i>
        Announcements
        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $type === 'announcement' ? 'bg-white bg-opacity-30 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $announcements }}</span>
    </a>
    <a href="{{ route('activities.index', ['type' => 'training']) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition
              {{ $type === 'training' ? 'bg-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
        <i class="fa-solid fa-chalkboard-user"></i>
        Training Programs
        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $type === 'training' ? 'bg-white bg-opacity-30 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $trainings }}</span>
    </a>
    <a href="{{ route('activities.index', ['type' => 'event']) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition
              {{ $type === 'event' ? 'bg-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
        <i class="fa-solid fa-calendar-days"></i>
        Events
        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $type === 'event' ? 'bg-white bg-opacity-30 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $events }}</span>
    </a>
</div>

{{-- Cards Grid --}}
@if($activities->count() > 0)
<div class="grid grid-cols-3 gap-4 mb-6">
    @foreach($activities as $activity)
    @php
        $priorityBg = match($activity->priority) {
            'high'   => 'bg-red-500',
            'normal' => 'bg-primary',
            'low'    => 'bg-blue-400',
            default  => 'bg-gray-400',
        };
        $icon = match($type) {
            'announcement' => 'fa-bullhorn',
            'training'     => 'fa-chalkboard-user',
            'event'        => 'fa-calendar-days',
            default        => 'fa-bell',
        };
        $actId       = $activity->id;
        $actTitle    = e($activity->title);
        $actContent  = e($activity->content);
        $actPriority = ucfirst($activity->priority);
        $actDate     = $activity->event_date ? $activity->event_date->format('F d, Y') : '';
        $actLocation = e($activity->location ?? '');
        $actPosted   = $activity->created_at->format('F j, Y');
        $actAuthor   = e($activity->createdBy?->name ?? 'System');
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition cursor-pointer"
         onclick="openViewModal({{ $actId }})">

        {{-- Card Top Banner --}}
        <div class="{{ $priorityBg }} p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                    <i class="fa-solid {{ $icon }} text-lg"></i>
                </div>
                <span class="text-xs font-medium bg-white bg-opacity-20 px-2 py-1 rounded-full">
                    {{ $actPriority }} Priority
                </span>
            </div>
            <h3 class="text-base font-bold mt-3 leading-tight">{{ $activity->title }}</h3>
        </div>

        {{-- Card Body --}}
        <div class="p-4">
            <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                {{ $activity->content }}
            </p>

            @if($activity->event_date)
            <div class="flex items-center gap-1.5 mt-3 text-primary text-xs font-medium">
                <i class="fa-solid fa-calendar"></i>
                {{ $activity->event_date->format('F d, Y') }}
                @if($activity->location)
                    <span class="text-gray-400 mx-1">•</span>
                    <i class="fa-solid fa-location-dot"></i>
                    {{ $activity->location }}
                @endif
            </div>
            @endif

            <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-50">
                <div class="text-xs text-gray-400">
                    <span>{{ $activity->created_at->format('M j, Y') }}</span>
                    <span class="mx-1">•</span>
                    <span>{{ $activity->createdBy?->name ?? 'System' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-primary font-medium">Read more →</span>
                    @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
                    <form method="POST" action="{{ route('activities.destroy', $activity) }}"
                          onsubmit="return confirm('Delete this post?')" onclick="event.stopPropagation()">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs ml-1">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Hidden data for modal --}}
        <div id="activity-data-{{ $actId }}" class="hidden"
             data-title="{{ $actTitle }}"
             data-content="{{ $actContent }}"
             data-priority="{{ $actPriority }}"
             data-date="{{ $actDate }}"
             data-location="{{ $actLocation }}"
             data-posted="{{ $actPosted }}"
             data-author="{{ $actAuthor }}">
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if($activities->hasPages())
<div class="mb-4">{{ $activities->links() }}</div>
@endif

@else
<div class="bg-white rounded-xl border border-gray-100 p-16 text-center">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fa-solid {{ $type === 'announcement' ? 'fa-bullhorn' : ($type === 'training' ? 'fa-chalkboard-user' : 'fa-calendar-days') }} text-gray-400 text-2xl"></i>
    </div>
    <h3 class="text-base font-semibold text-gray-600 mb-1">No {{ ucfirst($type) }}s Yet</h3>
    <p class="text-sm text-gray-400">No {{ $type }}s have been posted yet.</p>
    @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
    <button onclick="openAddModal()"
            class="mt-4 bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition">
        + Add First {{ ucfirst($type) }}
    </button>
    @endif
</div>
@endif

{{-- View Full Modal --}}
<div id="viewModal" style="display:none;" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div id="viewModalBanner" class="bg-primary p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <span id="viewModalPriority" class="text-xs font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full"></span>
                <button onclick="closeViewModal()" class="text-white hover:text-gray-200">
                    <i class="fa-solid fa-x"></i>
                </button>
            </div>
            <h2 id="viewModalTitle" class="text-xl font-bold leading-tight"></h2>
        </div>
        <div class="p-6">
            <p id="viewModalContent" class="text-gray-700 text-sm leading-relaxed mb-4"></p>
            <div id="viewModalDate" class="text-xs text-primary font-medium mb-1 hidden">
                <i class="fa-solid fa-calendar mr-1"></i>
                <span id="viewModalDateText"></span>
            </div>
            <div id="viewModalLocation" class="text-xs text-gray-500 mb-4 hidden">
                <i class="fa-solid fa-location-dot mr-1"></i>
                <span id="viewModalLocationText"></span>
            </div>
            <div class="border-t border-gray-100 pt-3 text-xs text-gray-400">
                <span>Posted: </span><span id="viewModalPostedDate"></span>
                <span class="mx-1">•</span>
                <span>By: </span><span id="viewModalAuthor"></span>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
@if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
<div id="addModal" style="display:none;" class="fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-800">Add New Post</h3>
            <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>
        <form id="activityForm">
            @csrf
            <input type="hidden" name="_token" id="csrfToken" value="{{ csrf_token() }}">
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                        <select name="type" id="typeSelect" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="announcement">Announcement</option>
                            <option value="training">Training Program</option>
                            <option value="event">Event</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Priority</label>
                        <select name="priority" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                    <input type="text" name="title" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Content</label>
                    <textarea name="body" rows="4" required
                              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Date (optional)</label>
                        <input type="date" name="event_date"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Location (optional)</label>
                        <input type="text" name="location"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="submit"
                        class="flex-1 bg-primary hover:bg-primary-dark text-white font-semibold py-2 rounded-md transition text-sm">
                    Post
                </button>
                <button type="button" onclick="closeAddModal()"
                        class="flex-1 border border-gray-300 text-gray-600 font-medium py-2 rounded-md transition text-sm hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function openViewModal(id) {
    const el       = document.getElementById('activity-data-' + id);
    const title    = el.getAttribute('data-title');
    const content  = el.getAttribute('data-content');
    const priority = el.getAttribute('data-priority');
    const date     = el.getAttribute('data-date');
    const location = el.getAttribute('data-location');
    const posted   = el.getAttribute('data-posted');
    const author   = el.getAttribute('data-author');

    document.getElementById('viewModalTitle').textContent   = title;
    document.getElementById('viewModalContent').textContent = content;
    document.getElementById('viewModalPriority').textContent = priority + ' Priority';
    document.getElementById('viewModalPostedDate').textContent = posted;
    document.getElementById('viewModalAuthor').textContent  = author;

    const banner = document.getElementById('viewModalBanner');
    if (priority === 'High') banner.className = 'bg-red-500 p-6 text-white';
    else if (priority === 'Low') banner.className = 'bg-blue-400 p-6 text-white';
    else banner.className = 'bg-primary p-6 text-white';

    if (date) {
        document.getElementById('viewModalDate').classList.remove('hidden');
        document.getElementById('viewModalDateText').textContent = date;
    } else {
        document.getElementById('viewModalDate').classList.add('hidden');
    }

    if (location) {
        document.getElementById('viewModalLocation').classList.remove('hidden');
        document.getElementById('viewModalLocationText').textContent = location;
    } else {
        document.getElementById('viewModalLocation').classList.add('hidden');
    }

    document.getElementById('viewModal').style.display = 'flex';
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

function openAddModal() {
    document.getElementById('typeSelect').value = "{{ $type }}";
    document.getElementById('addModal').style.display = 'flex';
}

function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
}

document.getElementById('viewModal').addEventListener('click', function(e) {
    if (e.target === this) closeViewModal();
});

@if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});

document.getElementById('activityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const token = document.getElementById('csrfToken').value;
    fetch('{{ route('activities.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
    })
    .then(response => {
        if (response.ok || response.redirected) {
            window.location.href = '{{ route('activities.index') }}?type=' + document.getElementById('typeSelect').value;
        } else {
            response.json().then(data => {
                if (data.errors) alert(Object.values(data.errors).flat().join('\n'));
            });
        }
    })
    .catch(() => { this.submit(); });
});
@endif
</script>

@endsection