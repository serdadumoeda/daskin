<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Model-model yang digunakan
use App\Models\JumlahPenempatanKemnaker;
use App\Models\JumlahKepesertaanPelatihan;
use App\Models\JumlahRegulasiBaru;
use App\Models\AplikasiIntegrasiSiapkerja;
use App\Models\DataKetenagakerjaan;

class MainDashboardController extends Controller
{
    const STATUS_LULUS_PELATIHAN = 1; // Asumsi nilai 1 berarti LULUS

    public function index(Request $request)
    {
        $selectedYear = $request->input('tahun', Carbon::now()->year);

        // PERBAIKAN: Memastikan $selectedMonth adalah integer atau null, bukan string kosong.
        $selectedMonthInput = $request->input('bulan');
        $selectedMonth = !empty($selectedMonthInput) ? (int)$selectedMonthInput : null;

        $dashboardSummaryCards = $this->getDashboardSummaryCards($selectedYear, $selectedMonth);

        $chartData = [];
        // Definisikan $monthLabels di sini untuk konsistensi
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        // 1. Chart Penempatan Tenaga Kerja
        $penempatanData = $this->getPenempatanTenagaKerjaData($selectedYear, $selectedMonth);
        $chartData['penempatan'] = [
            'title' => 'Penempatan Tenaga Kerja',
            'akumulasi_total' => number_format($penempatanData['akumulasi_total']) . ' Orang',
            'tren_bulanan' => $penempatanData['tren_bulanan'],
            'tren_akumulasi_bulanan' => $penempatanData['tren_akumulasi_bulanan'],
            'icon' => 'ri-briefcase-4-line',
            'icon_bg_color' => 'bg-icon-summary-1-bg',
            'icon_text_color' => 'text-icon-summary-1-text',
            'bar_color' => '#ffab00', 
            'line_color' => '#d97706', 
        ];

        // 2. Chart Peserta Pelatihan Lulus
        $pelatihanData = $this->getPesertaPelatihanData($selectedYear, $selectedMonth);
        $chartData['pelatihan'] = [
            'title' => 'Peserta Pelatihan Lulus',
            'akumulasi_total' => number_format($pelatihanData['akumulasi_total']) . ' Peserta',
            'tren_bulanan' => $pelatihanData['tren_bulanan'],
            'tren_akumulasi_bulanan' => $pelatihanData['tren_akumulasi_bulanan'],
            'icon' => 'ri-team-line',
            'icon_bg_color' => 'bg-icon-summary-2-bg',
            'icon_text_color' => 'text-icon-summary-2-text',
            'bar_color' => '#f5365c', 
            'line_color' => '#c026d3', 
        ];
        
        // 3. Chart Penerbitan Regulasi
        $regulasiData = $this->getPenerbitanRegulasiData($selectedYear, $selectedMonth);
        $chartData['regulasi'] = [
            'title' => 'Penerbitan Regulasi Baru',
            'akumulasi_total' => number_format($regulasiData['akumulasi_total']) . ' Regulasi',
            'tren_bulanan' => $regulasiData['tren_bulanan'],
            'tren_akumulasi_bulanan' => $regulasiData['tren_akumulasi_bulanan'],
            'icon' => 'ri-file-list-3-line',
            'icon_bg_color' => 'bg-icon-summary-3-bg',
            'icon_text_color' => 'text-icon-summary-3-text',
            'bar_color' => '#1172ef', 
            'line_color' => '#2563eb', 
        ];
        
        return view('dashboards.main', compact(
            'selectedYear',
            'selectedMonth',
            'dashboardSummaryCards',
            'chartData',
            'monthLabels' // Pastikan $monthLabels selalu dikirim
        ));
    }

    private function getDashboardSummaryCards($year, $month)
    {
        $totalPenempatan = JumlahPenempatanKemnaker::query()
            ->when($year, fn ($q) => $q->where('tahun', $year))
            ->when($month, fn ($q) => $q->where('bulan', $month))
            ->sum(DB::raw('COALESCE(jumlah, 0)'));

        $totalLulusPelatihan = JumlahKepesertaanPelatihan::query()
            ->where('status_kelulusan', self::STATUS_LULUS_PELATIHAN) 
            ->when($year, fn ($q) => $q->where('tahun', $year))
            ->when($month, fn ($q) => $q->where('bulan', $month))
            ->sum(DB::raw('COALESCE(jumlah, 0)'));

        $tptModel = DataKetenagakerjaan::query()
            ->where('tahun', $year)
            ->when($month, fn ($q) => $q->where('bulan', $month))
            ->orderBy('bulan', 'desc')
            ->first();
        $tpt = $tptModel ? number_format($tptModel->tpt_persen, 2) . '%' : 'N/A';


        return [
            [
                'title' => 'Total Penempatan Kerja',
                'value' => number_format($totalPenempatan),
                'unit' => 'Orang',
                'icon' => 'ri-group-line',
                'icon_bg_color' => 'bg-icon-summary-1-bg',
                'icon_text_color' => 'text-icon-summary-1-text',
            ],
            [ 
                'title' => 'Tingkat Pengangguran Terbuka',
                'value' => $tpt,
                'unit' => '',
                'icon' => 'ri-line-chart-line',
                'icon_bg_color' => 'bg-icon-summary-4-bg', 
                'icon_text_color' => 'text-icon-summary-4-text',
            ],
            [
                'title' => 'Total Lulus Pelatihan ',
                'value' => number_format($totalLulusPelatihan),
                'unit' => 'Peserta',
                'icon' => 'ri-graduation-cap-line',
                'icon_bg_color' => 'bg-icon-summary-2-bg',
                'icon_text_color' => 'text-icon-summary-2-text',
            ],
           
        ];
    }

