<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgressTemuanBpk; 
use App\Models\ProgressTemuanInternal; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItjenDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->input('year_filter', $currentYear);
        $selectedMonth = $request->input('month_filter'); 

        // --- Data untuk Kartu Ringkasan ---
        $queryBpk = ProgressTemuanBpk::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));

        $summaryBpk = (clone $queryBpk)->select(
                DB::raw('COALESCE(SUM(temuan_administratif_kasus),0) as total_temuan_admin_kasus'),
                DB::raw('COALESCE(SUM(temuan_kerugian_negara_rp),0) as total_temuan_kerugian_rp'),
                DB::raw('COALESCE(SUM(tindak_lanjut_administratif_kasus),0) as total_tl_admin_kasus'),
                DB::raw('COALESCE(SUM(tindak_lanjut_kerugian_negara_rp),0) as total_tl_kerugian_rp')
            )->first();

        $persentaseSelesaiBpkAdmin = 0;
        if ($summaryBpk && $summaryBpk->total_temuan_admin_kasus > 0) {
            $persentaseSelesaiBpkAdmin = round(($summaryBpk->total_tl_admin_kasus / $summaryBpk->total_temuan_admin_kasus) * 100, 2);
        }
        $persentaseSelesaiBpkKerugian = 0;
        if ($summaryBpk && $summaryBpk->total_temuan_kerugian_rp > 0) {
            $persentaseSelesaiBpkKerugian = round(($summaryBpk->total_tl_kerugian_rp / $summaryBpk->total_temuan_kerugian_rp) * 100, 2);
        }

        $queryInternal = ProgressTemuanInternal::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));

        $summaryInternal = (clone $queryInternal)->select(
                DB::raw('COALESCE(SUM(temuan_administratif_kasus),0) as total_temuan_admin_kasus'),
                DB::raw('COALESCE(SUM(temuan_kerugian_negara_rp),0) as total_temuan_kerugian_rp'),
                DB::raw('COALESCE(SUM(tindak_lanjut_administratif_kasus),0) as total_tl_admin_kasus'),
                DB::raw('COALESCE(SUM(tindak_lanjut_kerugian_negara_rp),0) as total_tl_kerugian_rp')
            )->first();
        
        $persentaseSelesaiInternalAdmin = 0;
        if ($summaryInternal && $summaryInternal->total_temuan_admin_kasus > 0) {
            $persentaseSelesaiInternalAdmin = round(($summaryInternal->total_tl_admin_kasus / $summaryInternal->total_temuan_admin_kasus) * 100, 2);
        }
        $persentaseSelesaiInternalKerugian = 0;
        if ($summaryInternal && $summaryInternal->total_temuan_kerugian_rp > 0) {
            $persentaseSelesaiInternalKerugian = round(($summaryInternal->total_tl_kerugian_rp / $summaryInternal->total_temuan_kerugian_rp) * 100, 2);
        }

        // --- Data untuk Chart Tren Temuan BPK (Kasus Administratif & Kerugian Negara per Bulan) ---
        $trendTemuanBpk = ProgressTemuanBpk::select(
                'bulan',
                DB::raw('COALESCE(SUM(temuan_administratif_kasus),0) as temuan_admin'),
                DB::raw('COALESCE(SUM(tindak_lanjut_administratif_kasus),0) as tl_admin'),
                DB::raw('COALESCE(SUM(temuan_kerugian_negara_rp),0) as temuan_kerugian'),
                DB::raw('COALESCE(SUM(tindak_lanjut_kerugian_negara_rp),0) as tl_kerugian')
            )
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $bpkChartLabels = [];
        $bpkChartData = ['temuan_admin' => [], 'tl_admin' => [], 'temuan_kerugian' => [], 'tl_kerugian' => []];
        for ($m=1; $m <= 12 ; $m++) { 
            $bpkChartLabels[] = Carbon::create()->month($m)->format('M');
            $monthData = $trendTemuanBpk->firstWhere('bulan', $m);
            $bpkChartData['temuan_admin'][] = $monthData->temuan_admin ?? 0;
            $bpkChartData['tl_admin'][] = $monthData->tl_admin ?? 0;
            $bpkChartData['temuan_kerugian'][] = $monthData->temuan_kerugian ?? 0;
            $bpkChartData['tl_kerugian'][] = $monthData->tl_kerugian ?? 0;
        }


        // --- Data untuk Chart Tren Temuan Internal (Kasus Administratif & Kerugian Negara per Bulan) ---
        $trendTemuanInternal = ProgressTemuanInternal::select(
                'bulan',
                DB::raw('COALESCE(SUM(temuan_administratif_kasus),0) as temuan_admin'),
                DB::raw('COALESCE(SUM(tindak_lanjut_administratif_kasus),0) as tl_admin'),
                DB::raw('COALESCE(SUM(temuan_kerugian_negara_rp),0) as temuan_kerugian'),
                DB::raw('COALESCE(SUM(tindak_lanjut_kerugian_negara_rp),0) as tl_kerugian')
            )
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
        
        $internalChartLabels = [];
        $internalChartData = ['temuan_admin' => [], 'tl_admin' => [], 'temuan_kerugian' => [], 'tl_kerugian' => []];
        for ($m=1; $m <= 12 ; $m++) { 
            $internalChartLabels[] = Carbon::create()->month($m)->format('M');
            $monthData = $trendTemuanInternal->firstWhere('bulan', $m);
            $internalChartData['temuan_admin'][] = $monthData->temuan_admin ?? 0;
            $internalChartData['tl_admin'][] = $monthData->tl_admin ?? 0;
            $internalChartData['temuan_kerugian'][] = $monthData->temuan_kerugian ?? 0;
            $internalChartData['tl_kerugian'][] = $monthData->tl_kerugian ?? 0;
        }
        
        $availableYears = ProgressTemuanBpk::select('tahun')->distinct()
                            ->union(ProgressTemuanInternal::select('tahun')->distinct())
                            ->orderBy('tahun', 'desc')->pluck('tahun');


        return view('dashboards.itjen', compact(
            'summaryBpk', 
            'persentaseSelesaiBpkAdmin',
            'persentaseSelesaiBpkKerugian',
            'summaryInternal',
            'persentaseSelesaiInternalAdmin',
            'persentaseSelesaiInternalKerugian',
            'bpkChartLabels',
            'bpkChartData',
            'internalChartLabels',
            'internalChartData',
            'availableYears',
            'selectedYear',
            'selectedMonth'
        ));
    }
}
