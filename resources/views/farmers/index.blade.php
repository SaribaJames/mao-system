@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Farmers Registry</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Manage registered farmers and their profiles</p>
    </div>
    <div class="flex items-center gap-2">
        @if(Auth::user()->isAdmin())
        @php $pendingCount = \App\Models\Farmer::where('registration_status', 'pending')->count(); @endphp
        @if($pendingCount > 0)
        <a href="{{ route('farmers.pending') }}"
           class="bg-accent/10 text-accent border border-accent/30 text-xs font-semibold px-3 py-1.5 rounded-md flex items-center gap-1 hover:bg-accent/20 transition">
            <i class="fa-solid fa-clock"></i> {{ $pendingCount }} Pending
        </a>
        @endif
        @endif
        @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff' || Auth::user()->isBarangayUser())
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

@if(Auth::user()->isBarangayUser())
{{-- Status tabs — barangay reps see their own Pending/Approved/Rejected separately --}}
<div class="flex items-center gap-2 mb-4">
    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}"
       class="text-xs font-medium px-3 py-1.5 rounded-md transition {{ !request('status') ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
        All
    </a>
    <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}"
       class="text-xs font-medium px-3 py-1.5 rounded-md transition {{ request('status') === 'pending' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
        Pending
    </a>
    <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}"
       class="text-xs font-medium px-3 py-1.5 rounded-md transition {{ request('status') === 'approved' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
        Approved
    </a>
    <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}"
       class="text-xs font-medium px-3 py-1.5 rounded-md transition {{ request('status') === 'rejected' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
        Rejected
    </a>
</div>
@endif

{{-- Search / Filter --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-4 mb-4">
    <form method="GET" class="flex gap-3">
        @if(Auth::user()->isBarangayUser() && request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
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
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Contact</th>
                <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Livelihood</th>
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
                            @if(Auth::user()->isBarangayUser())
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium mt-1 inline-block
                                {{ ($farmer->registration_status ?? 'approved') === 'approved' ? 'bg-green-100 text-green-700' :
                                   (($farmer->registration_status ?? 'approved') === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-accent/10 text-accent border border-accent/30') }}">
                                {{ ucfirst($farmer->registration_status ?? 'approved') }}
                            </span>
                            @if(($farmer->registration_status ?? '') === 'rejected' && $farmer->registration_rejection_reason)
                            <p class="text-xs text-red-500 mt-0.5">Reason: {{ $farmer->registration_rejection_reason }}</p>
                            @endif
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $farmer->barangay?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $farmer->mobile_number ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $farmer->main_livelihood ?? '—')) }}</td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $farmer->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('farmers.show', $farmer) }}"
                       class="text-primary hover:underline text-xs font-medium">View</a>

                    @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
                    <a href="{{ route('farmers.edit', $farmer) }}"
                       class="text-blue-500 hover:underline text-xs font-medium ml-2">Edit</a>
                    @endif

                    @if(Auth::user()->isAdmin() && ($farmer->registration_status ?? 'approved') === 'pending')
                    <a href="{{ route('farmers.print', $farmer) }}" target="_blank"
                       class="text-gray-500 hover:underline text-xs font-medium ml-2">Print</a>
                    <form method="POST" action="{{ route('farmers.approve', $farmer) }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="text-green-600 hover:underline text-xs font-medium ml-2">Approve</button>
                    </form>
                    <button onclick="document.getElementById('reject-modal-{{ $farmer->id }}').classList.remove('hidden')"
                            class="text-red-500 hover:underline text-xs font-medium ml-2">Reject</button>

                    {{-- Reject Modal — lives inside this same <td>, no hidden <tr> wrapper.
                         A hidden ancestor row would hide this modal permanently no
                         matter what class the modal div itself has. --}}
                    <div id="reject-modal-{{ $farmer->id }}"
                         class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">
                                Reject Registration — {{ $farmer->first_name }} {{ $farmer->surname }}
                            </h3>
                            <form method="POST" action="{{ route('farmers.reject', $farmer) }}">
                                @csrf
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Reason for rejection</label>
                                <textarea name="reason" rows="3" required
                                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                                <div class="flex gap-2">
                                    <button type="submit"
                                            class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                                        Confirm Reject
                                    </button>
                                    <button type="button"
                                            onclick="document.getElementById('reject-modal-{{ $farmer->id }}').classList.add('hidden')"
                                            class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium px-4 py-2 rounded-md transition">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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