@extends('layouts.app')

@section('content')

<div class="mb-6">
    <a href="{{ route('livestock-insurance.index') }}" class="text-xs text-gray-400 hover:text-primary">
        <i class="fa-solid fa-arrow-left"></i> Back to Applications
    </a>
    <h2 class="text-3xl font-bold tracking-tight text-gray-900 mt-1">New Livestock Insurance Application</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">LIV-UPI-01</p>
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

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-4 mb-4">
    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Farmer <span class="text-red-500">*</span></label>
    <select id="farmer-select" form="liv-form" name="farmer_id" required
        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        <option value="">Select farmer...</option>
        @foreach($farmers as $farmer)
        <option value="{{ $farmer->id }}">{{ $farmer->surname }}, {{ $farmer->first_name }} — {{ $farmer->barangay?->name }}</option>
        @endforeach
    </select>
    <p class="text-xs text-gray-400 mt-1">Selecting a farmer auto-fills their known details below — you can still edit any field.</p>
</div>

<style>
    .ovl-wrap {
        position: relative;
        width: 100%;
        max-width: 850px;
        margin: 0 auto;
    }
    .ovl-wrap img {
        width: 100%;
        display: block;
    }
    .ovl-input {
        position: absolute;
        border: none;
        background: rgba(255, 235, 150, 0.35);
        font-size: 0.7rem;
        padding: 1px 3px;
        box-sizing: border-box;
    }
    .ovl-input:focus {
        outline: 2px solid #2563eb;
        background: rgba(255, 255, 150, 0.6);
    }
    input[type="radio"].cb {
        position: absolute;
        appearance: none;
        -webkit-appearance: none;
        margin: 0;
        box-sizing: border-box;
        border: 1.5px solid #333;
        background: rgba(255, 235, 150, 0.25);
        cursor: pointer;
    }
    input[type="radio"].cb:checked {
        background: white;
    }
    input[type="radio"].cb:checked::after {
        content: "X";
        display: block;
        width: 100%;
        height: 100%;
        text-align: center;
        line-height: 1;
        font-size: 0.65rem;
        font-weight: bold;
        color: #111;
    }
</style>

