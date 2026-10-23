<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\LivestockInsuranceApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestockInsuranceController extends Controller
{
    public function index()
    {
        $query = LivestockInsuranceApplication::with('farmer')->latest();

        // A barangay rep only sees applications for farmers in their barangay.
        if (Auth::user()->isBarangayUser()) {
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
            $barangayId
                ? $query->whereHas('farmer', fn ($q) => $q->where('barangay_id', $barangayId))
                : $query->whereRaw('1 = 0');
        }

        $applications = $query->paginate(15);

        return view('forms.livestock-insurance-list', compact('applications'));
    }

    public function create()
    {
        $farmersQuery = Farmer::where('registration_status', 'approved')->orderBy('surname');

        // Reps file for their own farmers only.
        if (Auth::user()->isBarangayUser()) {
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
            $barangayId
                ? $farmersQuery->where('barangay_id', $barangayId)
                : $farmersQuery->whereRaw('1 = 0');
        }

        $farmers = $farmersQuery->get();

        return view('forms.livestock-insurance', compact('farmers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'cover_type' => 'required|in:commercial,non_commercial,special',
            'animal_type' => 'required|in:cattle,carabao,swine,poultry,horse,goat,other',
            'purpose' => 'required|in:fattening,draft,broilers,pullets,breeding,dairy,layers,parent_stock',
        ]);

        // Build the 6-row animals table from parallel arrays submitted by the form
        $animals = [];
        if ($request->has('animal_male')) {
            foreach ($request->animal_male as $i => $male) {
                $row = [
                    'male' => $male,
                    'female' => $request->animal_female[$i] ?? null,
                    'age' => $request->animal_age[$i] ?? null,
                    'breed' => $request->animal_breed[$i] ?? null,
                    'ear_mark' => $request->animal_ear_mark[$i] ?? null,
                    'basic_color' => $request->animal_basic_color[$i] ?? null,
                    'proof_ownership' => $request->animal_proof_ownership[$i] ?? null,
                ];
                // skip fully-empty rows
                if (array_filter($row)) {
                    $animals[] = $row;
                }
            }
        }

        // Strip thousands-separator commas from peso amount fields —
        // MySQL decimal columns reject "1,000" but accept "1000"
        $sumInsuredPerHead = $request->sum_insured_per_head
            ? str_replace(',', '', $request->sum_insured_per_head)
            : null;
        $totalSumInsured = $request->total_sum_insured
            ? str_replace(',', '', $request->total_sum_insured)
            : null;

        $application = LivestockInsuranceApplication::create([
            'farmer_id' => $validated['farmer_id'],
            'cover_type' => $validated['cover_type'],
            'is_indigenous' => $request->boolean('is_indigenous'),
            'tribe' => $request->tribe,
            'is_pwd' => $request->boolean('is_pwd'),
            'name_of_spouse' => $request->name_of_spouse,
            'address' => $request->address,
            'farm_address' => $request->farm_address,
            'contact_number' => $request->contact_number,
            'animal_type' => $validated['animal_type'],
            'animal_type_other' => $request->animal_type_other,
            'purpose' => $validated['purpose'],
            'animals' => $animals,
            'total_heads' => $request->total_heads,
            'source_of_stock' => $request->source_of_stock,
            'no_of_housing_units' => $request->no_of_housing_units,
            'birds_per_housing_unit' => $request->birds_per_housing_unit,
            'date_of_purchase' => $request->date_of_purchase,
            'sum_insured_per_head' => $sumInsuredPerHead,
            'total_sum_insured' => $totalSumInsured,
            'epidemic_coverage_1' => $request->epidemic_coverage_1,
            'epidemic_coverage_2' => $request->epidemic_coverage_2,
            'epidemic_coverage_3' => $request->epidemic_coverage_3,
            'assignee_name' => $request->assignee_name,
            'assignee_address' => $request->assignee_address,
            'assignee_contact' => $request->assignee_contact,
            'application_date' => $request->application_date,
            'name_of_proponent' => $request->name_of_proponent,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('livestock-insurance.index')
            ->with('success', 'Livestock insurance application saved successfully!');
    }

    public function print(LivestockInsuranceApplication $application)
    {
        $application->load('farmer');

        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P', 'pt', [612, 792]);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();
        $pdf->setSourceFile(resource_path('pdf/livestock_insurance_template.pdf'));
        $tpl = $pdf->importPage(1);
        $pdf->useTemplate($tpl, 0, 0, 612, 792);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);

        $write = function ($x, $y, $text, $size = 9) use ($pdf) {
            $pdf->SetFont('helvetica', '', $size);
            $pdf->SetXY($x, $y);
            $pdf->Cell(0, 10, $text, 0, 0, 'L');
        };

        $mark = function ($x, $y) use ($pdf) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetXY($x, $y);
            $pdf->Cell(10, 10, 'X', 0, 0, 'C');
        };

        $coverMap = [
            'commercial' => [91.6, 94.5],
            'non_commercial' => [246.5, 94.9],
            'special' => [427.3, 94.5],
        ];
        if (isset($coverMap[$application->cover_type])) {
            [$x, $y] = $coverMap[$application->cover_type];
            $mark($x, $y);
        }

        $write(174.8, 120.1, strtoupper($application->farmer->first_name . ' ' . $application->farmer->surname));

        $application->is_indigenous ? $mark(180.2, 137.4) : $mark(236.0, 137.0);
        $write(351.2, 133.7, $application->tribe, 8);

        $application->is_pwd ? $mark(181.2, 151.6) : $mark(236.0, 151.6);

        $write(174.8, 162.7, $application->name_of_spouse, 8);
        $write(174.8, 175.8, $application->address, 8);
        $write(174.2, 191.1, $application->farm_address, 8);
        $write(174.2, 205.9, $application->contact_number, 8);

        $animalMap = [
            'cattle' => [91.6, 247.5],
            'carabao' => [181.6, 247.0],
            'swine' => [289.9, 247.0],
            'poultry' => [397.2, 247.0],
            'horse' => [92.1, 260.3],
            'goat' => [182.1, 259.8],
            'other' => [290.3, 260.3],
        ];
        if (isset($animalMap[$application->animal_type])) {
            [$x, $y] = $animalMap[$application->animal_type];
            $mark($x, $y);
        }

        $purposeMap = [
            'fattening' => [92.1, 297.3],
            'draft' => [181.6, 298.2],
            'broilers' => [289.4, 298.2],
            'pullets' => [397.7, 297.7],
            'breeding' => [91.2, 311.0],
            'dairy' => [181.6, 310.5],
            'layers' => [289.4, 310.5],
            'parent_stock' => [397.2, 310.5],
        ];
        if (isset($purposeMap[$application->purpose])) {
            [$x, $y] = $purposeMap[$application->purpose];
            $mark($x, $y);
        }

        $tableCoords = [
            0 => [[90.1, 382.3], [139.3, 382.1], [190.2, 382.7], [236.3, 383.3], [312.8, 383.1], [367.6, 383.1], [408.0, 383.3]],
            1 => [[90.1, 395.8], [139.3, 395.8], [189.6, 396.3], [236.9, 395.2], [313.2, 396.4], [368.0, 395.9], [408.2, 395.6]],
            2 => [[90.1, 409.4], [139.3, 409.4], [189.6, 409.4], [236.9, 408.8], [313.2, 409.6], [368.0, 409.6], [407.8, 409.3]],
            3 => [[90.1, 421.8], [138.1, 422.4], [189.6, 422.4], [237.5, 422.4], [312.3, 422.9], [367.1, 422.9], [407.3, 423.0]],
            4 => [[90.1, 435.4], [138.1, 434.8], [189.0, 436.0], [236.9, 435.4], [313.2, 436.1], [367.6, 435.7], [407.8, 434.9]],
            5 => [[90.1, 449.0], [138.7, 448.4], [189.6, 448.4], [236.9, 448.4], [313.2, 449.4], [367.1, 448.4], [407.8, 449.0]],
        ];
        $animalKeys = ['male', 'female', 'age', 'breed', 'ear_mark', 'basic_color', 'proof_ownership'];
        foreach (($application->animals ?? []) as $i => $row) {
            if (!isset($tableCoords[$i])) break;
            foreach ($animalKeys as $j => $key) {
                if (!empty($row[$key])) {
                    [$x, $y] = $tableCoords[$i][$j];
                    $write($x, $y, $row[$key], 8);
                }
            }
        }

        $write(274.8, 464.0, $application->total_heads, 8);
        $write(258.9, 476.8, $application->source_of_stock, 8);
        $write(259.3, 488.2, $application->no_of_housing_units, 8);
        $write(259.8, 501.0, $application->birds_per_housing_unit, 8);
        $write(260.2, 513.2, $application->date_of_purchase?->format('m/d/Y'), 8);

        $write(253.8, 549.8, $application->sum_insured_per_head ? number_format($application->sum_insured_per_head, 2) : '', 8);
        $write(203.6, 565.3, $application->total_sum_insured ? number_format($application->total_sum_insured, 2) : '', 8);
        $write(127.8, 588.1, $application->epidemic_coverage_1, 8);
        $write(127.8, 601.9, $application->epidemic_coverage_2, 8);
        $write(127.3, 614.2, $application->epidemic_coverage_3, 8);

        $write(116.8, 628.8, $application->assignee_name, 8);
        $write(116.8, 639.7, $application->assignee_address, 8);
        $write(116.8, 652.5, $application->assignee_contact, 8);
        $write(116.8, 664.4, $application->application_date?->format('m/d/Y'), 8);
        $write(434.3, 709.6, $application->name_of_proponent, 9);

        $pdf->Output('livestock_insurance_' . $application->id . '.pdf', 'I');
        exit;
    }
}