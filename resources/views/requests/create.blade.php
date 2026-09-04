@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h2 class="text-3xl font-bold tracking-tight text-gray-900">New Request</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Submit a farmer request</p>
</div>

@if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-sm rounded-md p-3 mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('requests.store') }}">
@csrf

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5 mb-4">
    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Request Details</h3>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Farmer <span class="text-red-500">*</span></label>
            <select name="farmer_id" id="farmerSelect" required onchange="showFarmerPrograms()"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select Farmer</option>
                @foreach($farmers as $farmer)
                    {{-- data-programs feeds the hint below, so the rep can see at a
                         glance which program this farmer already belongs to. --}}
                    <option value="{{ $farmer->id }}"
                            data-programs="{{ $farmer->activePrograms->pluck('name')->join(', ') }}"
                            data-program-ids="{{ $farmer->activePrograms->pluck('id')->join(',') }}"
                            {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}>
                        {{ $farmer->first_name }} {{ $farmer->surname }} — {{ $farmer->barangay?->name }}
                    </option>
                @endforeach
            </select>
            <p id="farmerProgramHint" class="text-xs mt-1 hidden"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Request Type <span class="text-red-500">*</span></label>
            <select name="request_type" required
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select Type</option>
                <option value="seeds_distribution">Seeds Distribution</option>
                <option value="fertilizer_request">Fertilizer Request</option>
                <option value="pesticide_request">Pesticide Request</option>
                <option value="equipment_request">Equipment Request</option>
                <option value="training_seminar">Training/Seminar</option>
                <option value="technical_assistance">Technical Assistance</option>
                <option value="financial_assistance">Financial Assistance</option>
                <option value="others">Others</option>
            </select>
        </div>
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">
            Program <span class="text-gray-400 font-normal">(optional)</span>
        </label>
        <select name="program_id"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Not part of a program</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                    {{ $program->name }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-400 mt-1">
            Tagging a program sends this to that program's coordinator. Leave blank for
            general requests like certifications or technical assistance.
        </p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Item/Service <span class="text-red-500">*</span></label>
            <input type="text" name="item_service" value="{{ old('item_service') }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Stock Item (if applicable)</label>
            <select name="stock_id"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select Stock Item</option>
                @foreach($stocks as $stock)
                    <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>
                        {{ $stock->item_name }} ({{ number_format($stock->remaining_stock, 0) }} {{ $stock->unit }} available)
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Quantity</label>
            <input type="number" step="0.01" name="quantity" value="{{ old('quantity') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Unit</label>
            <input type="text" name="quantity_unit" value="{{ old('quantity_unit', 'kg') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Purpose</label>
        <textarea name="purpose" rows="3"
                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">{{ old('purpose') }}</textarea>
    </div>
</div>

<div class="flex items-center gap-3">
    <button type="submit"
            class="bg-primary hover:bg-primary-dark text-white font-semibold px-6 py-2.5 rounded-md transition text-sm">
        Submit Request
    </button>
    <a href="{{ route('requests.index') }}"
       class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium px-6 py-2.5 rounded-md transition text-sm">
        Cancel
    </a>
</div>

</form>
<script>
function showFarmerPrograms() {
    var sel  = document.getElementById('farmerSelect');
    var hint = document.getElementById('farmerProgramHint');
    var opt  = sel.options[sel.selectedIndex];
    var names = opt ? (opt.dataset.programs || '') : '';
    var ids   = opt ? (opt.dataset.programIds || '') : '';

    if (!names) {
        hint.className = 'text-xs mt-1 text-gray-400';
        hint.textContent = opt && opt.value ? 'Not enrolled in any program.' : '';
        hint.classList.toggle('hidden', !opt || !opt.value);
        return;
    }

    hint.className = 'text-xs mt-1 text-primary font-medium';
    hint.textContent = 'Enrolled in: ' + names;
    hint.classList.remove('hidden');

    // If the farmer is in exactly one program, pre-select it — that is almost
    // always the program the request belongs to.
    var idList = ids.split(',').filter(Boolean);
    var progSel = document.querySelector('select[name="program_id"]');
    if (progSel && idList.length === 1 && !progSel.value) {
        progSel.value = idList[0];
    }
}
document.addEventListener('DOMContentLoaded', showFarmerPrograms);
</script>

@endsection