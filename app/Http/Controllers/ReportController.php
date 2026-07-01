<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\FarmerRequest;
use App\Models\Stock;
use App\Models\ServiceRecord;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Farmer Statistics
        $totalFarmers       = Farmer::count();
        $activeFarmers      = Farmer::where('status', 'active')->count();
        $farmersByBarangay  = Farmer::with('barangay')
            ->select('barangay_id', DB::raw('count(*) as total'))
            ->groupBy('barangay_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $farmersByLivelihood = Farmer::select('main_livelihood', DB::raw('count(*) as total'))
            ->whereNotNull('main_livelihood')
            ->groupBy('main_livelihood')
            ->get();

        $farmersBySex = Farmer::select('sex', DB::raw('count(*) as total'))
            ->groupBy('sex')
            ->get();

        // Monthly farmer registrations (current year)
        $monthlyRegistrations = Farmer::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Request Statistics
        $totalRequests     = FarmerRequest::count();
        $pendingRequests   = FarmerRequest::where('status', 'pending')->count();
        $approvedRequests  = FarmerRequest::where('status', 'approved')->count();
        $completedRequests = FarmerRequest::where('status', 'completed')->count();
        $rejectedRequests  = FarmerRequest::where('status', 'rejected')->count();

        $requestsByType = FarmerRequest::select('request_type', DB::raw('count(*) as total'))
            ->groupBy('request_type')
            ->orderByDesc('total')
            ->get();

        // Stock Statistics
        $totalStock     = Stock::sum('total_stock');
        $releasedStock  = Stock::sum('released_stock');
        $remainingStock = Stock::sum('remaining_stock');
        $stockByCategory = Stock::select('category', DB::raw('sum(remaining_stock) as total'))
            ->groupBy('category')
            ->get();

        // Service Record Statistics
        $totalServices    = ServiceRecord::count();
        $completedServices = ServiceRecord::where('status', 'completed')->count();
        $servicesByType   = ServiceRecord::select('service_type', DB::raw('count(*) as total'))
            ->groupBy('service_type')
            ->orderByDesc('total')
            ->get();

        $monthlyServices = ServiceRecord::select(
                DB::raw('MONTH(service_date) as month'),
                DB::raw('count(*) as total')
            )
            ->whereYear('service_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Build monthly data arrays for charts
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $registrationData = [];
        $serviceData = [];
        foreach (range(1, 12) as $month) {
            $registrationData[] = $monthlyRegistrations->get($month)?->total ?? 0;
            $serviceData[]      = $monthlyServices->get($month)?->total ?? 0;
        }

        return view('reports.index', compact(
            'totalFarmers', 'activeFarmers', 'farmersByBarangay',
            'farmersByLivelihood', 'farmersBySex',
            'totalRequests', 'pendingRequests', 'approvedRequests',
            'completedRequests', 'rejectedRequests', 'requestsByType',
            'totalStock', 'releasedStock', 'remainingStock', 'stockByCategory',
            'totalServices', 'completedServices', 'servicesByType',
            'months', 'registrationData', 'serviceData'
        ));
    }
        public function exportPDF()
{
    $data = $this->getReportData();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', $data)
        ->setPaper('a4', 'portrait');

    return $pdf->download('MAO-Guinobatan-Report-' . date('Y-m-d') . '.pdf');
}

public function exportFarmersPDF()
{
    $farmers = \App\Models\Farmer::with('barangay')->orderBy('surname')->get();
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.farmers-pdf', compact('farmers'))
        ->setPaper('a4', 'landscape');
    return $pdf->download('MAO-Farmers-List-' . date('Y-m-d') . '.pdf');
}

public function exportRequestsPDF()
{
    $requests = \App\Models\FarmerRequest::with(['farmer', 'farmer.barangay'])
        ->latest()->get();
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.requests-pdf', compact('requests'))
        ->setPaper('a4', 'landscape');
    return $pdf->download('MAO-Requests-Report-' . date('Y-m-d') . '.pdf');
}

public function exportServicesPDF()
{
    $services = \App\Models\ServiceRecord::with(['farmer', 'farmer.barangay', 'processedBy'])
        ->latest()->get();
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.services-pdf', compact('services'))
        ->setPaper('a4', 'landscape');
    return $pdf->download('MAO-Services-Report-' . date('Y-m-d') . '.pdf');
}

private function getReportData()
{
    $totalFarmers        = \App\Models\Farmer::count();
    $activeFarmers       = \App\Models\Farmer::where('status', 'active')->count();
    $totalRequests       = \App\Models\FarmerRequest::count();
    $pendingRequests     = \App\Models\FarmerRequest::where('status', 'pending')->count();
    $approvedRequests    = \App\Models\FarmerRequest::where('status', 'approved')->count();
    $completedRequests   = \App\Models\FarmerRequest::where('status', 'completed')->count();
    $rejectedRequests    = \App\Models\FarmerRequest::where('status', 'rejected')->count();
    $totalServices       = \App\Models\ServiceRecord::count();
    $completedServices   = \App\Models\ServiceRecord::where('status', 'completed')->count();
    $totalStock          = \App\Models\Stock::sum('total_stock');
    $releasedStock       = \App\Models\Stock::sum('released_stock');
    $remainingStock      = \App\Models\Stock::sum('remaining_stock');
    $farmersByBarangay   = \App\Models\Farmer::with('barangay')
        ->select('barangay_id', DB::raw('count(*) as total'))
        ->groupBy('barangay_id')
        ->orderByDesc('total')
        ->take(10)
        ->get();
    $farmersByLivelihood = \App\Models\Farmer::select('main_livelihood', DB::raw('count(*) as total'))
        ->whereNotNull('main_livelihood')
        ->groupBy('main_livelihood')
        ->get();
    $farmersBySex        = \App\Models\Farmer::select('sex', DB::raw('count(*) as total'))
        ->groupBy('sex')
        ->get();
    $servicesByType      = \App\Models\ServiceRecord::select('service_type', DB::raw('count(*) as total'))
        ->groupBy('service_type')
        ->orderByDesc('total')
        ->get();

    return compact(
        'totalFarmers', 'activeFarmers',
        'totalRequests', 'pendingRequests', 'approvedRequests',
        'completedRequests', 'rejectedRequests',
        'totalServices', 'completedServices',
        'totalStock', 'releasedStock', 'remainingStock',
        'farmersByBarangay', 'farmersByLivelihood',
        'farmersBySex', 'servicesByType'
    );
        }
    }