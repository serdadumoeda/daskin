<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Pastikan namespace dan nama model sudah benar
use App\Models\JumlahPenempatanKemnaker;
use App\Models\JumlahLowonganPasker;
use App\Models\JumlahTkaDisetujui; // Mengganti PersetujuanRptka dengan JumlahTkaDisetujui jika ini model yang benar
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BinapentaDashboardController extends Controller
{
    private function calculateCumulative(array $data): array
    {
        $cumulative = [];
        $sum = 0;
        foreach ($data as $value) {
            $sum += is_numeric($value) ? $value : 0;
            $cumulative[] = $sum;
        }
        return $cumulative;
    }

    public function index(Request $request)
    {
        $currentYear = date('Y');
        // Menggunakan 'tahun' dan 'bulan' sesuai standar filter kita
        $selectedYear = $request->input('tahun', $currentYear);
        $selectedMonth = $request->input('bulan'); // Bisa null

        // --- Data untuk Kartu Ringkasan ---
        $totalPenempatanKemnaker = JumlahPenempatanKemnaker::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');

        $totalLowonganPasker = JumlahLowonganPasker::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_lowongan');
        
        // Menggunakan model JumlahTkaDisetujui untuk RPTKA
        $totalTkaDisetujui = JumlahTkaDisetujui::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_tka');


        // --- Logika Chart ---
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $chartLabels = []; 
        // Inisialisasi array data untuk chart
        $penempatanBulanan = []; $penempatanKumulatif = [];
        $lowonganPaskerBulanan = []; $lowonganPaskerKumulatif = [];
        $tkaDisetujuiBulanan = []; $tkaDisetujuiKumulatif = [];

        if ($selectedMonth) {
            // === JIKA BULAN DIPILIH ===
            $chartLabels = [$months[(int)$selectedMonth - 1]];

            $penempatanBulanan = [(int)JumlahPenempatanKemnaker::query()->where('tahun', $selectedYear)->where('bulan', $selectedMonth)->sum('jumlah')];
            $penempatanKumulatif = $penempatanBulanan;

            $lowonganPaskerBulanan = [(int)JumlahLowonganPasker::query()->where('tahun', $selectedYear)->where('bulan', $selectedMonth)->sum('jumlah_lowongan')];
            $lowonganPaskerKumulatif = $lowonganPaskerBulanan;

            $tkaDisetujuiBulanan = [(int)JumlahTkaDisetujui::query()->where('tahun', $selectedYear)->where('bulan', $selectedMonth)->sum('jumlah_tka')];
            $tkaDisetujuiKumulatif = $tkaDisetujuiBulanan;

        } else {
            // === JIKA SEMUA BULAN (TAHUNAN) ===
            $chartLabels = $months;

            // Fungsi helper untuk mengambil data bulanan tahunan
            $getAnnualMonthlyData = function ($model, $yearColumnName, $monthColumnName, $valueColumnName) use ($selectedYear) {
                $data = $model::query()->where($yearColumnName, $selectedYear)
                    ->select($monthColumnName, DB::raw("SUM({$valueColumnName}) as total_value"))
                    ->groupBy($monthColumnName)
                    ->orderBy($monthColumnName, 'asc')
                    ->get()
                    ->pluck('total_value', $monthColumnName);
                
                $monthlyValues = [];
                for ($m = 1; $m <= 12; $m++) {
                    $monthlyValues[] = (int)($data->get($m) ?? 0);
                }
                return $monthlyValues;
            };

            // Data Chart Penempatan Kemnaker
            $penempatanBulanan = $getAnnualMonthlyData(new JumlahPenempatanKemnaker, 'tahun', 'bulan', 'jumlah');
            $penempatanKumulatif = $this->calculateCumulative($penempatanBulanan);

            // Data Chart Lowongan Pasker
            $lowonganPaskerBulanan = $getAnnualMonthlyData(new JumlahLowonganPasker, 'tahun', 'bulan', 'jumlah_lowongan');
            $lowonganPaskerKumulatif = $this->calculateCumulative($lowonganPaskerBulanan);

            // Data Chart TKA Disetujui (RPTKA)
            $tkaDisetujuiBulanan = $getAnnualMonthlyData(new JumlahTkaDisetujui, 'tahun', 'bulan', 'jumlah_tka');
            $tkaDisetujuiKumulatif = $this->calculateCumulative($tkaDisetujuiBulanan);
        }

        // Ambil tahun yang tersedia untuk filter
        $yearsPenempatan = JumlahPenempatanKemnaker::select('tahun')->distinct();
        $yearsLowongan = JumlahLowonganPasker::select('tahun')->distinct();
        $yearsTka = JumlahTkaDisetujui::select('tahun')->distinct();
        
        $availableYears = $yearsPenempatan
            ->union($yearsLowongan)
            ->union($yearsTka)
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($availableYears->isEmpty() && !$availableYears->contains($currentYear)) {
            $availableYears->push($currentYear);
            $availableYears = $availableYears->sortDesc()->values();
        }
        
        $viewData = compact(
            'totalPenempatanKemnaker', 'totalLowonganPasker', 'totalTkaDisetujui',
            'availableYears', 'selectedYear', 'selectedMonth'
        );

        $chartData = [
            'penempatan' => ['labels' => $chartLabels, 'bulanan' => $penempatanBulanan, 'kumulatif' => $penempatanKumulatif],
            'lowongan_pasker' => ['labels' => $chartLabels, 'bulanan' => $lowonganPaskerBulanan, 'kumulatif' => $lowonganPaskerKumulatif],
            'tka_disetujui' => ['labels' => $chartLabels, 'bulanan' => $tkaDisetujuiBulanan, 'kumulatif' => $tkaDisetujuiKumulatif],
        ];
        
        // Komposisi Pie Chart (Contoh, Anda bisa kembangkan)
        // Penempatan berdasarkan Jenis Kelamin
        $penempatanPerJenisKelamin = JumlahPenempatanKemnaker::select('jenis_kelamin', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('jenis_kelamin')
            ->get()
            ->map(function ($item) {
                // Anda mungkin perlu accessor di model untuk teks jenis kelamin
                $jkText = $item->jenis_kelamin == 1 ? 'Laki-laki' : ($item->jenis_kelamin == 2 ? 'Perempuan' : 'N/A');
                return ['name' => $jkText, 'value' => (int)$item->total];
            })->toArray();

        $pieChartData = [
            'penempatan_jk' => $penempatanPerJenisKelamin,
        ];

        return view('dashboards.binapenta', array_merge($viewData, ['chartData' => $chartData], ['pieChartData' => $pieChartData]));
    }
}