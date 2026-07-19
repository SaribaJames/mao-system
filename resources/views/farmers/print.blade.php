<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>RSBSA Enrollment Form — {{ $farmer->reference_number }}</title>
<style>
    @page { size: A4 portrait; margin: 0; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, Helvetica, sans-serif; }
    .rsbsa-page { position:relative; width:595px; height:893px; }
    .rsbsa-page img { width:595px; height:893px; display:block; }
    .v { position:absolute; font-size:11px; font-weight:bold; color:#000; white-space:nowrap; overflow:hidden; }
    .chk { position:absolute; width:13px; height:13px; font-size:11px; font-weight:bold; text-align:center; line-height:13px; color:#000; }
    .dg { position:absolute; text-align:center; font-size:10px; font-weight:bold; color:#000; }
    .page-break { page-break-before: always; }
</style>
</head>
<h1 style="color:red; font-size:60px;">TEST123 IF YOU SEE THIS THE FILE UPDATED</h1>
<body>

@php
    $religion = $farmer->religion;
    $relChristian = $religion === 'Christianity';
    $relIslam = $religion === 'Islam';
    $relOthers = $religion && !$relChristian && !$relIslam;

    $pobParts = $farmer->place_of_birth ? explode(', ', $farmer->place_of_birth) : [];
    $mobileDigits = str_split(preg_replace('/\D/', '', $farmer->mobile_number ?? ''));
    $landlineDigits = str_split(preg_replace('/\D/', '', $farmer->landline_number ?? ''));
    $contactDigits = str_split(preg_replace('/\D/', '', $farmer->emergency_contact_number ?? ''));
    $dobDigits = $farmer->date_of_birth ? str_split($farmer->date_of_birth->format('mdY')) : [];
@endphp

<div class="rsbsa-page">
<img src="{{ str_replace('\\', '/', public_path('images/rsbsa_page-1-print.jpg')) }}" alt="RSBSA Page 1">

@if($farmer->photo)
<img src="{{ str_replace('\\', '/', public_path('storage/' . $farmer->photo)) }}"
     style="position:absolute; top:25.1px; left:442.8px; width:127.6px; height:124.4px; object-fit:cover;">
@endif

<div class="chk" style="top:114.4px;left:108.6px;">{{ $farmer->enrollment_type === 'new' ? 'X' : '' }}</div>
<div class="chk" style="top:114.1px;left:151.1px;">{{ $farmer->enrollment_type === 'updating' ? 'X' : '' }}</div>

<div class="v" style="top:166.5px;left:33.2px;width:250.3px;">{{ strtoupper($farmer->surname) }}</div>
<div class="v" style="top:165.1px;left:311.8px;width:250.3px;">{{ strtoupper($farmer->first_name) }}</div>

<div class="v" style="top:191.1px;left:33.4px;width:250.3px;">{{ strtoupper($farmer->middle_name) }}</div>
<div class="v" style="top:191.5px;left:303.3px;width:58.3px;">{{ strtoupper($farmer->extension_name) }}</div>

<div class="chk" style="top:209.9px;left:455.5px;">{{ $farmer->sex === 'male' ? 'X' : '' }}</div>
<div class="chk" style="top:209.3px;left:504.2px;">{{ $farmer->sex === 'female' ? 'X' : '' }}</div>

<div class="v" style="top:228.1px;left:70.8px;width:157.4px;">{{ $farmer->house_lot_number }}</div>
<div class="v" style="top:228.5px;left:239.1px;width:157.4px;">{{ $farmer->street }}</div>
<div class="v" style="top:228.2px;left:406.9px;width:157.4px;">{{ $farmer->barangay?->name }}</div>

<div class="v" style="top:256.2px;left:70.2px;width:157.4px;">{{ $farmer->municipality }}</div>
<div class="v" style="top:256.2px;left:239.1px;width:157.4px;">{{ $farmer->province }}</div>
<div class="v" style="top:256.7px;left:406.9px;width:157.4px;">{{ $farmer->region }}</div>

@foreach($mobileDigits as $i => $d)
<div class="dg" style="top:292.7px;left:{{ 32.07 + $i*8.33 }}px;width:7.7px;">{{ $d }}</div>
@endforeach

@foreach($landlineDigits as $i => $d)
<div class="dg" style="top:292.2px;left:{{ 176.24 + $i*9.52 }}px;width:8.3px;">{{ $d }}</div>
@endforeach

@php
$eduMap = [
    'pre_school'=>['top'=>'299.8px','left'=>'315.2px'], 'junior_high_k12'=>['top'=>'299.8px','left'=>'403.5px'], 'vocational'=>['top'=>'299.9px','left'=>'499.1px'],
    'elementary'=>['top'=>'310.9px','left'=>'315.4px'], 'senior_high_k12'=>['top'=>'311.2px','left'=>'403.2px'], 'post_graduate'=>['top'=>'311.3px','left'=>'498.9px'],
    'high_school_non_k12'=>['top'=>'323.3px','left'=>'315.2px'], 'college'=>['top'=>'323.3px','left'=>'403.7px'], 'none'=>['top'=>'323.4px','left'=>'499.1px'],
];
@endphp
@foreach($eduMap as $val => $pos)
<div class="chk" style="top:{{ $pos['top'] }};left:{{ $pos['left'] }};">{{ $farmer->highest_education === $val ? 'X' : '' }}</div>
@endforeach

@foreach($dobDigits as $i => $d)
<div class="dg" style="top:322.0px;left:{{ 32.43 + $i*12.5 }}px;width:11.9px;">{{ $d }}</div>
@endforeach

<div class="v" style="top:319.2px;left:152.0px;width:139.3px;font-size:9px;">{{ $pobParts[0] ?? '' }}</div>
<div class="v" style="top:330.7px;left:151.3px;width:67.2px;font-size:9px;">{{ $pobParts[1] ?? '' }}</div>
<div class="v" style="top:330.8px;left:223.1px;width:67.2px;font-size:9px;">{{ $pobParts[2] ?? '' }}</div>

<div class="chk" style="top:344.0px;left:452.1px;">{{ $farmer->is_pwd ? 'X' : '' }}</div>
<div class="chk" style="top:344.0px;left:494.5px;">{{ !$farmer->is_pwd ? 'X' : '' }}</div>

<div class="chk" style="top:359.9px;left:77.5px;">{{ $relChristian ? 'X' : '' }}</div>
<div class="chk" style="top:360.7px;left:136.6px;">{{ $relIslam ? 'X' : '' }}</div>
<div class="chk" style="top:360.4px;left:172.1px;">{{ $relOthers ? 'X' : '' }}</div>
<div class="v" style="top:359.2px;left:236.4px;width:57.2px;font-size:9px;">{{ $relOthers ? $religion : '' }}</div>

<div class="chk" style="top:366.9px;left:450.4px;">{{ $farmer->is_4ps_beneficiary ? 'X' : '' }}</div>
<div class="chk" style="top:367.0px;left:491.8px;">{{ !$farmer->is_4ps_beneficiary ? 'X' : '' }}</div>

@php $csMap = ['single'=>'93.7px','married'=>'139.0px','widowed'=>'187.5px','separated'=>'241.9px']; @endphp
@foreach($csMap as $val=>$left)
<div class="chk" style="top:379.3px;left:{{ $left }};">{{ $farmer->civil_status === $val ? 'X' : '' }}</div>
@endforeach

<div class="chk" style="top:381.8px;left:450.2px;">{{ $farmer->is_indigenous ? 'X' : '' }}</div>
<div class="chk" style="top:381.8px;left:491.8px;">{{ !$farmer->is_indigenous ? 'X' : '' }}</div>
<div class="v" style="top:393.5px;left:359.4px;width:202.2px;font-size:9px;">{{ $farmer->indigenous_group_name }}</div>

<div class="v" style="top:400.4px;left:101.3px;width:188.7px;">{{ $farmer->spouse_name }}</div>

<div class="chk" style="top:416.3px;left:397.1px;">{{ $farmer->has_government_id ? 'X' : '' }}</div>
<div class="chk" style="top:416.3px;left:439.5px;">{{ !$farmer->has_government_id ? 'X' : '' }}</div>
<div class="v" style="top:426.0px;left:396.6px;width:166.7px;font-size:9px;">{{ $farmer->government_id_type }}</div>
<div class="v" style="top:436.7px;left:396.5px;width:166.7px;font-size:9px;">{{ $farmer->government_id_number }}</div>

<div class="v" style="top:428.6px;left:101.2px;width:188.7px;">{{ $farmer->mother_maiden_name }}</div>

<div class="chk" style="top:449.7px;left:134.3px;">{{ $farmer->is_household_head ? 'X' : '' }}</div>
<div class="chk" style="top:450.2px;left:176.8px;">{{ !$farmer->is_household_head ? 'X' : '' }}</div>
@if(!$farmer->is_household_head)
<div class="v" style="top:463.1px;left:143.3px;width:146.8px;font-size:9px;">{{ $farmer->household_head_name }}</div>
<div class="v" style="top:480.1px;left:143.5px;width:146.8px;font-size:9px;">{{ $farmer->household_head_relationship }}</div>
@endif
<div class="v" style="top:499.3px;left:153.4px;width:136.1px;font-size:9px;">{{ $farmer->household_members_count }}</div>
<div class="v" style="top:515.4px;left:78.5px;width:62.5px;font-size:9px;">{{ $farmer->household_male_count }}</div>
<div class="v" style="top:515.7px;left:225.7px;width:62.5px;font-size:9px;">{{ $farmer->household_female_count }}</div>

<div class="chk" style="top:455.8px;left:501.6px;">{{ $farmer->is_farmers_association_member ? 'X' : '' }}</div>
<div class="chk" style="top:455.8px;left:537.1px;">{{ !$farmer->is_farmers_association_member ? 'X' : '' }}</div>
<div class="v" style="top:469.5px;left:358.7px;width:204.0px;font-size:9px;">{{ $farmer->farmers_association_name }}</div>

<div class="v" style="top:496.6px;left:390.9px;width:172.4px;font-size:9px;">{{ $farmer->emergency_contact_name }}</div>
@foreach($contactDigits as $i => $d)
<div class="dg" style="top:514.6px;left:{{ 390.86 + $i*14.88 }}px;width:13.7px;">{{ $d }}</div>
@endforeach

@php $lhMap = ['farmer'=>'121.6px','farmworker'=>'210.2px','fisherfolk'=>'359.5px','agri_youth'=>'479.0px']; @endphp
@foreach($lhMap as $val=>$left)
<div class="chk" style="top:553.0px;left:{{ $left }};">{{ $farmer->main_livelihood === $val ? 'X' : '' }}</div>
@endforeach

<div class="chk" style="top:600.4px;left:33.3px;">{{ $farmer->farming_rice ? 'X' : '' }}</div>
<div class="chk" style="top:617.9px;left:33.1px;">{{ $farmer->farming_corn ? 'X' : '' }}</div>
<div class="chk" style="top:635.5px;left:33.5px;">{{ $farmer->farming_other_crops ? 'X' : '' }}</div>
<div class="v" style="top:641.8px;left:100.5px;width:100.8px;font-size:9px;">{{ $farmer->farming_other_crops_specify }}</div>
<div class="chk" style="top:659.7px;left:32.8px;">{{ $farmer->farming_livestock ? 'X' : '' }}</div>
<div class="v" style="top:667.4px;left:100.3px;width:100.8px;font-size:9px;">{{ $farmer->farming_livestock_specify }}</div>
<div class="chk" style="top:683.1px;left:32.7px;">{{ $farmer->farming_poultry ? 'X' : '' }}</div>
<div class="v" style="top:691.0px;left:101.3px;width:100.8px;font-size:9px;">{{ $farmer->farming_poultry_specify }}</div>

<div class="chk" style="top:602.1px;left:214.0px;">{{ $farmer->farmwork_land_preparation ? 'X' : '' }}</div>
<div class="chk" style="top:619.2px;left:213.9px;">{{ $farmer->farmwork_planting ? 'X' : '' }}</div>
<div class="chk" style="top:636.3px;left:214.1px;">{{ $farmer->farmwork_cultivation ? 'X' : '' }}</div>
<div class="chk" style="top:653.3px;left:213.4px;">{{ $farmer->farmwork_harvesting ? 'X' : '' }}</div>
<div class="chk" style="top:670.3px;left:214.2px;">{{ $farmer->farmwork_others ? 'X' : '' }}</div>
<div class="v" style="top:690.7px;left:215.3px;width:95.6px;font-size:9px;">{{ $farmer->farmwork_others_specify }}</div>

<div class="chk" style="top:645.8px;left:331.1px;">{{ $farmer->fishing_capture ? 'X' : '' }}</div>
<div class="chk" style="top:645.6px;left:396.8px;">{{ $farmer->fishing_processing ? 'X' : '' }}</div>
<div class="chk" style="top:658.0px;left:330.7px;">{{ $farmer->fishing_aquaculture ? 'X' : '' }}</div>
<div class="chk" style="top:657.7px;left:396.6px;">{{ $farmer->fishing_vending ? 'X' : '' }}</div>
<div class="chk" style="top:669.7px;left:330.4px;">{{ $farmer->fishing_gleaning ? 'X' : '' }}</div>
<div class="chk" style="top:681.1px;left:330.9px;">{{ $farmer->fishing_others ? 'X' : '' }}</div>
<div class="v" style="top:689.5px;left:326.1px;width:130.0px;font-size:9px;">{{ $farmer->fishing_others_specify }}</div>

<div class="chk" style="top:629.7px;left:471.7px;">{{ $farmer->agri_youth_farming_household ? 'X' : '' }}</div>
<div class="chk" style="top:639.7px;left:471.9px;">{{ $farmer->agri_youth_formal_course ? 'X' : '' }}</div>
<div class="chk" style="top:655.6px;left:472.0px;">{{ $farmer->agri_youth_nonformal_course ? 'X' : '' }}</div>
<div class="chk" style="top:673.2px;left:471.7px;">{{ $farmer->agri_youth_participated_program ? 'X' : '' }}</div>
<div class="chk" style="top:690.6px;left:472.1px;">{{ $farmer->agri_youth_others ? 'X' : '' }}</div>
<div class="v" style="top:695.3px;left:471.7px;width:87.6px;font-size:8px;">{{ $farmer->agri_youth_others_specify }}</div>

<div class="v" style="top:711.1px;left:213.3px;width:132.0px;font-size:9px;">{{ $farmer->gross_annual_income_farming ? number_format($farmer->gross_annual_income_farming, 2) : '' }}</div>
<div class="v" style="top:711.5px;left:432.5px;width:129.6px;font-size:9px;">{{ $farmer->gross_annual_income_non_farming ? number_format($farmer->gross_annual_income_non_farming, 2) : '' }}</div>

<div class="v" style="top:808.6px;left:29.0px;width:256.8px;">{{ strtoupper($farmer->surname) }}</div>
<div class="v" style="top:808.6px;left:305.0px;width:256.8px;">{{ strtoupper($farmer->first_name) }}</div>
<div class="v" style="top:837.0px;left:28.9px;width:256.8px;">{{ strtoupper($farmer->middle_name) }}</div>
<div class="v" style="top:837.0px;left:299.8px;width:70.8px;">{{ strtoupper($farmer->extension_name) }}</div>

</div>

</body>
</html>