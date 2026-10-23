@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Farmers Registry</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Manage registered farmers and their profiles</p>
    </div>
    <div class="flex items-center gap-2">
        @if(Auth::user()->isAdmin())
        @php $viewRequestCount = \App\Models\FarmerViewRequest::where('status', 'pending')->count(); @endphp
        @if($viewRequestCount > 0)
        <a href="{{ route('farmers.view-requests') }}"
           class="bg-accent/10 text-accent border border-accent/30 text-xs font-semibold px-3 py-1.5 rounded-md flex items-center gap-1 hover:bg-accent/20 transition">
            <i class="fa-solid fa-user-lock"></i> {{ $viewRequestCount }} View Request{{ $viewRequestCount === 1 ? '' : 's' }}
        </a>
        @endif
        @endif

        @if(!Auth::user()->isAdmin())
        <a href="{{ route('farmers.my-view-requests') }}"
           class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-xs font-semibold px-3 py-1.5 rounded-md flex items-center gap-1 transition">
            <i class="fa-solid fa-clock-rotate-left"></i> My View Requests
        </a>
        @endif

        @if(Auth::user()->isBarangayUser())
        <a href="{{ route('farmers.blank-form') }}"
           class="bg-gray-700 hover:bg-gray-800 text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
            <i class="fa-solid fa-file-arrow-down"></i> Download Blank Form
        </a>
        @endif

        @if(Auth::user()->isAdmin())
        <a href="{{ route('farmers.create') }}"
           class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
            <i class="fa-solid fa-plus"></i> Register Farmer
        </a>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

@unless(Auth::user()->isAdmin())
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 text-xs rounded-md p-3 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-shield-halved"></i>
        Only basic info is shown here to protect farmer identity. Open a farmer and request access to see their full profile.
    </div>
@endunless

{{-- Search / Filter --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-4 mb-4">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name..."
               class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        @if(!Auth::user()->isBarangayUser())
        <select name="barangay" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">All Barangays</option>
            @foreach(\App\Models\Barangay::orderBy('name')->get() as $b)
            <option value="{{ $b->id }}" {{ request('barangay') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
        @endif
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary-dark transition">
            Search
        </button>
    </form>
</div>

{{-- Farmers Table --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Farmer</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Barangay</th>
                @if(Auth::user()->isAdmin())
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Contact</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Livelihood</th>
                @endif
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Registered</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($farmers as $farmer)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if($farmer->photo_url)
                        <img src="{{ $farmer->photo_url }}"
                             class="w-8 h-8 rounded-full object-cover border border-gray-200">
                        @else
                        <div class="w-8 h-8 rounded-full bg-primary-light flex items-center justify-center text-primary text-xs font-bold">
                            {{ strtoupper(substr($farmer->first_name, 0, 1)) }}
                        </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ $farmer->first_name }} {{ $farmer->surname }}</p>
                            <p class="text-xs text-gray-400">{{ $farmer->sex }} ·
                                {{ \Carbon\Carbon::parse($farmer->date_of_birth)->age }} yrs
                            </p>
                            @if($farmer->activePrograms->count() > 0)
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($farmer->activePrograms as $prog)
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-full font-medium bg-primary/10 text-primary dark:bg-primary/20">
                                            <i class="fa-solid fa-seedling"></i> {{ $prog->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $farmer->barangay?->name ?? '—' }}</td>
                @if(Auth::user()->isAdmin())
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $farmer->mobile_number ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $farmer->main_livelihood ?? '—')) }}</td>
                @endif
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $farmer->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('farmers.show', $farmer) }}"
                           class="text-primary hover:underline text-xs font-medium">View</a>
                        <a href="{{ route('farmers.edit', $farmer) }}"
                           class="text-blue-500 hover:underline text-xs font-medium ml-2">Edit</a>
                        <a href="{{ route('farmers.print', $farmer) }}" target="_blank"
                           class="text-gray-600 dark:text-gray-300 hover:underline text-xs font-medium ml-2">Print</a>
                    @else
                        @if(in_array($farmer->id, $activeAccessFarmerIds ?? []))
                            <a href="{{ route('farmers.show', $farmer) }}"
                               class="text-green-600 hover:underline text-xs font-medium">
                                <i class="fa-solid fa-lock-open"></i> View Full
                            </a>
                        @else
                            <a href="{{ route('farmers.show', $farmer) }}"
                               class="text-primary hover:underline text-xs font-medium">Basic Info</a>
                        @endif
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">No farmers found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-border-soft dark:border-gray-700">
        {{ $farmers->links() }}
    </div>
</div>

@endsection
