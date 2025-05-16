<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PelaporanWlkpOnline;
use App\Models\PengaduanPelanggaranNorma;
use App\Models\PenerapanSmk3;
use App\Models\SelfAssessmentNorma100;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BinwasnakerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->input('year_filter', $currentYear);
        $selectedMonth = $request->input('month_filter');

        // --- Data untuk Kartu Ringkasan ---
        $totalWlkpReported = PelaporanWlkpOnline::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_perusahaan_melapor');

        $totalPengaduanNorma = PengaduanPelanggaranNorma::query()
            ->when($selectedYear, fn($q) => $q->where('tahun_pengaduan', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan_pengaduan', $selectedMonth))
            ->sum('jumlah_kasus');
        
        $totalPenerapanSmk3 = PenerapanSmk3::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_perusahaan');

        $totalSelfAssessment = SelfAssessmentNorma100::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_perusahaan');

        // --- Data untuk Chart ---
        // 1. Tren Pelaporan WLKP Online per Bulan
        $wlkpPerBulan = PelaporanWlkpOnline::select('bulan', DB::raw('SUM(jumlah_perusahaan_melapor) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
            
        $wlkpChartLabels = [];
        $wlkpChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $wlkpChartLabels[] = Carbon::create()->month($m)->format('M');
            $wlkpChartDataValues[] = $wlkpPerBulan->get($m, 0); 
        }

        // 2. Komposisi Pengaduan Pelanggaran Norma berdasarkan Jenis Pelanggaran (Top 5 + Lainnya)
        $pengaduanPerJenis = PengaduanPelanggaranNorma::select('jenis_pelanggaran', DB::raw('SUM(jumlah_kasus) as total'))
            ->where('tahun_pengaduan', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan_pengaduan', $selectedMonth))
            ->groupBy('jenis_pelanggaran')
            ->orderBy('total', 'desc')
            ->get();
        
        $pengaduanChartData = [];
        $othersCount = 0;
        $limit = 4; 
        foreach($pengaduanPerJenis as $index => $item){
            if($index < $limit){
                $pengaduanChartData[] = ['name' => $item->jenis_pelanggaran, 'value' => $item->total];
            } else {
                $othersCount += $item->total;
            }
        }
        if($othersCount > 0){
            $pengaduanChartData[] = ['name' => 'Lainnya', 'value' => $othersCount];
        }
        
        // 3. Grafik Tren Perusahaan yang menerapkan SMK3 Tahun ... (BARU)
        $smk3PerBulan = PenerapanSmk3::select('bulan', DB::raw('SUM(jumlah_perusahaan) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $smk3TrendChartLabels = [];
        $smk3TrendChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $smk3TrendChartLabels[] = Carbon::create()->month($m)->format('M');
            $smk3TrendChartDataValues[] = $smk3PerBulan->get($m, 0); 
        }

        // 4. Grafik Tren Perusahaan yang melakukan self-assessment norma 100 Tahun ... (BARU)
        $saPerBulan = SelfAssessmentNorma100::select('bulan', DB::raw('SUM(jumlah_perusahaan) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $saTrendChartLabels = [];
        $saTrendChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $saTrendChartLabels[] = Carbon::create()->month($m)->format('M');
            $saTrendChartDataValues[] = $saPerBulan->get($m, 0); 
        }

        // Data untuk chart tahunan WLKP (jika ingin tetap ada)
        $wlkpAnnualTrend = PelaporanWlkpOnline::select('tahun', DB::raw('SUM(jumlah_perusahaan_melapor) as total_tahunan'))
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();
        $wlkpAnnualTrendLabels = $wlkpAnnualTrend->pluck('tahun')->toArray();
        $wlkpAnnualTrendDataValues = $wlkpAnnualTrend->pluck('total_tahunan')->toArray();


        $availableYears = PelaporanWlkpOnline::select('tahun')->distinct()
                            ->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYears->isEmpty() && !$availableYears->contains($currentYear)) {
            $availableYears->push($currentYear);
            $availableYears = $availableYears->sortDesc();
        }
        
        return view('dashboards.binwasnaker', compact(
            'totalWlkpReported',
            'totalPengaduanNorma',
            'totalPenerapanSmk3',
            'totalSelfAssessment',
            'wlkpChartLabels',
            'wlkpChartDataValues',
            'pengaduanChartData',
            'smk3TrendChartLabels', // Data baru untuk tren SMK3
            'smk3TrendChartDataValues', // Data baru untuk tren SMK3
            'saTrendChartLabels', // Data baru untuk tren Self Assessment
            'saTrendChartDataValues', // Data baru untuk tren Self Assessment
            'wlkpAnnualTrendLabels', // Untuk contoh tren tahunan WLKP
            'wlkpAnnualTrendDataValues', // Untuk contoh tren tahunan WLKP
            'availableYears',
            'selectedYear',
            'selectedMonth'
        ));
    }
}