<form id="liv-form" method="POST" action="{{ route('livestock-insurance.store') }}">
    @csrf

    <div class="ovl-wrap bg-white shadow-sm border border-gray-200 rounded-md overflow-hidden">
        <img src="{{ asset('images/livestock_insurance_page-1.jpg') }}" alt="LIV-UPI-01 Form">

        <input type="radio" name="cover_type" value="commercial" class="cb" style="top:11.93%; left:14.973%; width:1.691%; height:1.306%;">
            <input type="radio" name="cover_type" value="non_commercial" class="cb" style="top:11.988%; left:40.274%; width:1.392%; height:1.133%;">
            <input type="radio" name="cover_type" value="special" class="cb" style="top:11.93%; left:69.828%; width:1.541%; height:1.191%;">
            <input type="text" name="name_of_farmer" class="ovl-input" style="top:15.166%; left:28.557%; width:59.136%; height:1.62%;">
            <input type="radio" name="is_indigenous" value="1" class="cb" style="top:17.351%; left:29.452%; width:2.661%; height:1.364%;">
            <input type="radio" name="is_indigenous" value="0" class="cb" style="top:17.293%; left:38.557%; width:2.586%; height:1.306%;">
            <input type="text" name="tribe" class="ovl-input" style="top:16.885%; left:57.379%; width:30.41%; height:1.695%;">
            <input type="radio" name="is_pwd" value="1" class="cb" style="top:19.139%; left:29.601%; width:2.511%; height:1.249%;">
            <input type="radio" name="is_pwd" value="0" class="cb" style="top:19.139%; left:38.557%; width:2.511%; height:1.191%;">
            <input type="text" name="name_of_spouse" class="ovl-input" style="top:20.547%; left:28.557%; width:59.039%; height:1.62%;">
            <input type="text" name="address" class="ovl-input" style="top:22.192%; left:28.557%; width:59.039%; height:1.77%;">
            <input type="text" name="farm_address" class="ovl-input" style="top:24.135%; left:28.46%; width:59.136%; height:1.62%;">
            <input type="text" name="contact_number" class="ovl-input" style="top:26.003%; left:28.46%; width:59.232%; height:1.546%;">
            <input type="radio" name="animal_type" value="cattle" class="cb" style="top:31.248%; left:14.973%; width:2.511%; height:1.191%;">
            <input type="radio" name="animal_type" value="carabao" class="cb" style="top:31.19%; left:29.676%; width:2.586%; height:1.306%;">
            <input type="radio" name="animal_type" value="swine" class="cb" style="top:31.19%; left:47.364%; width:2.511%; height:1.306%;">
            <input type="radio" name="animal_type" value="poultry" class="cb" style="top:31.19%; left:64.902%; width:2.81%; height:1.306%;">
            <input type="radio" name="animal_type" value="horse" class="cb" style="top:32.862%; left:15.048%; width:2.586%; height:1.249%;">
            <input type="radio" name="animal_type" value="goat" class="cb" style="top:32.805%; left:29.75%; width:2.437%; height:1.306%;">
            <input type="radio" name="animal_type" value="other" class="cb" style="top:32.862%; left:47.438%; width:2.511%; height:1.249%;">
            <input type="radio" name="purpose" value="fattening" class="cb" style="top:37.534%; left:15.048%; width:2.437%; height:1.364%;">
            <input type="radio" name="purpose" value="draft" class="cb" style="top:37.649%; left:29.676%; width:2.511%; height:1.191%;">
            <input type="radio" name="purpose" value="broilers" class="cb" style="top:37.649%; left:47.289%; width:2.661%; height:1.306%;">
            <input type="radio" name="purpose" value="pullets" class="cb" style="top:37.591%; left:64.977%; width:2.586%; height:1.306%;">
            <input type="radio" name="purpose" value="breeding" class="cb" style="top:39.264%; left:14.899%; width:2.661%; height:1.306%;">
            <input type="radio" name="purpose" value="dairy" class="cb" style="top:39.206%; left:29.676%; width:2.661%; height:1.306%;">
            <input type="radio" name="purpose" value="layers" class="cb" style="top:39.206%; left:47.289%; width:2.586%; height:1.306%;">
            <input type="radio" name="purpose" value="parent_stock" class="cb" style="top:39.206%; left:64.902%; width:2.661%; height:1.249%;">
            <input type="text" name="animal_male[]" class="ovl-input" style="top:48.275%; left:14.726%; width:7.971%; height:1.695%;">
            <input type="text" name="animal_female[]" class="ovl-input" style="top:48.25%; left:22.753%; width:8.261%; height:1.845%;">
            <input type="text" name="animal_age[]" class="ovl-input" style="top:48.325%; left:31.071%; width:7.68%; height:1.695%;">
            <input type="text" name="animal_breed[]" class="ovl-input" style="top:48.4%; left:38.616%; width:12.516%; height:1.62%;">
            <input type="text" name="animal_ear_mark[]" class="ovl-input" style="top:48.374%; left:51.104%; width:8.912%; height:1.712%;">
            <input type="text" name="animal_basic_color[]" class="ovl-input" style="top:48.374%; left:60.06%; width:6.449%; height:1.654%;">
            <input type="text" name="animal_proof_ownership[]" class="ovl-input" style="top:48.402%; left:66.664%; width:23.156%; height:1.695%;">
            <input type="text" name="animal_male[]" class="ovl-input" style="top:49.969%; left:14.726%; width:8.067%; height:1.77%;">
            <input type="text" name="animal_female[]" class="ovl-input" style="top:49.969%; left:22.753%; width:8.358%; height:1.77%;">
            <input type="text" name="animal_age[]" class="ovl-input" style="top:50.044%; left:30.975%; width:7.874%; height:1.62%;">
            <input type="text" name="animal_breed[]" class="ovl-input" style="top:49.895%; left:38.712%; width:12.323%; height:1.845%;">
            <input type="text" name="animal_ear_mark[]" class="ovl-input" style="top:50.047%; left:51.179%; width:8.837%; height:1.712%;">
            <input type="text" name="animal_basic_color[]" class="ovl-input" style="top:49.989%; left:60.135%; width:6.374%; height:1.769%;">
            <input type="text" name="animal_proof_ownership[]" class="ovl-input" style="top:49.949%; left:66.702%; width:23.017%; height:1.712%;">
            <input type="text" name="animal_male[]" class="ovl-input" style="top:51.688%; left:14.726%; width:7.971%; height:1.695%;">
            <input type="text" name="animal_female[]" class="ovl-input" style="top:51.688%; left:22.753%; width:8.261%; height:1.77%;">
            <input type="text" name="animal_age[]" class="ovl-input" style="top:51.688%; left:30.975%; width:7.777%; height:1.695%;">
            <input type="text" name="animal_breed[]" class="ovl-input" style="top:51.614%; left:38.712%; width:12.42%; height:1.845%;">
            <input type="text" name="animal_ear_mark[]" class="ovl-input" style="top:51.719%; left:51.179%; width:8.837%; height:1.712%;">
            <input type="text" name="animal_basic_color[]" class="ovl-input" style="top:51.719%; left:60.135%; width:6.374%; height:1.712%;">
            <input type="text" name="animal_proof_ownership[]" class="ovl-input" style="top:51.679%; left:66.628%; width:23.092%; height:1.654%;">
            <input type="text" name="animal_male[]" class="ovl-input" style="top:53.258%; left:14.726%; width:7.971%; height:1.845%;">
            <input type="text" name="animal_female[]" class="ovl-input" style="top:53.333%; left:22.56%; width:8.454%; height:1.695%;">
            <input type="text" name="animal_age[]" class="ovl-input" style="top:53.333%; left:30.975%; width:7.874%; height:1.77%;">
            <input type="text" name="animal_breed[]" class="ovl-input" style="top:53.333%; left:38.809%; width:12.323%; height:1.77%;">
            <input type="text" name="animal_ear_mark[]" class="ovl-input" style="top:53.392%; left:51.03%; width:8.986%; height:1.654%;">
            <input type="text" name="animal_basic_color[]" class="ovl-input" style="top:53.392%; left:59.985%; width:6.598%; height:1.654%;">
            <input type="text" name="animal_proof_ownership[]" class="ovl-input" style="top:53.409%; left:66.553%; width:23.241%; height:1.539%;">
            <input type="text" name="animal_male[]" class="ovl-input" style="top:54.977%; left:14.726%; width:8.067%; height:1.77%;">
            <input type="text" name="animal_female[]" class="ovl-input" style="top:54.902%; left:22.56%; width:8.454%; height:1.845%;">
            <input type="text" name="animal_age[]" class="ovl-input" style="top:55.052%; left:30.878%; width:7.874%; height:1.695%;">
            <input type="text" name="animal_breed[]" class="ovl-input" style="top:54.977%; left:38.712%; width:12.42%; height:1.695%;">
            <input type="text" name="animal_ear_mark[]" class="ovl-input" style="top:55.064%; left:51.179%; width:8.837%; height:1.654%;">
            <input type="text" name="animal_basic_color[]" class="ovl-input" style="top:55.006%; left:60.06%; width:6.524%; height:1.654%;">
            <input type="text" name="animal_proof_ownership[]" class="ovl-input" style="top:54.909%; left:66.628%; width:23.166%; height:1.827%;">
            <input type="text" name="animal_male[]" class="ovl-input" style="top:56.696%; left:14.726%; width:8.067%; height:1.695%;">
            <input type="text" name="animal_female[]" class="ovl-input" style="top:56.621%; left:22.657%; width:8.454%; height:1.77%;">
            <input type="text" name="animal_age[]" class="ovl-input" style="top:56.621%; left:30.975%; width:7.874%; height:1.695%;">
            <input type="text" name="animal_breed[]" class="ovl-input" style="top:56.621%; left:38.712%; width:12.516%; height:1.695%;">
            <input type="text" name="animal_ear_mark[]" class="ovl-input" style="top:56.736%; left:51.179%; width:8.837%; height:1.654%;">
            <input type="text" name="animal_basic_color[]" class="ovl-input" style="top:56.621%; left:59.985%; width:6.598%; height:1.827%;">
            <input type="text" name="animal_proof_ownership[]" class="ovl-input" style="top:56.696%; left:66.628%; width:23.092%; height:1.596%;">
            <input type="text" name="total_heads" class="ovl-input" style="top:58.582%; left:44.91%; width:30.406%; height:1.366%;">
            <input type="text" name="source_of_stock" class="ovl-input" style="top:60.197%; left:42.298%; width:33.167%; height:1.366%;">
            <input type="text" name="no_of_housing_units" class="ovl-input" style="top:61.638%; left:42.372%; width:33.092%; height:1.481%;">
            <input type="text" name="birds_per_housing_unit" class="ovl-input" style="top:63.253%; left:42.447%; width:33.018%; height:1.596%;">
            <input type="text" name="date_of_purchase" class="ovl-input" style="top:64.803%; left:42.522%; width:32.943%; height:1.539%;">
            <input type="text" name="sum_insured_per_head" class="ovl-input" style="top:69.417%; left:41.477%; width:11.524%; height:1.769%;">
            <input type="text" name="total_sum_insured" class="ovl-input" style="top:71.378%; left:33.267%; width:19.286%; height:1.366%;">
            <input type="text" name="epidemic_coverage_1" class="ovl-input" style="top:74.261%; left:20.878%; width:12.793%; height:1.654%;">
            <input type="text" name="epidemic_coverage_2" class="ovl-input" style="top:75.991%; left:20.878%; width:12.867%; height:1.539%;">
            <input type="text" name="epidemic_coverage_3" class="ovl-input" style="top:77.548%; left:20.804%; width:13.016%; height:1.539%;">
            <input type="text" name="assignee_name" class="ovl-input" style="top:79.394%; left:19.087%; width:31.152%; height:1.366%;">
            <input type="text" name="assignee_address" class="ovl-input" style="top:80.77%; left:19.087%; width:31.227%; height:1.539%;">
            <input type="text" name="assignee_contact" class="ovl-input" style="top:82.384%; left:19.087%; width:31.227%; height:1.539%;">
            <input type="text" name="application_date" class="ovl-input" style="top:83.884%; left:19.087%; width:31.301%; height:1.596%;">
            <input type="text" name="name_of_proponent" class="ovl-input" style="top:89.593%; left:70.956%; width:23.017%; height:2.346%;">


    </div>

    <div class="max-w-[850px] mx-auto mt-4">
        <button type="submit"
            class="w-full bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-3 rounded-md transition">
            Save Application
        </button>
    </div>
