<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JumlahKajianRekomendasi;
use App\Models\AplikasiIntegrasiSiapkerja; // Pastikan model ini benar
use App\Models\DataKetenagakerjaan; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarenbangDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYearMain = $request->input('year_filter_main', $currentYear);
        $selectedMonthMain = $request->input('month_filter_main');
        $selectedYearSakernas = $request->input('year_filter_sakernas', $currentYear);

        // --- Data untuk Kartu Ringkasan Barenbang (Kajian & Aplikasi) ---
        $totalKajianRekomendasi = JumlahKajianRekomendasi::query()
            ->when($selectedYearMain, fn($q) => $q->where('tahun', $selectedYearMain))
            ->when($selectedMonthMain, fn($q) => $q->where('bulan', $selectedMonthMain))
            ->sum('jumlah'); // Sudah diperbaiki menjadi 'jumlah'

        // *** PERBAIKAN DI SINI untuk Aplikasi Terintegrasi ***
        // Jika setiap baris adalah 1 aplikasi yang terintegrasi (status_integrasi = 1)
        $totalAplikasiTerintegrasi = AplikasiIntegrasiSiapkerja::query()
            ->where('status_integrasi', 1) // Asumsi 1 = Terintegrasi
            ->when($selectedYearMain, fn($q) => $q->where('tahun', $selectedYearMain))
            ->when($selectedMonthMain, fn($q) => $q->where('bulan', $selectedMonthMain))
            ->count(); // Menggunakan count() karena setiap baris adalah satu aplikasi

        // --- Data Ketenagakerjaan (Sakernas - TPAK & TPT) ---
        // (Kode Sakernas tetap sama seperti sebelumnya)
        $latestSakernasForYear = DataKetenagakerjaan::where('tahun', $selectedYearSakernas)
                                ->whereIn('bulan', [2, 8])
                                ->orderBy('bulan', 'desc')
                                ->first();
        
        $latestTpak = $latestSakernasForYear->tingkat_partisipasi_angkatan_kerja ?? null;
        $latestTpt = $latestSakernasForYear->tingkat_pengangguran_terbuka ?? null;
        $latestSakernasPeriod = '';
        if ($latestSakernasForYear) {
            $latestSakernasPeriod = ($latestSakernasForYear->bulan == 2 ? 'Februari' : 'Agustus') . ' ' . $latestSakernasForYear->tahun;
        }

        $sakernasDataForChart = DataKetenagakerjaan::where('tahun', $selectedYearSakernas)
            ->whereIn('bulan', [2, 8])
            ->orderBy('bulan', 'asc')
            ->get();
        $tpakTptChartLabels = [];
        $tpakChartData = [];
        $tptChartData = [];
        foreach ($sakernasDataForChart as $data) {
            $label = ($data->bulan == 2 ? 'Feb' : 'Agu') . ' ' . $data->tahun;
            $tpakTptChartLabels[] = $label;
            $tpakChartData[] = $data->tingkat_partisipasi_angkatan_kerja;
            $tptChartData[] = $data->tingkat_pengangguran_terbuka;
        }
        
        $startYearForTrend = $currentYear - 2; 
        $sakernasMultiYearTrend = DataKetenagakerjaan::where('tahun', '>=', $startYearForTrend)
            ->where('tahun', '<=', $currentYear)
            ->whereIn('bulan', [2, 8])
            ->orderBy('tahun', 'asc')->orderBy('bulan', 'asc')
            ->get();
        $tpakMultiYearLabels = [];
        $tpakMultiYearValues = [];
        $tptMultiYearValues = [];
        foreach($sakernasMultiYearTrend as $data){
            $tpakMultiYearLabels[] = ($data->bulan == 2 ? 'Feb' : 'Agu') . ' ' . $data->tahun;
            $tpakMultiYearValues[] = $data->tingkat_partisipasi_angkatan_kerja;
            $tptMultiYearValues[] = $data->tingkat_pengangguran_terbuka;
        }

        // --- Chart untuk Kajian & Aplikasi ---
        $kajianPerBulan = JumlahKajianRekomendasi::select('bulan', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYearMain)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $kajianChartLabels = [];
        $kajianChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $kajianChartLabels[] = Carbon::create()->month($m)->isoFormat('MMM');
            $kajianChartDataValues[] = $kajianPerBulan->get($m, 0); 
        }

        // *** PERBAIKAN DI SINI untuk Aplikasi Terintegrasi ***
        // Jika setiap baris adalah 1 aplikasi, maka kita count per bulan
        $aplikasiPerBulan = AplikasiIntegrasiSiapkerja::select('bulan', DB::raw('COUNT(*) as total'))
            ->where('status_integrasi', 1) // Asumsi 1 = Terintegrasi
            ->where('tahun', $selectedYearMain)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $aplikasiChartLabels = [];
        $aplikasiChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $aplikasiChartLabels[] = Carbon::create()->month($m)->isoFormat('MMM');
            $aplikasiChartDataValues[] = $aplikasiPerBulan->get($m, 0); 
        }

        $availableYearsMain = JumlahKajianRekomendasi::select('tahun')->distinct()
                            ->union(AplikasiIntegrasiSiapkerja::select('tahun')->distinct())
                            ->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYearsMain->isEmpty() && !$availableYearsMain->contains($currentYear)) {
            $availableYearsMain->push($currentYear);
            $availableYearsMain = $availableYearsMain->sortDesc();
        }
        
        $availableYearsSakernas = DataKetenagakerjaan::select('tahun')->distinct()
                                    ->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYearsSakernas->isEmpty() && !$availableYearsSakernas->contains($currentYear)) {
            $availableYearsSakernas->push($currentYear);
            $availableYearsSakernas = $availableYearsSakernas->sortDesc();
        }

        return view('dashboards.barenbang', compact(
            'totalKajianRekomendasi',
            'totalAplikasiTerintegrasi',
            'latestTpak',
            'latestTpt',
            'latestSakernasPeriod',
            'tpakTptChartLabels',
            'tpakChartData',
            'tptChartData',
            'tpakMultiYearLabels',
            'tpakMultiYearValues',
            'tptMultiYearValues',
            'kajianChartLabels',
            'kajianChartDataValues',
            'aplikasiChartLabels',
            'aplikasiChartDataValues',
            'availableYearsMain',
            'availableYearsSakernas',
            'selectedYearMain',
            'selectedMonthMain',
            'selectedYearSakernas'
        ));
    }
}