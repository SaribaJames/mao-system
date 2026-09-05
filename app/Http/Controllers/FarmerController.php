<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerController extends Controller
{
    public function index(Request $request)
    {
        // activePrograms is eager-loaded so the program badge on each row
        // doesn't fire a query per farmer.
        $query = Farmer::with(['barangay', 'activePrograms']);

        if (Auth::user()->isBarangayUser()) {
            // Barangay user can only see their own barangay's farmers,
            // and can filter by registration status via tabs (default: all)
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
            if ($barangayId) {
                $query->where('barangay_id', $barangayId);
            } else {
                $query->whereRaw('1 = 0');
            }

            if ($request->status && in_array($request->status, ['pending', 'approved', 'rejected'])) {
                $query->where('registration_status', $request->status);
            }
        } else {
            // Admin/staff only see approved farmers in the official registry.
            // Pending/rejected registrations are reviewed separately via
            // the Pending Registrations page.
            $query->where('registration_status', 'approved');
        }

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('surname', 'like', '%' . $search . '%');
            });
        }

        // Filter by barangay (admin/staff only)
        if ($request->barangay && !Auth::user()->isBarangayUser()) {
            $query->where('barangay_id', $request->barangay);
        }

        $farmers = $query->latest()->paginate(15);
        return view('farmers.index', compact('farmers'));
    }

    public function create()
    {
        $barangays = Barangay::orderBy('name')->get();
        return view('farmers.create', compact('barangays'));
    }

    /**
     * Cleans up numeric-field input from the form before validation/saving:
     * strips thousands-separator commas, and converts empty strings to null
     * since MySQL decimal columns reject '' but accept NULL.
     */
    protected function cleanNumericFields(Request $request): void
    {
        $numericFields = [
            'gross_annual_income_farming',
            'gross_annual_income_non_farming',
            'land_area_hectares',
        ];

        $cleaned = [];
        foreach ($numericFields as $field) {
            $value = $request->input($field);
            if ($value === null || $value === '') {
                $cleaned[$field] = null;
            } else {
                $cleaned[$field] = str_replace(',', '', $value);
            }
        }

        $request->merge($cleaned);
    }

    public function store(Request $request)
    {
        $this->cleanNumericFields($request);

        $validated = $request->validate([
            'surname' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'extension_name' => 'nullable|string|max:20',
            'sex' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'mobile_number' => 'nullable|string|max:20',
            'barangay_id' => 'nullable|exists:barangays,id',
        ]);

        // If barangay rep, automatically assign their barangay
        // and set registration as pending (requires admin/DA approval)
        $barangayId = $request->barangay_id;
        $registrationStatus = 'approved';
        if (Auth::user()->isBarangayUser()) {
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
            $registrationStatus = 'pending';
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('farmer-photos', 'cloudinary');
        }

        $farmer = Farmer::create(array_merge(
            $request->except('_token', 'photo'),
            [
                'photo' => $photoPath,
                'registered_by' => Auth::id(),
                'barangay_id' => $barangayId,
                'registration_status' => $registrationStatus,
                'religion' => $request->religion === 'others' ? $request->religion_other : $request->religion,
                'is_household_head' => $request->boolean('is_household_head'),
                'is_pwd' => $request->boolean('is_pwd'),
                'is_4ps_beneficiary' => $request->boolean('is_4ps_beneficiary'),
                'is_indigenous' => $request->boolean('is_indigenous'),
                'has_government_id' => $request->boolean('has_government_id'),
                'is_farmers_association_member' => $request->boolean('is_farmers_association_member'),
                'farming_rice' => $request->boolean('farming_rice'),
                'farming_corn' => $request->boolean('farming_corn'),
                'farming_other_crops' => $request->boolean('farming_other_crops'),
                'farming_livestock' => $request->boolean('farming_livestock'),
                'farming_poultry' => $request->boolean('farming_poultry'),
                'farmwork_land_preparation' => $request->boolean('farmwork_land_preparation'),
                'farmwork_planting' => $request->boolean('farmwork_planting'),
                'farmwork_cultivation' => $request->boolean('farmwork_cultivation'),
                'farmwork_harvesting' => $request->boolean('farmwork_harvesting'),
                'farmwork_others' => $request->boolean('farmwork_others'),
                'fishing_capture' => $request->boolean('fishing_capture'),
                'fishing_aquaculture' => $request->boolean('fishing_aquaculture'),
                'fishing_processing' => $request->boolean('fishing_processing'),
                'fishing_vending' => $request->boolean('fishing_vending'),
                'fishing_gleaning' => $request->boolean('fishing_gleaning'),
                'fishing_others' => $request->boolean('fishing_others'),
                'agri_youth_farming_household' => $request->boolean('agri_youth_farming_household'),
                'agri_youth_formal_course' => $request->boolean('agri_youth_formal_course'),
                'agri_youth_nonformal_course' => $request->boolean('agri_youth_nonformal_course'),
                'agri_youth_participated_program' => $request->boolean('agri_youth_participated_program'),
                'agri_youth_others' => $request->boolean('agri_youth_others'),
            ]
        ));

        $this->saveCoconutProfile($request, $farmer);


        $this->saveFarmParcels($request, $farmer);

        return redirect()->route('farmers.index')
            ->with('success', "Farmer {$farmer->full_name} registered successfully!");
    }

    public function show(Farmer $farmer)
    {
        $farmer->load(['barangay', 'registeredBy', 'farmParcels',
            'coconutFarmProfile.coconutTreeRecords', 'coconutFarmProfile.farmIncomeRecords',
            'enrollments.program', 'enrollments.processedBy']);
        return view('farmers.show', compact('farmer'));
    }

    public function edit(Farmer $farmer)
    {
        $barangays = Barangay::orderBy('name')->get();
        $farmer->load('coconutFarmProfile.coconutTreeRecords', 'coconutFarmProfile.farmIncomeRecords');
        return view('farmers.edit', compact('farmer', 'barangays'));
    }

    public function update(Request $request, Farmer $farmer)
    {
        $this->cleanNumericFields($request);

        $updateData = $request->except('_token', '_method', 'photo');

        if ($request->hasFile('photo')) {
            $updateData['photo'] = $request->file('photo')->store('farmer-photos', 'cloudinary');
        }

        $farmer->update(array_merge(
            $updateData,
            [
                'religion' => $request->religion === 'others' ? $request->religion_other : $request->religion,
                'is_household_head' => $request->boolean('is_household_head'),
                'is_pwd' => $request->boolean('is_pwd'),
                'is_4ps_beneficiary' => $request->boolean('is_4ps_beneficiary'),
                'is_indigenous' => $request->boolean('is_indigenous'),
                'has_government_id' => $request->boolean('has_government_id'),
                'is_farmers_association_member' => $request->boolean('is_farmers_association_member'),
                'farming_rice' => $request->boolean('farming_rice'),
                'farming_corn' => $request->boolean('farming_corn'),
                'farming_other_crops' => $request->boolean('farming_other_crops'),
                'farming_livestock' => $request->boolean('farming_livestock'),
                'farming_poultry' => $request->boolean('farming_poultry'),
                'farmwork_land_preparation' => $request->boolean('farmwork_land_preparation'),
                'farmwork_planting' => $request->boolean('farmwork_planting'),
                'farmwork_cultivation' => $request->boolean('farmwork_cultivation'),
                'farmwork_harvesting' => $request->boolean('farmwork_harvesting'),
                'farmwork_others' => $request->boolean('farmwork_others'),
                'fishing_capture' => $request->boolean('fishing_capture'),
                'fishing_aquaculture' => $request->boolean('fishing_aquaculture'),
                'fishing_processing' => $request->boolean('fishing_processing'),
                'fishing_vending' => $request->boolean('fishing_vending'),
                'fishing_gleaning' => $request->boolean('fishing_gleaning'),
                'fishing_others' => $request->boolean('fishing_others'),
                'agri_youth_farming_household' => $request->boolean('agri_youth_farming_household'),
                'agri_youth_formal_course' => $request->boolean('agri_youth_formal_course'),
                'agri_youth_nonformal_course' => $request->boolean('agri_youth_nonformal_course'),
                'agri_youth_participated_program' => $request->boolean('agri_youth_participated_program'),
                'agri_youth_others' => $request->boolean('agri_youth_others'),
            ]
        ));

        $this->saveCoconutProfile($request, $farmer);

        $this->saveFarmParcels($request, $farmer);

        return redirect()->route('farmers.show', $farmer)
            ->with('success', 'Farmer record updated successfully!');
    }

    public function destroy(Farmer $farmer)
    {
        $farmer->delete();
        return redirect()->route('farmers.index')
            ->with('success', 'Farmer record deleted successfully!');
    }


    public function print(Farmer $farmer)
    {
        $farmer->load(['barangay']);

        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P', 'pt', [595, 893]);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();
        $templateId = $pdf->setSourceFile(resource_path('pdf/rsbsa_template.pdf'));
        $tpl = $pdf->importPage(1);
        $pdf->useTemplate($tpl, 0, 0, 595, 893);

        // 2x2 ID photo, drawn into the "2x2 PICTURE" box on the template
        if ($farmer->photo_url) {
            $tmpPhoto = null;
            try {
                $imageContents = @file_get_contents($farmer->photo_url);
                if ($imageContents !== false && $imageContents !== '') {
                    $tmpPhoto = tempnam(sys_get_temp_dir(), 'farmer_photo_') . '.jpg';
                    file_put_contents($tmpPhoto, $imageContents);
                    // Box coordinates (pt) measured off the template: x 435.6-572.4, y 25.2-147.6
                    $pdf->Image($tmpPhoto, 435.6, 25.2, 136.8, 122.4, '', '', '', true, 300, '', false, false, 0, 'CM', false, false, false);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning(
                    "Farmer #{$farmer->id}: could not draw photo on printed form: {$e->getMessage()}"
                );
            } finally {
                if ($tmpPhoto && file_exists($tmpPhoto)) {
                    @unlink($tmpPhoto);
                }
            }
        }

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);

        $write = function ($x, $y, $text, $size = 9) use ($pdf) {
            $text = $text === null ? '' : (string) $text;
            $pdf->SetFont('helvetica', 'B', $size);
            $yAdjusted = $y + max(0, (9 - $size) * 1.3);
            $pdf->SetXY($x, $yAdjusted);
            $pdf->Cell(0, 10, $text, 0, 0, 'L');
        };

        $writeBoxed = function ($x, $y, $text, $size = 9) use ($pdf) {
            $text = $text === null ? '' : (string) $text;
            $pdf->SetFont('helvetica', 'B', $size);
            $pdf->SetXY($x, $y + 1.5);
            $pdf->Cell(8, 10, $text, 0, 0, 'C');
        };

        $mark = function ($x, $y) use ($pdf) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetXY($x, $y);
            $pdf->Cell(13, 13, 'X', 0, 0, 'C');
        };

        if ($farmer->enrollment_type === 'new')
            $mark(106.6, 111.4);
        if ($farmer->enrollment_type === 'updating')
            $mark(148.1, 111.4);

        // Reference Number and Date Administered — real database fields, edited via edit.blade.php after DA approval
        // Date Administered (8 boxed digits: MMDDYYYY)
        if ($farmer->date_administered) {
            $dateAdminLefts = [211.5, 223.2, 235.3, 247.0, 258.8, 271.0, 282.5, 294.5];
            $dateAdminDigits = str_split($farmer->date_administered->format('mdY'));
            foreach ($dateAdminDigits as $i => $d) {
                $writeBoxed($dateAdminLefts[$i], 112.2, $d, 8);
            }
        }

        // Reference Number (15 boxed digits: Region-Province-CityMuni-Barangay)
        $refLefts = [109.4, 121.5, 136.7, 148.9, 164.5, 176.5, 191.9, 204.2, 216.6, 231.7, 244.1, 256.5, 268.7, 281.0, 293.6];
        $refDigits = str_split(preg_replace('/\D/', '', $farmer->reference_number ?? ''));
        foreach ($refDigits as $i => $d) {
            if (isset($refLefts[$i])) {
                $writeBoxed($refLefts[$i], 130.7, $d, 8);
            }
        }

        $write(137.5, 172.5, strtoupper($farmer->surname ?? ''));
        $write(402.5, 172.5, strtoupper($farmer->first_name ?? ''));
        $write(137.5, 200.5, strtoupper($farmer->middle_name ?? ''));
        $write(320.5, 200.5, strtoupper($farmer->extension_name ?? ''));

        if ($farmer->sex === 'male')
            $mark(452.5, 206.9);
        if ($farmer->sex === 'female')
            $mark(501.2, 206.9);

        $write(70.8, 228.1, $farmer->house_lot_number, 8);
        $write(239.1, 228.5, $farmer->street, 8);
        $write(406.9, 228.2, $farmer->barangay?->name, 8);
        $write(70.2, 256.2, $farmer->municipality, 8);
        $write(239.1, 256.2, $farmer->province, 8);
        $write(406.9, 256.7, $farmer->region, 8);

        $mobile = str_split(preg_replace('/\D/', '', $farmer->mobile_number ?? ''));
        foreach ($mobile as $i => $d)
            $writeBoxed(35.5 + $i * 10.70, 291.3, $d, 8);
        $landline = str_split(preg_replace('/\D/', '', $farmer->landline_number ?? ''));
        foreach ($landline as $i => $d)
            $writeBoxed(179.5 + $i * 10.52, 292.2, $d, 8);

        $eduMap = [
            'pre_school' => [312.2, 296.8],
            'junior_high_k12' => [400.5, 296.8],
            'vocational' => [496.1, 296.8],
            'elementary' => [312.2, 308.3],
            'senior_high_k12' => [400.2, 308.3],
            'post_graduate' => [495.9, 308.3],
            'high_school_non_k12' => [312.2, 320.3],
            'college' => [400.2, 320.3],
            'none' => [496.1, 320.3],
        ];
        if (isset($eduMap[$farmer->highest_education])) {
            [$x, $y] = $eduMap[$farmer->highest_education];
            $mark($x, $y);
        }

        if ($farmer->date_of_birth) {
            $dob = str_split($farmer->date_of_birth->format('mdY'));
            foreach ($dob as $i => $d)
                $writeBoxed(35.5 + $i * 13.3, 322.0, $d, 8);
        }

        $pob = $farmer->place_of_birth ? explode(', ', $farmer->place_of_birth) : [];
        $write(200.0, 315.2, $pob[0] ?? '', 7);
        $write(165.3, 326.7, $pob[1] ?? '', 7);
        $write(235.1, 326.7, $pob[2] ?? '', 7);

        $farmer->is_pwd ? $mark(449.1, 341.0) : $mark(491.8, 341.0);

        $rel = $farmer->religion;
        if ($rel === 'Christianity')
            $mark(73.5, 355.9);
        elseif ($rel === 'Islam')
            $mark(133.6, 357.7);
        elseif ($rel) {
            $mark(169.1, 358.4);
            $write(233.4, 359.2, $rel, 7);
        }

        $farmer->is_4ps_beneficiary ? $mark(447.4, 363.9) : $mark(488.8, 363.9);

        $csMap = ['single' => 90.7, 'married' => 136.0, 'widowed' => 184.5, 'separated' => 238.9];
        if (isset($csMap[$farmer->civil_status]))
            $mark($csMap[$farmer->civil_status], 376.3);

        $farmer->is_indigenous ? $mark(447.5, 378.9) : $mark(488.8, 378.89);
        $write(359.4, 393.5, $farmer->indigenous_group_name, 7);

        $write(101.3, 400.4, $farmer->spouse_name, 9);

        $farmer->has_government_id ? $mark(394.1, 413.3) : $mark(436.5, 413.3);
        $write(396.6, 424.0, $farmer->government_id_type, 7);
        $write(396.5, 433.9, $farmer->government_id_number, 7);

        $write(101.2, 428.6, $farmer->mother_maiden_name, 9);

        $farmer->is_household_head ? $mark(131.4, 447.7) : $mark(174.4, 447.2);
        if (!$farmer->is_household_head) {
            $write(143.3, 463.1, $farmer->household_head_name, 7);
            $write(143.5, 480.1, $farmer->household_head_relationship, 7);
        }
        $write(153.4, 499.3, $farmer->household_members_count, 7);
        $write(78.5, 515.4, $farmer->household_male_count, 7);
        $write(225.7, 515.7, $farmer->household_female_count, 7);

        $farmer->is_farmers_association_member ? $mark(499.0, 452.8) : $mark(534.5, 452.8);
        $write(358.7, 469.5, $farmer->farmers_association_name, 7);

        $write(390.9, 496.6, $farmer->emergency_contact_name, 7);
        $contact = str_split(preg_replace('/\D/', '', $farmer->emergency_contact_number ?? ''));
        foreach ($contact as $i => $d)
            $writeBoxed(394.0 + $i * 14.98, 516.6, $d, 8);

        $lhMap = ['farmer' => 118.9, 'farmworker' => 207.0, 'fisherfolk' => 356.5, 'agri_youth' => 476.0];
        if (isset($lhMap[$farmer->main_livelihood]))
            $mark($lhMap[$farmer->main_livelihood], 550.0);

        if ($farmer->farming_rice)
            $mark(30.3, 597.4);
        if ($farmer->farming_corn)
            $mark(30.3, 614.5);
        if ($farmer->farming_other_crops) {
            $mark(30.3, 632.5);
            $write(100.5, 641.8, $farmer->farming_other_crops_specify, 7);
        }
        if ($farmer->farming_livestock) {
            $mark(30.3, 656.5);
            $write(100.3, 667.4, $farmer->farming_livestock_specify, 7);
        }
        if ($farmer->farming_poultry) {
            $mark(30.3, 680.5);
            $write(101.3, 691.0, $farmer->farming_poultry_specify, 7);
        }

        if ($farmer->farmwork_land_preparation)
            $mark(211.2, 599.1);
        if ($farmer->farmwork_planting)
            $mark(211.2, 616.2);
        if ($farmer->farmwork_cultivation)
            $mark(211.2, 633.3);
        if ($farmer->farmwork_harvesting)
            $mark(211.2, 650.3);
        if ($farmer->farmwork_others) {
            $mark(211.2, 667.3);
            $write(215.3, 690.7, $farmer->farmwork_others_specify, 7);
        }

        if ($farmer->fishing_capture)
            $mark(328.1, 642.8);
        if ($farmer->fishing_processing)
            $mark(393.8, 642.6);
        if ($farmer->fishing_aquaculture)
            $mark(328.1, 655.0);
        if ($farmer->fishing_vending)
            $mark(393.8, 655.0);
        if ($farmer->fishing_gleaning)
            $mark(328.1, 666.5);
        if ($farmer->fishing_others) {
            $mark(328.1, 678.1);
            $write(326.1, 689.5, $farmer->fishing_others_specify, 7);
        }

        if ($farmer->agri_youth_farming_household)
            $mark(468.5, 626.5);
        if ($farmer->agri_youth_formal_course)
            $mark(468.5, 636.5);
        if ($farmer->agri_youth_nonformal_course)
            $mark(468.5, 652.5);
        if ($farmer->agri_youth_participated_program)
            $mark(468.5, 670.5);
        if ($farmer->agri_youth_others) {
            $mark(468.5, 687.5);
            $write(468.5, 695.3, $farmer->agri_youth_others_specify, 6);
        }

        $write(218.3, 709.3, $farmer->gross_annual_income_farming ? number_format($farmer->gross_annual_income_farming, 2) : '', 7);
        $write(438.3, 709.3, $farmer->gross_annual_income_non_farming ? number_format($farmer->gross_annual_income_non_farming, 2) : '', 7);

        $write(130.0, 812.6, strtoupper($farmer->surname ?? ''));
        $write(410.0, 812.6, strtoupper($farmer->first_name ?? ''));
        $write(135.0, 837.0, strtoupper($farmer->middle_name ?? ''));
        $write(320.8, 837.0, strtoupper($farmer->extension_name ?? ''));

        // ═══ PAGE 2 — FARM PARCEL INFORMATION ═══
        $farmer->load('farmParcels');

        $mark2 = function ($x, $y) use ($pdf) {
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->SetXY($x - 0.4, $y - 1.6);
            $pdf->Cell(6.0, 8.0, 'X', 0, 0, 'C');
        };

        $P2 = [
            'no_parcels' => [114.3, 24.3],
            1 => [
                'brgy' => [100.0, 98.3],
                'muni' => [100.0, 108.7],
                'area' => [142.4, 124.7],
                'doc' => [136.0, 144.2],
                'anc_y' => [179.1, 137.1],
                'anc_n' => [220.8, 137.2],
                'arb_y' => [179.1, 156.4],
                'arb_n' => [221.0, 156.3],
                'reg' => [144.7, 172.7],
                'oth' => [61.7, 172.5],
                'ten' => [61.6, 182.6],
                'les' => [61.7, 193.4],
                'oth_t' => [175.2, 168.2],
                'ten_t' => [156.3, 178.3],
                'les_t' => [156.5, 189.0],
                'crop' => [274.1, 96.7],
                'size' => [346.9, 97.5],
                'head' => [386.4, 97.3],
                'ftype' => [426.5, 97.8],
                'org' => [470.6, 97.5],
                'rem' => [513.4, 97.0]
            ],
            2 => [
                'brgy' => [99.7, 204.7],
                'muni' => [99.7, 215.4],
                'area' => [142.1, 231.6],
                'doc' => [136.0, 250.8],
                'anc_y' => [179.0, 244.1],
                'anc_n' => [220.9, 243.8],
                'arb_y' => [179.3, 263.1],
                'arb_n' => [220.9, 263.0],
                'reg' => [144.6, 279.5],
                'oth' => [61.8, 279.3],
                'ten' => [61.6, 289.5],
                'les' => [61.6, 300.1],
                'oth_t' => [174.9, 275.4],
                'ten_t' => [156.0, 285.8],
                'les_t' => [155.0, 295.9],
                'crop' => [274.6, 202.7],
                'size' => [347.0, 202.7],
                'head' => [387.3, 203.5],
                'ftype' => [426.6, 203.4],
                'org' => [470.9, 203.1],
                'rem' => [514.0, 203.2]
            ],
            3 => [
                'brgy' => [103.5, 312.2],
                'muni' => [103.5, 322.2],
                'area' => [145.6, 339.0],
                'doc' => [138.4, 358.2],
                'anc_y' => [182.2, 349.2],
                'anc_n' => [224.0, 349.1],
                'arb_y' => [182.3, 368.4],
                'arb_n' => [224.1, 368.4],
                'reg' => [147.7, 384.6],
                'oth' => [64.8, 384.5],
                'ten' => [64.9, 394.7],
                'les' => [64.9, 405.3],
                'oth_t' => [178.4, 382.7],
                'ten_t' => [159.5, 393.1],
                'les_t' => [158.1, 403.2],
                'crop' => [274.3, 308.9],
                'size' => [348.0, 309.0],
                'head' => [387.5, 309.5],
                'ftype' => [426.8, 309.4],
                'org' => [470.5, 309.1],
                'rem' => [513.9, 309.4]
            ],
            'date' => [31.5, 559.5],
            'pname' => [144.5, 559.5],
        ];

        $tpl2 = $pdf->importPage(2);
        $pdf->AddPage();
        $pdf->useTemplate($tpl2, 0, 0, 595, 893);

        if ($farmer->farmParcels->count() > 0) {
            $write($P2['no_parcels'][0], $P2['no_parcels'][1], $farmer->farmParcels->count(), 8);
        }

        foreach ($farmer->farmParcels as $parcel) {
            $n = $parcel->parcel_number;
            if (!isset($P2[$n]))
                continue;
            $g = $P2[$n];

            $write($g['brgy'][0], $g['brgy'][1], $parcel->farm_location_barangay, 7);
            $write($g['muni'][0], $g['muni'][1], $parcel->farm_location_municipality, 7);
            $write($g['area'][0], $g['area'][1], $parcel->total_farm_area_ha ? number_format($parcel->total_farm_area_ha, 2) : '', 7);
            $write($g['doc'][0], $g['doc'][1], $parcel->ownership_document_code, 7);

            $parcel->within_ancestral_domain
                ? $mark2($g['anc_y'][0], $g['anc_y'][1])
                : $mark2($g['anc_n'][0], $g['anc_n'][1]);

            $parcel->agrarian_reform_beneficiary
                ? $mark2($g['arb_y'][0], $g['arb_y'][1])
                : $mark2($g['arb_n'][0], $g['arb_n'][1]);

            switch ($parcel->ownership_type) {
                case 'registered_owner':
                    $mark2($g['reg'][0], $g['reg'][1]);
                    break;
                case 'others':
                    $mark2($g['oth'][0], $g['oth'][1]);
                    $write($g['oth_t'][0], $g['oth_t'][1], $parcel->owner_name, 7);
                    break;
                case 'tenant':
                    $mark2($g['ten'][0], $g['ten'][1]);
                    $write($g['ten_t'][0], $g['ten_t'][1], $parcel->owner_name, 7);
                    break;
                case 'lessee':
                    $mark2($g['les'][0], $g['les'][1]);
                    $write($g['les_t'][0], $g['les_t'][1], $parcel->owner_name, 7);
                    break;
            }

            $write($g['crop'][0], $g['crop'][1], $parcel->crop_commodity, 7);
            $write($g['size'][0], $g['size'][1], $parcel->size_ha ? number_format($parcel->size_ha, 2) : '', 7);
            $write($g['head'][0], $g['head'][1], $parcel->no_of_head, 7);
            $write($g['ftype'][0], $g['ftype'][1], $parcel->farm_type, 7);
            $write($g['org'][0], $g['org'][1], $parcel->organic_practitioner ? 'Y' : 'N', 7);
            $write($g['rem'][0], $g['rem'][1], $parcel->remarks, 7);
        }

        $write($P2['date'][0], $P2['date'][1], now()->format('m/d/Y'), 7);
        $write($P2['pname'][0], $P2['pname'][1], strtoupper(trim(($farmer->first_name ?? '') . ' ' . ($farmer->middle_name ?? '') . ' ' . ($farmer->surname ?? ''))), 7);

        return response($pdf->Output('Farmer-' . $farmer->reference_number . '.pdf', 'S'), 200)
            ->header('Content-Type', 'application/pdf');
    }


    protected function saveFarmParcels(Request $request, Farmer $farmer): void
    {
        $farmer->farmParcels()->delete();

        foreach (($request->parcel_barangay ?? []) as $i => $barangay) {
            $crop = $request->parcel_crop[$i] ?? null;
            if (blank($barangay) && blank($crop))
                continue;

            $type = $request->parcel_ownership_type[$i] ?? null;
            $ownerName = match ($type) {
                'tenant' => $request->parcel_tenant_name[$i] ?? null,
                'lessee' => $request->parcel_lessee_name[$i] ?? null,
                'others' => $request->parcel_others_specify[$i] ?? null,
                default => null,
            };
            $num = fn($v) => is_numeric($v) ? $v : null;

            $farmer->farmParcels()->create([
                'parcel_number' => $i + 1,
                'farm_location_barangay' => $barangay,
                'farm_location_municipality' => $request->parcel_municipality[$i] ?? null,
                'total_farm_area_ha' => $num($request->parcel_area[$i] ?? null),
                'within_ancestral_domain' => ($request->parcel_ancestral[$i] ?? null) === '1',
                'agrarian_reform_beneficiary' => ($request->parcel_arb[$i] ?? null) === '1',
                'ownership_document_code' => $request->parcel_doc_code[$i] ?? null,
                'ownership_type' => $type,
                'owner_name' => $ownerName,
                'crop_commodity' => $crop,
                'size_ha' => $num($request->parcel_size[$i] ?? null),
                'no_of_head' => $num($request->parcel_no_head[$i] ?? null),
                'farm_type' => $num($request->parcel_farm_type[$i] ?? null),
                'organic_practitioner' => strtoupper(trim($request->parcel_organic[$i] ?? '')) === 'Y',
                'remarks' => $request->parcel_remarks[$i] ?? null,
            ]);
        }
    }
    /**
     * Saves the Page 2 coconut supplement (single-value profile fields) plus
     * its two repeatable-row tables (coconut trees, farm incomes/expenses).
     * Only runs if the form actually submitted coconut-related data — a
     * farmer with no coconut activity simply won't have a profile row.
     */
    protected function saveCoconutProfile(Request $request, Farmer $farmer): void
    {
        if (!$request->filled('coconut_land_holding_status') && !$request->has('tree_variety_code')) {
            return;
        }

        $profile = $farmer->coconutFarmProfile()->updateOrCreate(
            ['farmer_id' => $farmer->id],
            [
                'coconut_land_holding_status' => $request->coconut_land_holding_status,
                'parcel_no' => $request->parcel_no,
                'coconut_farm_location_province' => $request->coconut_farm_location_province,
                'coconut_farm_location_municipality' => $request->coconut_farm_location_municipality,
                'coconut_farm_location_barangay' => $request->coconut_farm_location_barangay,
                'land_area_absolute' => $request->land_area_absolute,
                'land_area_coconut' => $request->land_area_coconut,
                'land_area_intercrop' => $request->land_area_intercrop,
                'land_area_other_crop' => $request->land_area_other_crop,
                'land_area_idle' => $request->land_area_idle,
                'area_classification' => $request->area_classification,
                'organic_certified' => $request->boolean('organic_certified'),
                'gap_certified' => $request->boolean('gap_certified'),
                'processing_dryer' => $request->boolean('processing_dryer'),
                'processing_dryer_specify' => $request->processing_dryer_specify,
                'processing_charcoal_kiln' => $request->boolean('processing_charcoal_kiln'),
                'processing_decort_machine' => $request->boolean('processing_decort_machine'),
                'processing_others' => $request->boolean('processing_others'),
                'processing_others_specify' => $request->processing_others_specify,
                'distance_to_market_km' => $request->distance_to_market_km,
                'coco_harvesting_cycle' => $request->coco_harvesting_cycle,
                'coco_harvesting_cycle_others_specify' => $request->coco_harvesting_cycle_others_specify,
                'husks_utilization_codes' => $request->husks_utilization_codes,
                'shell_utilization_codes' => $request->shell_utilization_codes,
                'water_utilization_codes' => $request->water_utilization_codes,
                'sold_to_whom_codes' => $request->sold_to_whom_codes,
                'sold_where_codes' => $request->sold_where_codes,
                'doc_certificate_land_transfer' => $request->boolean('doc_certificate_land_transfer'),
                'doc_emancipation_patent' => $request->boolean('doc_emancipation_patent'),
                'doc_individual_cloa' => $request->boolean('doc_individual_cloa'),
                'doc_collective_cloa' => $request->boolean('doc_collective_cloa'),
                'doc_co_ownership_cloa' => $request->boolean('doc_co_ownership_cloa'),
                'doc_agricultural_sales_patent' => $request->boolean('doc_agricultural_sales_patent'),
                'doc_homestead_patent' => $request->boolean('doc_homestead_patent'),
                'doc_free_patent' => $request->boolean('doc_free_patent'),
                'doc_extrajudicial_partition' => $request->boolean('doc_extrajudicial_partition'),
                'doc_certificate_of_title' => $request->boolean('doc_certificate_of_title'),
                'doc_ancestral_domain_title' => $request->boolean('doc_ancestral_domain_title'),
                'doc_ancestral_land_title' => $request->boolean('doc_ancestral_land_title'),
                'doc_tax_declaration' => $request->boolean('doc_tax_declaration'),
                'doc_deed_of_sale' => $request->boolean('doc_deed_of_sale'),
                'doc_dar_id' => $request->boolean('doc_dar_id'),
                'owner_or_tenant_lastname' => $request->owner_or_tenant_lastname,
                'owner_or_tenant_firstname' => $request->owner_or_tenant_firstname,
                'owner_or_tenant_middlename' => $request->owner_or_tenant_middlename,
                'owner_or_tenant_extension' => $request->owner_or_tenant_extension,
                'worker_farm_location_province' => $request->worker_farm_location_province,
                'worker_farm_location_municipality' => $request->worker_farm_location_municipality,
                'worker_farm_location_barangay' => $request->worker_farm_location_barangay,
                'kind_of_work_codes' => $request->kind_of_work_codes,
                'monthly_income' => $request->monthly_income,
                'days_working_per_week' => $request->days_working_per_week,
                'days_working_per_month' => $request->days_working_per_month,
                'days_working_per_year' => $request->days_working_per_year,
                'interviewed_by' => $request->interviewed_by,
                'encoded_by' => $request->encoded_by,
                'date_applied' => $request->date_applied,
            ]
        );

        // Repeatable rows — simplest reliable approach for an edit form:
        // wipe and recreate from whatever was submitted this time.
        $profile->coconutTreeRecords()->delete();
        if ($request->has('tree_variety_code')) {
            foreach ($request->tree_variety_code as $i => $variety) {
                if (blank($variety)) {
                    continue;
                }
                $profile->coconutTreeRecords()->create([
                    'variety_code' => $variety,
                    'year_planted' => $request->tree_year_planted[$i] ?? null,
                    'planting_pattern_code' => $request->tree_planting_pattern_code[$i] ?? null,
                    'planting_distance_code' => $request->tree_planting_distance_code[$i] ?? null,
                    'no_of_trees' => $request->tree_no_of_trees[$i] ?? null,
                    'ave_nut_per_tree_year' => $request->tree_ave_nut_per_tree_year[$i] ?? null,
                ]);
            }
        }

        $profile->farmIncomeRecords()->delete();
        if ($request->has('income_type_code')) {
            foreach ($request->income_type_code as $i => $type) {
                if (blank($type)) {
                    continue;
                }
                $profile->farmIncomeRecords()->create([
                    'income_type_code' => $type,
                    'quantity_per_hectare_year' => $request->income_quantity[$i] ?? null,
                    'unit' => $request->income_unit[$i] ?? null,
                    'unit_other_specify' => $request->income_unit_other_specify[$i] ?? null,
                    'unit_price' => $request->income_unit_price[$i] ?? null,
                    'expense_type_code' => $request->expense_type_code[$i] ?? null,
                    'expense_amount' => $request->expense_amount[$i] ?? null,
                ]);
            }
        }
    }

    public function createDb()
    {
        $barangays = Barangay::orderBy('name')->get();
        return view('farmers.create_db', compact('barangays'));
    }

    public function pendingRegistrations()
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $farmers = Farmer::where('registration_status', 'pending')
            ->with(['barangay', 'registeredBy'])
            ->latest()
            ->paginate(15);
        return view('farmers.pending', compact('farmers'));
    }

    public function approveRegistration(Farmer $farmer)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $farmer->update([
            'registration_status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return redirect()->back()->with('success', "Farmer {$farmer->full_name} registration approved.");
    }

    public function rejectRegistration(Request $request, Farmer $farmer)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $request->validate(['reason' => 'required|string|max:500']);
        $farmer->update([
            'registration_status' => 'rejected',
            'registration_rejection_reason' => $request->reason,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return redirect()->back()->with('success', "Farmer {$farmer->full_name} registration rejected.");
    }
}