</form>

@php
    $farmerAutofill = $farmers->mapWithKeys(function ($farmer) {
        $addressParts = array_filter([
            $farmer->house_lot_number,
            $farmer->street,
            $farmer->barangay?->name,
            $farmer->municipality,
            $farmer->province,
        ]);
        $farmAddressParts = array_filter([
            $farmer->farm_location_barangay,
            $farmer->farm_location_municipality,
            $farmer->farm_location_province,
        ]);

        return [$farmer->id => [
            'name_of_farmer' => strtoupper(trim($farmer->first_name . ' ' . $farmer->surname)),
            'address' => implode(', ', $addressParts),
            'farm_address' => implode(', ', $farmAddressParts),
            'contact_number' => $farmer->mobile_number,
            'is_indigenous' => $farmer->is_indigenous ? '1' : '0',
            'tribe' => $farmer->indigenous_group_name,
            'is_pwd' => $farmer->is_pwd ? '1' : '0',
        ]];
    });
@endphp

<script>
    const farmerAutofill = @json($farmerAutofill);

    document.getElementById('farmer-select').addEventListener('change', function () {
        const data = farmerAutofill[this.value];
        if (!data) return;

        const form = document.getElementById('liv-form');

        // Fill plain text inputs
        ['name_of_farmer', 'address', 'farm_address', 'contact_number', 'tribe'].forEach(function (field) {
            const input = form.querySelector('[name="' + field + '"]');
            if (input && data[field]) {
                input.value = data[field];
            }
        });

        // Set matching radio buttons for indigenous / pwd
        ['is_indigenous', 'is_pwd'].forEach(function (field) {
            const radios = form.querySelectorAll('[name="' + field + '"]');
            radios.forEach(function (radio) {
                radio.checked = (radio.value === data[field]);
            });
        });
    });
</script>

@endsection