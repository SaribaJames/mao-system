<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Farmer Profile — {{ $farmer->reference_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #2D7A2D; padding-bottom: 10px; }
        .header h1 { color: #2D7A2D; font-size: 16px; margin: 0; }
        .header h2 { font-size: 13px; margin: 5px 0 0; }
        .header p { color: #666; margin: 3px 0; font-size: 10px; }
        .ref { font-size: 11px; color: #666; margin-top: 5px; }
        .section { margin-bottom: 15px; }
        .section-title { background: #2D7A2D; color: white; padding: 4px 10px; font-size: 11px; font-weight: bold; margin-bottom: 8px; }
        .grid { display: table; width: 100%; }
        .row { display: table-row; }
        .cell { display: table-cell; width: 50%; padding: 3px 5px; vertical-align: top; }
        .label { color: #888; font-size: 9px; text-transform: uppercase; }
        .value { font-weight: bold; font-size: 11px; margin-top: 1px; }
        .checkbox { display: inline-block; width: 10px; height: 10px; border: 1px solid #333; margin-right: 4px; text-align: center; line-height: 10px; font-size: 8px; }
        .checked { background: #2D7A2D; color: white; }
        .footer { text-align: center; margin-top: 30px; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .signature-area { margin-top: 40px; display: table; width: 100%; }
        .signature-box { display: table-cell; width: 33%; text-align: center; padding: 0 10px; }
        .signature-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; font-size: 10px; }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>Republic of the Philippines</h1>
    <h1>Municipal Agriculture Office</h1>
    <h2>Guinobatan, Albay</h2>
    <p>Farmer Profile Record</p>
    <p class="ref">Reference No: <strong>{{ $farmer->reference_number }}</strong> | Date Printed: {{ date('F d, Y') }}</p>
</div>

{{-- Part I: Personal Information --}}
<div class="section">
    <div class="section-title">PART I — PERSONAL INFORMATION</div>
    <div class="grid">
        <div class="row">
            <div class="cell">
                <div class="label">Full Name</div>
                <div class="value">{{ $farmer->surname }}, {{ $farmer->first_name }} {{ $farmer->middle_name }} {{ $farmer->extension_name }}</div>
            </div>
            <div class="cell">
                <div class="label">Sex</div>
                <div class="value">{{ ucfirst($farmer->sex) }}</div>
            </div>
        </div>
        <div class="row">
            <div class="cell">
                <div class="label">Date of Birth</div>
                <div class="value">{{ \Carbon\Carbon::parse($farmer->date_of_birth)->format('F d, Y') }}</div>
            </div>
            <div class="cell">
                <div class="label">Place of Birth</div>
                <div class="value">{{ $farmer->place_of_birth ?? '—' }}</div>
            </div>
        </div>
        <div class="row">
            <div class="cell">
                <div class="label">Civil Status</div>
                <div class="value">{{ ucfirst($farmer->civil_status ?? '—') }}</div>
            </div>
            <div class="cell">
                <div class="label">Mobile Number</div>
                <div class="value">{{ $farmer->mobile_number ?? '—' }}</div>
            </div>
        </div>
        <div class="row">
            <div class="cell">
                <div class="label">Address</div>
                <div class="value">{{ $farmer->house_lot_number }} {{ $farmer->street }}, {{ $farmer->barangay?->name }}, {{ $farmer->municipality }}, {{ $farmer->province }}</div>
            </div>
            <div class="cell">
                <div class="label">Religion</div>
                <div class="value">{{ $farmer->religion ?? '—' }}</div>
            </div>
        </div>
        <div class="row">
            <div class="cell">
                <div class="label">Highest Education</div>
                <div class="value">{{ ucfirst(str_replace('_', ' ', $farmer->highest_education ?? '—')) }}</div>
            </div>
            <div class="cell">
                <div class="label">Household Members</div>
                <div class="value">{{ $farmer->household_members_count ?? '—' }} ({{ $farmer->household_male_count ?? 0 }} Male, {{ $farmer->household_female_count ?? 0 }} Female)</div>
            </div>
        </div>
        <div class="row">
            <div class="cell">
                <div class="label">Mother's Maiden Name</div>
                <div class="value">{{ $farmer->mother_maiden_name ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="label">Spouse Name</div>
                <div class="value">{{ $farmer->spouse_name ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Special Classifications --}}
    <div style="margin-top: 8px; padding: 5px;">
        <div class="label" style="margin-bottom: 4px;">Special Classifications</div>
        <span class="checkbox {{ $farmer->is_pwd ? 'checked' : '' }}">{{ $farmer->is_pwd ? '✓' : '' }}</span> PWD &nbsp;&nbsp;
        <span class="checkbox {{ $farmer->is_4ps_beneficiary ? 'checked' : '' }}">{{ $farmer->is_4ps_beneficiary ? '✓' : '' }}</span> 4P's Beneficiary &nbsp;&nbsp;
        <span class="checkbox {{ $farmer->is_indigenous ? 'checked' : '' }}">{{ $farmer->is_indigenous ? '✓' : '' }}</span> Indigenous &nbsp;&nbsp;
        <span class="checkbox {{ $farmer->is_farmers_association_member ? 'checked' : '' }}">{{ $farmer->is_farmers_association_member ? '✓' : '' }}</span> Farmers Association Member
        @if($farmer->is_farmers_association_member && $farmer->farmers_association_name)
            <br><span style="margin-left: 15px; font-size: 10px;">Association: <strong>{{ $farmer->farmers_association_name }}</strong></span>
        @endif
    </div>
</div>

{{-- Part II: Farm Profile --}}
<div class="section">
    <div class="section-title">PART II — FARM PROFILE</div>
    <div class="grid">
        <div class="row">
            <div class="cell">
                <div class="label">Main Livelihood</div>
                <div class="value">{{ ucfirst(str_replace('_', ' ', $farmer->main_livelihood ?? '—')) }}</div>
            </div>
            <div class="cell">
                <div class="label">Land Holding Status</div>
                <div class="value">{{ ucfirst(str_replace('_', ' ', $farmer->land_holding_status ?? '—')) }}</div>
            </div>
        </div>
        <div class="row">
            <div class="cell">
                <div class="label">Farm Location</div>
                <div class="value">{{ $farmer->farm_location_barangay }}, {{ $farmer->farm_location_municipality }}, {{ $farmer->farm_location_province }}</div>
            </div>
            <div class="cell">
                <div class="label">Land Area</div>
                <div class="value">{{ $farmer->land_area_hectares ? $farmer->land_area_hectares . ' hectares' : '—' }}</div>
            </div>
        </div>
        <div class="row">
            <div class="cell">
                <div class="label">Gross Annual Income (Farming)</div>
                <div class="value">{{ $farmer->gross_annual_income_farming ? '₱' . number_format($farmer->gross_annual_income_farming, 2) : '—' }}</div>
            </div>
            <div class="cell">
                <div class="label">Gross Annual Income (Non-Farming)</div>
                <div class="value">{{ $farmer->gross_annual_income_non_farming ? '₱' . number_format($farmer->gross_annual_income_non_farming, 2) : '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Farming Activities --}}
    <div style="margin-top: 8px; padding: 5px;">
        <div class="label" style="margin-bottom: 4px;">Type of Farming Activity</div>
        <span class="checkbox {{ $farmer->farming_rice ? 'checked' : '' }}">{{ $farmer->farming_rice ? '✓' : '' }}</span> Rice &nbsp;&nbsp;
        <span class="checkbox {{ $farmer->farming_corn ? 'checked' : '' }}">{{ $farmer->farming_corn ? '✓' : '' }}</span> Corn &nbsp;&nbsp;
        <span class="checkbox {{ $farmer->farming_livestock ? 'checked' : '' }}">{{ $farmer->farming_livestock ? '✓' : '' }}</span> Livestock &nbsp;&nbsp;
        <span class="checkbox {{ $farmer->farming_poultry ? 'checked' : '' }}">{{ $farmer->farming_poultry ? '✓' : '' }}</span> Poultry &nbsp;&nbsp;
        <span class="checkbox {{ $farmer->farming_other_crops ? 'checked' : '' }}">{{ $farmer->farming_other_crops ? '✓' : '' }}</span> Other Crops
        @if($farmer->farming_other_crops && $farmer->farming_other_crops_specify)
            ({{ $farmer->farming_other_crops_specify }})
        @endif
    </div>
</div>

{{-- Emergency Contact --}}
<div class="section">
    <div class="section-title">EMERGENCY CONTACT</div>
    <div class="grid">
        <div class="row">
            <div class="cell">
                <div class="label">Contact Person</div>
                <div class="value">{{ $farmer->emergency_contact_name ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="label">Contact Number</div>
                <div class="value">{{ $farmer->emergency_contact_number ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Signature Area --}}
<div class="signature-area">
    <div class="signature-box">
        <div class="signature-line">
            Farmer's Signature<br>
            <small>{{ $farmer->first_name }} {{ $farmer->surname }}</small>
        </div>
    </div>
    <div class="signature-box">
        <div class="signature-line">
            Recorded By<br>
            <small>{{ $farmer->registeredBy?->name ?? '—' }}</small>
        </div>
    </div>
    <div class="signature-box">
        <div class="signature-line">
            Municipal Agriculturist<br>
            <small>Guinobatan, Albay</small>
        </div>
    </div>
</div>

<div class="footer">
    <p>Municipal Agriculture Office — Guinobatan, Albay | Digital Farmer Records and Service Management System</p>
    <p>Generated on {{ date('F d, Y h:i A') }} | Reference: {{ $farmer->reference_number }}</p>
</div>

</body>
</html>