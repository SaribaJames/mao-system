@extends('layouts.app')

@section('content')

<div class="mb-4 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Edit Farmer</h2>
        <p class="text-gray-500 text-sm mt-1">{{ $farmer->reference_number }}</p>
    </div>
    <a href="{{ route('farmers.show', $farmer) }}"
       class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-5 py-2 rounded-md text-sm">
        ← Back
    </a>
</div>

@if ($errors->any())
    <div class="max-w-[850px] mx-auto bg-red-50 border border-red-200 text-red-600 text-sm rounded-md p-3 mb-4">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<style>
.rsbsa-page { position:relative; width:850px; margin:0 auto 24px auto; background:#fff; box-shadow:0 2px 12px rgba(0,0,0,.15); }
.rsbsa-page img { width:100%; display:block; pointer-events:none; user-select:none; }
.f { position:absolute; background:rgba(255,255,255,0.01); border:none; outline:none; font-family:Arial,sans-serif; font-size:11.5px; color:#000; padding:0 2px; box-sizing:border-box; }
.f:focus { background:rgba(255,255,180,0.8); outline:1px solid #2563eb; }
.cb { position:absolute; width:13px; height:13px; cursor:pointer; accent-color:#111; margin:0; }
.dg { position:absolute; text-align:center; border:1px solid #999; background:rgba(255,255,255,0.4); font-size:10px; font-weight:bold; padding:0; outline:none; }
.dg:focus { background:rgba(255,255,180,0.8); }
.photo-upload { position:absolute; cursor:pointer; }
.photo-upload input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; }
.photo-preview { width:100%; height:100%; object-fit:cover; display:none; }
</style>

@php
    $dobValue = old('date_of_birth', $farmer->date_of_birth?->format('Y-m-d'));
    $pobParts = old('place_of_birth', $farmer->place_of_birth) ? explode(', ', old('place_of_birth', $farmer->place_of_birth)) : [];
    $religionVal = old('religion', $farmer->religion);
    $isOtherReligion = $religionVal && !in_array($religionVal, ['Christianity', 'Islam']);
@endphp

<form method="POST" action="{{ route('farmers.update', $farmer) }}" enctype="multipart/form-data" id="farmerForm" autocomplete="off">
@csrf
@method('PUT')

<div class="rsbsa-page">
<img src="{{ asset('images/rsbsa_page-1.jpg') }}" alt="RSBSA Page 1">

<div class="photo-upload" style="top:2.81%;left:74.42%;width:21.45%;height:13.93%;">
    <input type="file" name="photo" id="photoInput" accept="image/*" autocomplete="off">
    <img id="photoPreview" class="photo-preview" alt="Preview"
         src="{{ $farmer->photo ? asset('storage/'.$farmer->photo) : '' }}"
         style="display:{{ $farmer->photo ? 'block' : 'none' }};">
</div>

<input type="checkbox" class="cb" id="cb_new" style="top:12.81%;left:18.26%;"
    {{ old('enrollment_type', $farmer->enrollment_type) === 'new' ? 'checked' : '' }}
    onchange="if(this.checked)document.getElementById('cb_upd').checked=false; document.getElementById('enroll_type').value='new';">
<input type="checkbox" class="cb" id="cb_upd" style="top:12.78%;left:25.39%;"
    {{ old('enrollment_type', $farmer->enrollment_type) === 'updating' ? 'checked' : '' }}
    onchange="if(this.checked){document.getElementById('cb_new').checked=false; document.getElementById('enroll_type').value='updating';}">
<input type="hidden" name="enrollment_type" id="enroll_type" value="{{ old('enrollment_type', $farmer->enrollment_type) }}">

<input type="text" name="surname" id="mainSurname" value="{{ old('surname', $farmer->surname) }}" required autocomplete="off"
    class="f" style="top:18.64%;left:5.58%;width:42.06%;height:2.05%;">
<input type="text" name="first_name" id="mainFirstName" value="{{ old('first_name', $farmer->first_name) }}" required autocomplete="off"
    class="f" style="top:18.49%;left:52.41%;width:42.06%;height:2.05%;">
<input type="text" name="middle_name" id="mainMiddleName" value="{{ old('middle_name', $farmer->middle_name) }}" autocomplete="off"
    class="f" style="top:21.40%;left:5.62%;width:42.06%;height:2.05%;">
<input type="text" name="extension_name" id="mainExtName" value="{{ old('extension_name', $farmer->extension_name) }}" autocomplete="off"
    class="f" style="top:21.44%;left:50.98%;width:9.79%;height:2.05%;">

<input type="checkbox" class="cb" id="sex_m" style="top:23.50%;left:76.56%;"
    onchange="if(this.checked){document.getElementById('sex_f').checked=false;document.getElementById('sex_h').value='male';}"
    {{ old('sex', $farmer->sex) == 'male' ? 'checked' : '' }}>
<input type="checkbox" class="cb" id="sex_f" style="top:23.44%;left:84.74%;"
    onchange="if(this.checked){document.getElementById('sex_m').checked=false;document.getElementById('sex_h').value='female';}"
    {{ old('sex', $farmer->sex) == 'female' ? 'checked' : '' }}>
<input type="hidden" name="sex" id="sex_h" value="{{ old('sex', $farmer->sex) }}" required>

<input type="text" name="house_lot_number" value="{{ old('house_lot_number', $farmer->house_lot_number) }}" autocomplete="off"
    class="f" style="top:25.54%;left:11.90%;width:26.46%;height:1.63%;">
<input type="text" name="street" value="{{ old('street', $farmer->street) }}" autocomplete="off"
    class="f" style="top:25.59%;left:40.18%;width:26.46%;height:1.63%;">
<select name="barangay_id" class="f" style="top:25.55%;left:68.38%;width:26.46%;height:1.63%;background:rgba(255,255,255,0.9);cursor:pointer;">
    <option value="">Select</option>
    @foreach($barangays as $b)
    <option value="{{ $b->id }}" {{ old('barangay_id', $farmer->barangay_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
    @endforeach
</select>

<input type="text" name="municipality" value="{{ old('municipality', $farmer->municipality) }}" autocomplete="off"
    class="f" style="top:28.69%;left:11.79%;width:26.46%;height:1.63%;">
<input type="text" name="province" value="{{ old('province', $farmer->province) }}" autocomplete="off"
    class="f" style="top:28.69%;left:40.18%;width:26.46%;height:1.63%;">
<input type="text" name="region" value="{{ old('region', $farmer->region) }}" autocomplete="off"
    class="f" style="top:28.75%;left:68.38%;width:26.46%;height:1.63%;">

<div style="position:absolute;top:32.78%;left:5.39%;width:20%;height:1.09%;display:flex;" id="mobBoxes"></div>
<input type="text" id="mobReal" maxlength="11" inputmode="numeric"
    value="{{ old('mobile_number', $farmer->mobile_number) }}"
    style="position:absolute;top:32.78%;left:5.39%;width:20%;height:1.09%;opacity:0;border:none;">
<input type="hidden" name="mobile_number" id="mobH" value="{{ old('mobile_number', $farmer->mobile_number) }}">

<div style="position:absolute;top:32.72%;left:29.62%;width:18%;height:1.19%;display:flex;" id="landBoxes"></div>
<input type="text" id="landReal" maxlength="10" inputmode="numeric"
    value="{{ old('landline_number', $farmer->landline_number) }}"
    style="position:absolute;top:32.72%;left:29.62%;width:18%;height:1.19%;opacity:0;border:none;">
<input type="hidden" name="landline_number" id="landH" value="{{ old('landline_number', $farmer->landline_number) }}">

@php
$eduMap = [
    'pre_school'=>['top'=>'33.57%','left'=>'52.98%'], 'junior_high_k12'=>['top'=>'33.57%','left'=>'67.82%'], 'vocational'=>['top'=>'33.58%','left'=>'83.89%'],
    'elementary'=>['top'=>'34.81%','left'=>'53.00%'], 'senior_high_k12'=>['top'=>'34.85%','left'=>'67.77%'], 'post_graduate'=>['top'=>'34.86%','left'=>'83.85%'],
    'high_school_non_k12'=>['top'=>'36.20%','left'=>'52.98%'], 'college'=>['top'=>'36.20%','left'=>'67.85%'], 'none'=>['top'=>'36.21%','left'=>'83.88%'],
];
$eduVal = old('highest_education', $farmer->highest_education);
@endphp
@foreach($eduMap as $val => $pos)
<input type="checkbox" class="cb edu-cb" data-val="{{ $val }}" style="top:{{ $pos['top'] }};left:{{ $pos['left'] }};" {{ $eduVal == $val ? 'checked' : '' }}>
@endforeach
<input type="hidden" name="highest_education" id="edu_h" value="{{ $eduVal }}">

<div style="position:absolute;top:36.06%;left:5.45%;width:17.5%;height:1.45%;display:flex;" id="dobBoxes"></div>
<input type="text" id="dobReal" maxlength="8" inputmode="numeric"
    value="{{ $dobValue ? \Carbon\Carbon::parse($dobValue)->format('mdY') : '' }}"
    style="position:absolute;top:36.06%;left:5.45%;width:17.5%;height:1.45%;opacity:0;border:none;">
<input type="hidden" name="date_of_birth" id="dobH" value="{{ $dobValue }}">

<input type="text" id="pob1" placeholder="City/Municipality" value="{{ $pobParts[0] ?? '' }}" autocomplete="off"
    class="f" style="top:35.74%;left:25.54%;width:23.42%;height:0.91%;font-size:9px;">
<input type="text" id="pob2" placeholder="Province/State" value="{{ $pobParts[1] ?? '' }}" autocomplete="off"
    class="f" style="top:37.03%;left:25.43%;width:11.29%;height:0.91%;font-size:9px;">
<input type="text" id="pob3" placeholder="Country" value="{{ $pobParts[2] ?? 'Philippines' }}" autocomplete="off"
    class="f" style="top:37.04%;left:37.49%;width:11.29%;height:0.91%;font-size:9px;">
<input type="hidden" name="place_of_birth" id="pobH" value="{{ old('place_of_birth', $farmer->place_of_birth) }}">

<input type="checkbox" class="cb" id="pwd_y" style="top:38.52%;left:75.99%;"
    onchange="if(this.checked){document.getElementById('pwd_n').checked=false;document.getElementById('pwd_h').value='1';}"
    {{ old('is_pwd', $farmer->is_pwd) == '1' ? 'checked' : '' }}>
<input type="checkbox" class="cb" id="pwd_n" style="top:38.52%;left:83.11%;"
    onchange="if(this.checked){document.getElementById('pwd_y').checked=false;document.getElementById('pwd_h').value='0';}"
    {{ old('is_pwd', $farmer->is_pwd) == '0' ? 'checked' : '' }}>
<input type="hidden" name="is_pwd" id="pwd_h" value="{{ old('is_pwd', $farmer->is_pwd ? '1' : '0') }}">

<input type="checkbox" class="cb rel-cb" data-val="Christianity" style="top:40.30%;left:13.03%;" {{ $religionVal == 'Christianity' ? 'checked' : '' }}>
<input type="checkbox" class="cb rel-cb" data-val="Islam" style="top:40.39%;left:22.96%;" {{ $religionVal == 'Islam' ? 'checked' : '' }}>
<input type="checkbox" class="cb rel-cb" data-val="others" style="top:40.36%;left:28.92%;" {{ $isOtherReligion ? 'checked' : '' }}>
<input type="text" id="religionOther" name="religion_other" value="{{ $isOtherReligion ? $religionVal : old('religion_other') }}" autocomplete="off"
    class="f" style="top:40.22%;left:39.73%;width:9.61%;height:0.91%;display:{{ $isOtherReligion ? 'block' : 'none' }};">
<input type="hidden" name="religion" id="religion_h" value="{{ $isOtherReligion ? 'others' : $religionVal }}">

<input type="checkbox" class="cb" id="fps_y" style="top:41.09%;left:75.69%;"
    onchange="if(this.checked){document.getElementById('fps_n').checked=false;document.getElementById('fps_h').value='1';}"
    {{ old('is_4ps_beneficiary', $farmer->is_4ps_beneficiary) == '1' ? 'checked' : '' }}>
<input type="checkbox" class="cb" id="fps_n" style="top:41.10%;left:82.66%;"
    onchange="if(this.checked){document.getElementById('fps_y').checked=false;document.getElementById('fps_h').value='0';}"
    {{ old('is_4ps_beneficiary', $farmer->is_4ps_beneficiary) == '0' ? 'checked' : '' }}>
<input type="hidden" name="is_4ps_beneficiary" id="fps_h" value="{{ old('is_4ps_beneficiary', $farmer->is_4ps_beneficiary ? '1' : '0') }}">

@php $csMap = ['single'=>'15.74%','married'=>'23.36%','widowed'=>'31.52%','separated'=>'40.66%']; $csVal = old('civil_status', $farmer->civil_status); @endphp
@foreach($csMap as $val=>$left)
<input type="checkbox" class="cb cs-cb" data-val="{{ $val }}" style="top:42.47%;left:{{ $left }};" {{ $csVal == $val ? 'checked' : '' }}>
@endforeach
<input type="hidden" name="civil_status" id="cs_h" value="{{ $csVal }}">

<input type="checkbox" class="cb" id="ind_y" style="top:42.76%;left:75.67%;"
    onchange="if(this.checked){document.getElementById('ind_n').checked=false;document.getElementById('ind_h').value='1';}"
    {{ old('is_indigenous', $farmer->is_indigenous) == '1' ? 'checked' : '' }}>
<input type="checkbox" class="cb" id="ind_n" style="top:42.75%;left:82.66%;"
    onchange="if(this.checked){document.getElementById('ind_y').checked=false;document.getElementById('ind_h').value='0';}"
    {{ old('is_indigenous', $farmer->is_indigenous) == '0' ? 'checked' : '' }}>
<input type="hidden" name="is_indigenous" id="ind_h" value="{{ old('is_indigenous', $farmer->is_indigenous ? '1' : '0') }}">
<input type="text" name="indigenous_group_name" value="{{ old('indigenous_group_name', $farmer->indigenous_group_name) }}" autocomplete="off"
    class="f" style="top:44.07%;left:60.40%;width:33.99%;height:1.45%;">

<input type="text" name="spouse_name" value="{{ old('spouse_name', $farmer->spouse_name) }}" autocomplete="off"
    class="f" style="top:44.84%;left:17.02%;width:31.72%;height:1.39%;">

<input type="checkbox" class="cb" id="gid_y" style="top:46.62%;left:66.74%;"
    onchange="if(this.checked){document.getElementById('gid_n').checked=false;document.getElementById('gid_h').value='1';}"
    {{ old('has_government_id', $farmer->has_government_id) == '1' ? 'checked' : '' }}>
<input type="checkbox" class="cb" id="gid_n" style="top:46.62%;left:73.87%;"
    onchange="if(this.checked){document.getElementById('gid_y').checked=false;document.getElementById('gid_h').value='0';}"
    {{ old('has_government_id', $farmer->has_government_id) == '0' ? 'checked' : '' }}>
<input type="hidden" name="has_government_id" id="gid_h" value="{{ old('has_government_id', $farmer->has_government_id ? '1' : '0') }}">
<input type="text" name="government_id_type" value="{{ old('government_id_type', $farmer->government_id_type) }}" autocomplete="off"
    class="f" style="top:47.70%;left:66.66%;width:28.02%;height:1.01%;">
<input type="text" name="government_id_number" value="{{ old('government_id_number', $farmer->government_id_number) }}" autocomplete="off"
    class="f" style="top:48.90%;left:66.64%;width:28.02%;height:1.01%;">

<input type="text" name="mother_maiden_name" value="{{ old('mother_maiden_name', $farmer->mother_maiden_name) }}" autocomplete="off"
    class="f" style="top:48.00%;left:17.00%;width:31.72%;height:1.39%;">

<input type="checkbox" class="cb" id="hh_y" style="top:50.36%;left:22.57%;"
    onchange="if(this.checked){document.getElementById('hh_n').checked=false;document.getElementById('hh_h').value='1';document.getElementById('hhSubFields').style.display='none';}"
    {{ old('is_household_head', $farmer->is_household_head) == '1' ? 'checked' : '' }}>
<input type="checkbox" class="cb" id="hh_n" style="top:50.41%;left:29.72%;"
    onchange="if(this.checked){document.getElementById('hh_y').checked=false;document.getElementById('hh_h').value='0';document.getElementById('hhSubFields').style.display='block';}"
    {{ old('is_household_head', $farmer->is_household_head) == '0' ? 'checked' : '' }}>
<input type="hidden" name="is_household_head" id="hh_h" value="{{ old('is_household_head', $farmer->is_household_head ? '1' : '0') }}">

<div id="hhSubFields" style="display:{{ old('is_household_head', $farmer->is_household_head) == '0' ? 'block' : 'none' }};">
<input type="text" name="household_head_name" value="{{ old('household_head_name', $farmer->household_head_name) }}" autocomplete="off"
    class="f" style="top:51.86%;left:24.09%;width:24.67%;height:1.39%;">
<input type="text" name="household_head_relationship" value="{{ old('household_head_relationship', $farmer->household_head_relationship) }}" autocomplete="off"
    class="f" style="top:53.76%;left:24.11%;width:24.67%;height:1.39%;">
</div>
<input type="text" name="household_members_count" value="{{ old('household_members_count', $farmer->household_members_count) }}" autocomplete="off"
    class="f" style="top:55.91%;left:25.78%;width:22.88%;height:1.39%;">
<input type="text" name="household_male_count" value="{{ old('household_male_count', $farmer->household_male_count) }}" autocomplete="off"
    class="f" style="top:57.72%;left:13.19%;width:10.51%;height:1.39%;">
<input type="text" name="household_female_count" value="{{ old('household_female_count', $farmer->household_female_count) }}" autocomplete="off"
    class="f" style="top:57.75%;left:37.94%;width:10.51%;height:1.39%;">

<input type="checkbox" class="cb" id="asc_y" style="top:51.04%;left:84.31%;"
    onchange="if(this.checked){document.getElementById('asc_n').checked=false;document.getElementById('asc_h').value='1';}"
    {{ old('is_farmers_association_member', $farmer->is_farmers_association_member) == '1' ? 'checked' : '' }}>
<input type="checkbox" class="cb" id="asc_n" style="top:51.04%;left:90.27%;"
    onchange="if(this.checked){document.getElementById('asc_y').checked=false;document.getElementById('asc_h').value='0';}"
    {{ old('is_farmers_association_member', $farmer->is_farmers_association_member) == '0' ? 'checked' : '' }}>
<input type="hidden" name="is_farmers_association_member" id="asc_h" value="{{ old('is_farmers_association_member', $farmer->is_farmers_association_member ? '1' : '0') }}">
<input type="text" name="farmers_association_name" value="{{ old('farmers_association_name', $farmer->farmers_association_name) }}" autocomplete="off"
    class="f" style="top:52.57%;left:60.28%;width:34.29%;height:1.25%;">

<input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $farmer->emergency_contact_name) }}" autocomplete="off"
    class="f" style="top:55.61%;left:65.70%;width:28.97%;height:1.25%;">
<div style="position:absolute;top:57.63%;left:65.69%;width:28%;height:1.74%;display:flex;" id="conBoxes"></div>
<input type="text" id="conReal" maxlength="11" inputmode="numeric"
    value="{{ old('emergency_contact_number', $farmer->emergency_contact_number) }}"
    style="position:absolute;top:57.63%;left:65.69%;width:28%;height:1.74%;opacity:0;border:none;">
<input type="hidden" name="emergency_contact_number" id="conH" value="{{ old('emergency_contact_number', $farmer->emergency_contact_number) }}">

@php $lhMap = ['farmer'=>'20.44%','farmworker'=>'35.32%','fisherfolk'=>'60.42%','agri_youth'=>'80.50%']; $lhVal = old('main_livelihood', $farmer->main_livelihood); @endphp
@foreach($lhMap as $val=>$left)
<input type="checkbox" class="cb lh-cb" data-val="{{ $val }}" style="top:61.93%;left:{{ $left }};" {{ $lhVal == $val ? 'checked' : '' }}>
@endforeach
<input type="hidden" name="main_livelihood" id="lh_h" value="{{ $lhVal }}">

<input type="checkbox" class="cb" name="farming_rice" value="1" style="top:67.23%;left:5.60%;" {{ old('farming_rice', $farmer->farming_rice) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="farming_corn" value="1" style="top:69.19%;left:5.56%;" {{ old('farming_corn', $farmer->farming_corn) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="farming_other_crops" value="1" style="top:71.16%;left:5.63%;" {{ old('farming_other_crops', $farmer->farming_other_crops) ? 'checked' : '' }}>
<input type="text" name="farming_other_crops_specify" value="{{ old('farming_other_crops_specify', $farmer->farming_other_crops_specify) }}" autocomplete="off"
    class="f" style="top:71.87%;left:16.89%;width:16.94%;height:1.03%;">
<input type="checkbox" class="cb" name="farming_livestock" value="1" style="top:73.88%;left:5.51%;" {{ old('farming_livestock', $farmer->farming_livestock) ? 'checked' : '' }}>
<input type="text" name="farming_livestock_specify" value="{{ old('farming_livestock_specify', $farmer->farming_livestock_specify) }}" autocomplete="off"
    class="f" style="top:74.74%;left:16.86%;width:16.94%;height:1.03%;">
<input type="checkbox" class="cb" name="farming_poultry" value="1" style="top:76.50%;left:5.49%;" {{ old('farming_poultry', $farmer->farming_poultry) ? 'checked' : '' }}>
<input type="text" name="farming_poultry_specify" value="{{ old('farming_poultry_specify', $farmer->farming_poultry_specify) }}" autocomplete="off"
    class="f" style="top:77.38%;left:17.03%;width:16.94%;height:1.03%;">

<input type="checkbox" class="cb" name="farmwork_land_preparation" value="1" style="top:67.42%;left:35.96%;" {{ old('farmwork_land_preparation', $farmer->farmwork_land_preparation) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="farmwork_planting" value="1" style="top:69.34%;left:35.95%;" {{ old('farmwork_planting', $farmer->farmwork_planting) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="farmwork_cultivation" value="1" style="top:71.25%;left:35.99%;" {{ old('farmwork_cultivation', $farmer->farmwork_cultivation) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="farmwork_harvesting" value="1" style="top:73.16%;left:35.86%;" {{ old('farmwork_harvesting', $farmer->farmwork_harvesting) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="farmwork_others" value="1" style="top:75.06%;left:36.00%;" {{ old('farmwork_others', $farmer->farmwork_others) ? 'checked' : '' }}>
<input type="text" name="farmwork_others_specify" value="{{ old('farmwork_others_specify', $farmer->farmwork_others_specify) }}" autocomplete="off"
    class="f" style="top:77.35%;left:36.18%;width:16.07%;height:1.03%;">

<input type="checkbox" class="cb" name="fishing_capture" value="1" style="top:72.32%;left:55.64%;" {{ old('fishing_capture', $farmer->fishing_capture) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="fishing_processing" value="1" style="top:72.30%;left:66.69%;" {{ old('fishing_processing', $farmer->fishing_processing) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="fishing_aquaculture" value="1" style="top:73.68%;left:55.58%;" {{ old('fishing_aquaculture', $farmer->fishing_aquaculture) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="fishing_vending" value="1" style="top:73.65%;left:66.65%;" {{ old('fishing_vending', $farmer->fishing_vending) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="fishing_gleaning" value="1" style="top:74.99%;left:55.53%;" {{ old('fishing_gleaning', $farmer->fishing_gleaning) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="fishing_others" value="1" style="top:76.27%;left:55.62%;" {{ old('fishing_others', $farmer->fishing_others) ? 'checked' : '' }}>
<input type="text" name="fishing_others_specify" value="{{ old('fishing_others_specify', $farmer->fishing_others_specify) }}" autocomplete="off"
    class="f" style="top:77.21%;left:54.81%;width:21.85%;height:1.03%;">

<input type="checkbox" class="cb" name="agri_youth_farming_household" value="1" style="top:70.52%;left:79.28%;" {{ old('agri_youth_farming_household', $farmer->agri_youth_farming_household) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="agri_youth_formal_course" value="1" style="top:71.64%;left:79.31%;" {{ old('agri_youth_formal_course', $farmer->agri_youth_formal_course) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="agri_youth_nonformal_course" value="1" style="top:73.42%;left:79.32%;" {{ old('agri_youth_nonformal_course', $farmer->agri_youth_nonformal_course) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="agri_youth_participated_program" value="1" style="top:75.39%;left:79.28%;" {{ old('agri_youth_participated_program', $farmer->agri_youth_participated_program) ? 'checked' : '' }}>
<input type="checkbox" class="cb" name="agri_youth_others" value="1" style="top:77.34%;left:79.34%;" {{ old('agri_youth_others', $farmer->agri_youth_others) ? 'checked' : '' }}>
<input type="text" name="agri_youth_others_specify" value="{{ old('agri_youth_others_specify', $farmer->agri_youth_others_specify) }}" autocomplete="off"
    class="f" style="top:77.86%;left:79.28%;width:14.72%;height:0.74%;">

<input type="text" name="gross_annual_income_farming" value="{{ old('gross_annual_income_farming', $farmer->gross_annual_income_farming) }}" autocomplete="off"
    class="f" style="top:79.63%;left:35.85%;width:22.18%;height:1.0%;">
<input type="text" name="gross_annual_income_non_farming" value="{{ old('gross_annual_income_non_farming', $farmer->gross_annual_income_non_farming) }}" autocomplete="off"
    class="f" style="top:79.68%;left:72.69%;width:21.78%;height:1.0%;">

<input type="hidden" name="farm_location_barangay" value="{{ old('farm_location_barangay', $farmer->farm_location_barangay) }}">
<input type="hidden" name="farm_location_municipality" value="{{ old('farm_location_municipality', $farmer->farm_location_municipality) }}">
<input type="hidden" name="farm_location_province" value="{{ old('farm_location_province', $farmer->farm_location_province) }}">
<input type="hidden" name="land_area_hectares" value="{{ old('land_area_hectares', $farmer->land_area_hectares) }}">
<input type="hidden" name="land_holding_status" value="{{ old('land_holding_status', $farmer->land_holding_status) }}">
<input type="hidden" name="status" value="{{ old('status', $farmer->status) }}">

@php $refTopLefts = [18.38,20.42,22.97,25.03,27.65,29.66,32.24,34.32,36.40,38.94,41.02,43.11,45.16,47.22,49.33]; @endphp
@foreach($refTopLefts as $left)
<input type="text" readonly tabindex="-1" class="dg" style="top:14.64%;left:{{ $left }}%;width:2.08%;height:1.33%;color:#888;">
@endforeach

<input type="text" id="stubSurname" readonly tabindex="-1"
    class="f" style="top:90.55%;left:4.87%;width:43.16%;height:1.43%;color:#333;" value="{{ strtoupper($farmer->surname) }}">
<input type="text" id="stubFirstName" readonly tabindex="-1"
    class="f" style="top:90.55%;left:51.26%;width:43.16%;height:1.43%;color:#333;" value="{{ strtoupper($farmer->first_name) }}">
<input type="text" id="stubMiddleName" readonly tabindex="-1"
    class="f" style="top:93.73%;left:4.86%;width:43.16%;height:1.43%;color:#333;" value="{{ strtoupper($farmer->middle_name) }}">
<input type="text" id="stubExtName" readonly tabindex="-1"
    class="f" style="top:93.73%;left:50.39%;width:11.90%;height:1.43%;color:#333;" value="{{ strtoupper($farmer->extension_name) }}">

</div>

<div style="width:850px;margin:0 auto 32px auto;display:flex;gap:12px;">
    <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-semibold px-8 py-2.5 rounded-md transition text-sm">
        Save Changes
    </button>
    <a href="{{ route('farmers.show', $farmer) }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-8 py-2.5 rounded-md transition text-sm">
        Cancel
    </a>
</div>

</form>

<script>
document.querySelectorAll('.edu-cb').forEach(cb => cb.addEventListener('change', () => {
    document.querySelectorAll('.edu-cb').forEach(o => { if(o!==cb) o.checked=false; });
    document.getElementById('edu_h').value = cb.checked ? cb.dataset.val : '';
}));
document.querySelectorAll('.cs-cb').forEach(cb => cb.addEventListener('change', () => {
    document.querySelectorAll('.cs-cb').forEach(o => { if(o!==cb) o.checked=false; });
    document.getElementById('cs_h').value = cb.checked ? cb.dataset.val : '';
}));
document.querySelectorAll('.lh-cb').forEach(cb => cb.addEventListener('change', () => {
    document.querySelectorAll('.lh-cb').forEach(o => { if(o!==cb) o.checked=false; });
    document.getElementById('lh_h').value = cb.checked ? cb.dataset.val : '';
}));
document.querySelectorAll('.rel-cb').forEach(cb => cb.addEventListener('change', () => {
    document.querySelectorAll('.rel-cb').forEach(o => { if(o!==cb) o.checked=false; });
    document.getElementById('religion_h').value = cb.checked ? cb.dataset.val : '';
    document.getElementById('religionOther').style.display = (cb.checked && cb.dataset.val==='others') ? 'block' : 'none';
}));

function setupDigitRow(boxesId, realId, hiddenId, length, boxWidthPct) {
    const boxesDiv = document.getElementById(boxesId);
    const real = document.getElementById(realId);
    const hidden = document.getElementById(hiddenId);

    function render() {
        const val = real.value.padEnd(length, ' ');
        boxesDiv.innerHTML = '';
        for (let i = 0; i < length; i++) {
            const box = document.createElement('div');
            box.textContent = val[i].trim();
            box.style.cssText = `width:${boxWidthPct}%;height:100%;text-align:center;font-size:10px;font-weight:bold;border:1px solid #999;background:rgba(255,255,255,0.4);display:flex;align-items:center;justify-content:center;`;
            boxesDiv.appendChild(box);
        }
        hidden.value = real.value;
    }

    real.addEventListener('input', () => {
        real.value = real.value.replace(/\D/g, '').slice(0, length);
        render();
    });

    render();
    boxesDiv.addEventListener('click', () => real.focus());
}

setupDigitRow('mobBoxes', 'mobReal', 'mobH', 11, 9);
setupDigitRow('landBoxes', 'landReal', 'landH', 10, 10);
setupDigitRow('dobBoxes', 'dobReal', 'dobH', 8, 12);
setupDigitRow('conBoxes', 'conReal', 'conH', 11, 9);

document.getElementById('farmerForm').addEventListener('submit', () => {
    const dob = document.getElementById('dobReal').value;
    if (dob.length === 8) document.getElementById('dobH').value = `${dob.slice(4,8)}-${dob.slice(0,2)}-${dob.slice(2,4)}`;
});
document.getElementById('farmerForm').addEventListener('submit', () => {
    const p1 = document.getElementById('pob1').value;
    const p2 = document.getElementById('pob2').value;
    const p3 = document.getElementById('pob3').value;
    document.getElementById('pobH').value = [p1,p2,p3].filter(Boolean).join(', ');
});

document.getElementById('photoInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('photoPreview');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>

@endsection