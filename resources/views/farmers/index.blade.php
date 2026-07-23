@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Farmers Registry</h2>
        <p class="text-gray-500 text-sm mt-1">Manage registered farmers and their profiles</p>
    </div>
    <div class="flex items-center gap-2">
        @if(Auth::user()->isAdmin())
        @php $pendingCount = \App\Models\Farmer::where('registration_status', 'pending')->count(); @endphp
        @if($pendingCount > 0)
        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1.5 rounded-md flex items-center gap-1">
            <i class="fa-solid fa-clock"></i> {{ $pendingCount }} Pending
        </span>
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
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Search / Filter --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name..."
               class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        @if(!Auth::user()->isBarangayUser())
        <select name="barangay" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
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
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Farmer</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Barangay</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Contact</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Livelihood</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Registered</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($farmers as $farmer)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if($farmer->photo)
                        <img src="{{ asset('storage/' . $farmer->photo) }}"
                             class="w-8 h-8 rounded-full object-cover border border-gray-200">
                        @else
                        <div class="w-8 h-8 rounded-full bg-primary-light flex items-center justify-center text-primary text-xs font-bold">
                            {{ strtoupper(substr($farmer->first_name, 0, 1)) }}
                        </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800">{{ $farmer->first_name }} {{ $farmer->surname }}</p>
                            <p class="text-xs text-gray-400">{{ $farmer->sex }} ·
                                {{ \Carbon\Carbon::parse($farmer->date_of_birth)->age }} yrs
                            </p>
                            @if(Auth::user()->isBarangayUser())
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium mt-1 inline-block
                                {{ ($farmer->registration_status ?? 'approved') === 'approved' ? 'bg-green-100 text-green-700' :
                                   (($farmer->registration_status ?? 'approved') === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($farmer->registration_status ?? 'approved') }}
                            </span>
                            @if(($farmer->registration_status ?? '') === 'rejected' && $farmer->registration_rejection_reason)
                            <p class="text-xs text-red-500 mt-0.5">Reason: {{ $farmer->registration_rejection_reason }}</p>
                            @endif
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $farmer->barangay?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $farmer->mobile_number ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ ucfirst(str_replace('_', ' ', $farmer->main_livelihood ?? '—')) }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $farmer->created_at->format('M d, Y') }}</td>
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
                    @endif
                </td>
            </tr>

            {{-- Reject Modal — inside the loop so $farmer is always defined --}}
            @if(Auth::user()->isAdmin() && ($farmer->registration_status ?? 'approved') === 'pending')
            <tr class="hidden">
                <td>
                    <div id="reject-modal-{{ $farmer->id }}"
                         class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                            <h3 class="font-semibold text-gray-800 mb-3">
                                Reject Registration — {{ $farmer->first_name }} {{ $farmer->surname }}
                            </h3>
                            <form method="POST" action="{{ route('farmers.reject', $farmer) }}">
                                @csrf
                                <label class="block text-xs text-gray-500 mb-1">Reason for rejection</label>
                                <textarea name="reason" rows="3" required
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                                <div class="flex gap-2">
                                    <button type="submit"
                                            class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                                        Confirm Reject
                                    </button>
                                    <button type="button"
                                            onclick="document.getElementById('reject-modal-{{ $farmer->id }}').classList.add('hidden')"
                                            class="flex-1 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-md transition">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @endif

            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">No farmers found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $farmers->links() }}
    </div>
</div>

@endsection