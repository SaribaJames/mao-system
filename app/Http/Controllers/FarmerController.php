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
        $query = Farmer::with('barangay');

        // Barangay user can only see their own barangay's farmers
        if (Auth::user()->isBarangayUser()) {
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
            if ($barangayId) {
                $query->where('barangay_id', $barangayId);
            } else {
                $query->whereRaw('1 = 0');
            }
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

        $farmer = Farmer::create(array_merge(
            $request->except('_token'),
            [
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

        return redirect()->route('farmers.index')
            ->with('success', "Farmer {$farmer->full_name} registered successfully!");
    }

    public function show(Farmer $farmer)
    {
        $farmer->load(['barangay', 'registeredBy', 'coconutFarmProfile.coconutTreeRecords', 'coconutFarmProfile.farmIncomeRecords']);
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

        $farmer->update(array_merge(
            $request->except('_token', '_method'),
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

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);

        $write = function ($x, $y, $text, $size = 9) use ($pdf) {
            $pdf->SetFont('helvetica', 'B', $size);
            $yAdjusted = $y + max(0, (9 - $size) * 1.3);
            $pdf->SetXY($x, $yAdjusted);
            $pdf->Cell(0, 10, $text, 0, 0, 'L');
        };

        $writeBoxed = function ($x, $y, $text, $size = 9) use ($pdf) {
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
            $mark(108.6, 114.4);
        if ($farmer->enrollment_type === 'updating')
            $mark(151.1, 114.1);

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

        $write(33.2, 166.5, strtoupper($farmer->surname));
        $write(311.8, 165.1, strtoupper($farmer->first_name));
        $write(33.4, 191.1, strtoupper($farmer->middle_name));
        $write(303.3, 191.5, strtoupper($farmer->extension_name));

        if ($farmer->sex === 'male')
            $mark(455.5, 209.9);
        if ($farmer->sex === 'female')
            $mark(504.2, 209.3);

        $write(70.8, 228.1, $farmer->house_lot_number, 8);
        $write(239.1, 228.5, $farmer->street, 8);
        $write(406.9, 228.2, $farmer->barangay?->name, 8);
        $write(70.2, 256.2, $farmer->municipality, 8);
        $write(239.1, 256.2, $farmer->province, 8);
        $write(406.9, 256.7, $farmer->region, 8);

        $mobile = str_split(preg_replace('/\D/', '', $farmer->mobile_number ?? ''));
        foreach ($mobile as $i => $d) $writeBoxed(35.5 + $i*10.33, 292.7, $d, 8);
        $landline = str_split(preg_replace('/\D/', '', $farmer->landline_number ?? ''));
        foreach ($landline as $i => $d) $writeBoxed(179.5 + $i*10.52, 292.2, $d, 8);

        $eduMap = [
            'pre_school' => [315.2, 299.8],
            'junior_high_k12' => [403.5, 299.8],
            'vocational' => [499.1, 299.9],
            'elementary' => [315.4, 310.9],
            'senior_high_k12' => [403.2, 311.2],
            'post_graduate' => [498.9, 311.3],
            'high_school_non_k12' => [315.2, 323.3],
            'college' => [403.7, 323.3],
            'none' => [499.1, 323.4],
        ];
        if (isset($eduMap[$farmer->highest_education])) {
            [$x, $y] = $eduMap[$farmer->highest_education];
            $mark($x, $y);
        }

        if ($farmer->date_of_birth) {
            $dob = str_split($farmer->date_of_birth->format('mdY'));
            foreach ($dob as $i => $d) $writeBoxed(35.5 + $i*13.5, 322.0, $d, 8);
        }

        $pob = $farmer->place_of_birth ? explode(', ', $farmer->place_of_birth) : [];
        $write(152.0, 319.2, $pob[0] ?? '', 7);
        $write(151.3, 330.7, $pob[1] ?? '', 7);
        $write(223.1, 330.8, $pob[2] ?? '', 7);

        $farmer->is_pwd ? $mark(452.1, 344.0) : $mark(494.5, 344.0);

        $rel = $farmer->religion;
        if ($rel === 'Christianity')
            $mark(73.5, 355.9);
        elseif ($rel === 'Islam')
            $mark(132.6, 356.7);
        elseif ($rel) {
            $mark(170.1, 360.4);
            $write(233.4, 359.2, $rel, 7);
        }

        $farmer->is_4ps_beneficiary ? $mark(450.4, 366.9) : $mark(491.8, 367.0);

        $csMap = ['single' => 93.7, 'married' => 139.0, 'widowed' => 187.5, 'separated' => 241.9];
        if (isset($csMap[$farmer->civil_status]))
            $mark($csMap[$farmer->civil_status], 379.3);

        $farmer->is_indigenous ? $mark(450.2, 381.8) : $mark(491.8, 381.8);
        $write(359.4, 393.5, $farmer->indigenous_group_name, 7);

        $write(101.3, 400.4, $farmer->spouse_name, 9);

        $farmer->has_government_id ? $mark(397.1, 416.3) : $mark(439.5, 416.3);
        $write(396.6, 426.0, $farmer->government_id_type, 7);
        $write(396.5, 436.7, $farmer->government_id_number, 7);

        $write(101.2, 428.6, $farmer->mother_maiden_name, 9);

        $farmer->is_household_head ? $mark(134.3, 449.7) : $mark(176.8, 450.2);
        if (!$farmer->is_household_head) {
            $write(143.3, 463.1, $farmer->household_head_name, 7);
            $write(143.5, 480.1, $farmer->household_head_relationship, 7);
        }
        $write(153.4, 499.3, $farmer->household_members_count, 7);
        $write(78.5, 515.4, $farmer->household_male_count, 7);
        $write(225.7, 515.7, $farmer->household_female_count, 7);

        $farmer->is_farmers_association_member ? $mark(501.6, 455.8) : $mark(537.1, 455.8);
        $write(358.7, 469.5, $farmer->farmers_association_name, 7);

        $write(390.9, 496.6, $farmer->emergency_contact_name, 7);
        $contact = str_split(preg_replace('/\D/', '', $farmer->emergency_contact_number ?? ''));
        foreach ($contact as $i => $d) $writeBoxed(394.0 + $i*15.88, 514.6, $d, 8);

        $lhMap = ['farmer' => 121.6, 'farmworker' => 210.2, 'fisherfolk' => 359.5, 'agri_youth' => 479.0];
        if (isset($lhMap[$farmer->main_livelihood]))
            $mark($lhMap[$farmer->main_livelihood], 553.0);

        if ($farmer->farming_rice)
            $mark(33.3, 600.4);
        if ($farmer->farming_corn)
            $mark(33.1, 617.9);
        if ($farmer->farming_other_crops) {
            $mark(33.5, 635.5);
            $write(100.5, 641.8, $farmer->farming_other_crops_specify, 7);
        }
        if ($farmer->farming_livestock) {
            $mark(32.8, 659.7);
            $write(100.3, 667.4, $farmer->farming_livestock_specify, 7);
        }
        if ($farmer->farming_poultry) {
            $mark(32.7, 683.1);
            $write(101.3, 691.0, $farmer->farming_poultry_specify, 7);
        }

        if ($farmer->farmwork_land_preparation)
            $mark(214.0, 602.1);
        if ($farmer->farmwork_planting)
            $mark(213.9, 619.2);
        if ($farmer->farmwork_cultivation)
            $mark(214.1, 636.3);
        if ($farmer->farmwork_harvesting)
            $mark(213.4, 653.3);
        if ($farmer->farmwork_others) {
            $mark(214.2, 670.3);
            $write(215.3, 690.7, $farmer->farmwork_others_specify, 7);
        }

        if ($farmer->fishing_capture)
            $mark(331.1, 645.8);
        if ($farmer->fishing_processing)
            $mark(396.8, 645.6);
        if ($farmer->fishing_aquaculture)
            $mark(330.7, 658.0);
        if ($farmer->fishing_vending)
            $mark(396.6, 657.7);
        if ($farmer->fishing_gleaning)
            $mark(330.4, 669.7);
        if ($farmer->fishing_others) {
            $mark(330.9, 681.1);
            $write(326.1, 689.5, $farmer->fishing_others_specify, 7);
        }

        if ($farmer->agri_youth_farming_household)
            $mark(471.7, 629.7);
        if ($farmer->agri_youth_formal_course)
            $mark(471.9, 639.7);
        if ($farmer->agri_youth_nonformal_course)
            $mark(472.0, 655.6);
        if ($farmer->agri_youth_participated_program)
            $mark(471.7, 673.2);
        if ($farmer->agri_youth_others) {
            $mark(472.1, 690.6);
            $write(471.7, 695.3, $farmer->agri_youth_others_specify, 6);
        }

        $write(213.3, 711.1, $farmer->gross_annual_income_farming ? number_format($farmer->gross_annual_income_farming, 2) : '', 7);
        $write(432.5, 711.5, $farmer->gross_annual_income_non_farming ? number_format($farmer->gross_annual_income_non_farming, 2) : '', 7);

        $write(29.0, 808.6, strtoupper($farmer->surname));
        $write(305.0, 808.6, strtoupper($farmer->first_name));
        $write(28.9, 837.0, strtoupper($farmer->middle_name));
        $write(299.8, 837.0, strtoupper($farmer->extension_name));

        return response($pdf->Output('Farmer-' . $farmer->reference_number . '.pdf', 'S'), 200)
            ->header('Content-Type', 'application/pdf');
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