    private function getPenempatanTenagaKerjaData($year, $monthFilter)
    {
        $query = JumlahPenempatanKemnaker::query()->where('tahun', $year);
        
        $akumulasiTotalQuery = clone $query;
        if ($monthFilter) {
            $akumulasiTotalQuery->where('bulan', '<=', $monthFilter);
        }
        $akumulasiTotal = $akumulasiTotalQuery->sum(DB::raw('COALESCE(jumlah, 0)'));

        $trenBulananDb = JumlahPenempatanKemnaker::query()->where('tahun', $year)
                         ->selectRaw('bulan, SUM(COALESCE(jumlah, 0)) as total')
                         ->groupBy('bulan')
                         ->orderBy('bulan')
                         ->pluck('total', 'bulan')
                         ->all();
        
        $trenBulanan = $this->formatMonthlyDataForChart($trenBulananDb);
        $trenAkumulasiBulanan = $this->calculateMonthlyAccumulation($trenBulanan);
        
        return [
            'akumulasi_total' => $akumulasiTotal,
            'tren_bulanan' => $trenBulanan,
            'tren_akumulasi_bulanan' => $trenAkumulasiBulanan,
        ];
    }

    private function getPesertaPelatihanData($year, $monthFilter)
    {
        $baseQuery = JumlahKepesertaanPelatihan::query()
                    ->where('status_kelulusan', self::STATUS_LULUS_PELATIHAN)
                    ->where('tahun', $year);
        
        $akumulasiTotalQuery = clone $baseQuery;
        if ($monthFilter) {
            $akumulasiTotalQuery->where('bulan', '<=', $monthFilter);
        }
        $akumulasiTotal = $akumulasiTotalQuery->sum(DB::raw('COALESCE(jumlah, 0)'));

        $trenBulananDb = (clone $baseQuery) 
                         ->selectRaw('bulan, SUM(COALESCE(jumlah, 0)) as total')
                         ->groupBy('bulan')
                         ->orderBy('bulan')
                         ->pluck('total', 'bulan')
                         ->all();

        $trenBulanan = $this->formatMonthlyDataForChart($trenBulananDb);
        $trenAkumulasiBulanan = $this->calculateMonthlyAccumulation($trenBulanan);

        return [
            'akumulasi_total' => $akumulasiTotal,
            'tren_bulanan' => $trenBulanan,
            'tren_akumulasi_bulanan' => $trenAkumulasiBulanan,
        ];
    }
    
    private function getPenerbitanRegulasiData($year, $monthFilter)
    {
        $query = JumlahRegulasiBaru::query()->where('tahun', $year);
        
        $akumulasiTotalQuery = clone $query;
        if ($monthFilter) {
            $akumulasiTotalQuery->where('bulan', '<=', $monthFilter);
        }
        $akumulasiTotal = $akumulasiTotalQuery->sum(DB::raw('COALESCE(jumlah_regulasi, 0)'));

        $trenBulananDb = JumlahRegulasiBaru::query()->where('tahun', $year)
                         ->selectRaw('bulan, SUM(COALESCE(jumlah_regulasi, 0)) as total')
                         ->groupBy('bulan')
                         ->orderBy('bulan')
                         ->pluck('total', 'bulan')
                         ->all();

        $trenBulanan = $this->formatMonthlyDataForChart($trenBulananDb);
        $trenAkumulasiBulanan = $this->calculateMonthlyAccumulation($trenBulanan);
        
        return [
            'akumulasi_total' => $akumulasiTotal,
            'tren_bulanan' => $trenBulanan,
            'tren_akumulasi_bulanan' => $trenAkumulasiBulanan,
        ];
    }

    private function formatMonthlyDataForChart(array $dataFromDb): array
    {
        $monthlyData = array_fill(0, 12, 0); // Indeks 0-11 untuk JavaScript (Jan-Des)
        foreach ($dataFromDb as $bulanDb => $total) { // $bulanDb adalah 1-12 dari database
            if ($bulanDb >= 1 && $bulanDb <= 12) {
                $monthlyData[(int)$bulanDb - 1] = (float)$total; // Konversi ke indeks 0-11
            }
        }
        return $monthlyData;
    }

    private function calculateMonthlyAccumulation(array $monthlyData): array
    {
        $accumulated = [];
        $currentSum = 0;
        foreach ($monthlyData as $value) { // $monthlyData sudah 0-indexed
            $currentSum += $value;
            $accumulated[] = $currentSum;
        }
        return $accumulated;
    }
}