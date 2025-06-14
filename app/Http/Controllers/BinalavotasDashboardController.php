<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Pastikan namespace dan nama model sudah benar sesuai dengan struktur folder Anda
// Misalnya, jika model ada di app\Models\Binalavotas\JumlahKepesertaanPelatihan
// maka gunakan App\Models\Binalavotas\JumlahKepesertaanPelatihan;
use App\Models\JumlahKepesertaanPelatihan;
use App\Models\JumlahSertifikasiKompetensi;
use Illuminate\Support\Facades\DB;

class BinalavotasDashboardController extends Controller
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
        $totalPesertaPelatihan = JumlahKepesertaanPelatihan::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');

        $totalLulusPelatihan = JumlahKepesertaanPelatihan::query()
            ->where('status_kelulusan', 1) // Asumsi 1 = Lulus
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');

        $totalSertifikasi = JumlahSertifikasiKompetensi::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_sertifikasi');

        // --- Logika Chart ---
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $chartLabels = []; 
        // Inisialisasi array data untuk chart
        $pesertaPelatihanBulanan = []; $pesertaPelatihanKumulatif = [];
        $lulusPelatihanBulanan = []; $lulusPelatihanKumulatif = [];
        $sertifikasiBulanan = []; $sertifikasiKumulatif = [];

        if ($selectedMonth) {
            // === JIKA BULAN DIPILIH ===
            $chartLabels = [$months[(int)$selectedMonth - 1]];

            // Data Peserta Pelatihan (Total)
            $jumlahPeserta = (int)JumlahKepesertaanPelatihan::query()
                ->where('tahun', $selectedYear)->where('bulan', $selectedMonth)
                ->sum('jumlah');
            $pesertaPelatihanBulanan = [$jumlahPeserta];
            $pesertaPelatihanKumulatif = [$jumlahPeserta];

            // Data Peserta Lulus Pelatihan
            $jumlahLulus = (int)JumlahKepesertaanPelatihan::query()
                ->where('status_kelulusan', 1)
                ->where('tahun', $selectedYear)->where('bulan', $selectedMonth)
                ->sum('jumlah');
            $lulusPelatihanBulanan = [$jumlahLulus];
            $lulusPelatihanKumulatif = [$jumlahLulus];

            // Data Sertifikasi Kompetensi
            $jumlahSertifikasi = (int)JumlahSertifikasiKompetensi::query()
                ->where('tahun', $selectedYear)->where('bulan', $selectedMonth)
                ->sum('jumlah_sertifikasi');
            $sertifikasiBulanan = [$jumlahSertifikasi];
            $sertifikasiKumulatif = [$jumlahSertifikasi];

        } else {
            // === JIKA SEMUA BULAN (TAHUNAN) ===
            $chartLabels = $months;

            // Fungsi helper untuk mengambil data bulanan tahunan
            $getAnnualMonthlyData = function ($model, $yearColumnName, $monthColumnName, $valueColumnName, $filter = null) use ($selectedYear) {
                $query = $model::query()->where($yearColumnName, $selectedYear);
                
                if (is_array($filter)) {
                    foreach ($filter as $col => $val) {
                        $query->where($col, $val);
                    }
                }

                $data = $query->select($monthColumnName, DB::raw("SUM({$valueColumnName}) as total_value"))
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

            // Data Chart Peserta Pelatihan (Total)
            $pesertaPelatihanBulanan = $getAnnualMonthlyData(new JumlahKepesertaanPelatihan, 'tahun', 'bulan', 'jumlah');
            $pesertaPelatihanKumulatif = $this->calculateCumulative($pesertaPelatihanBulanan);

            // Data Chart Peserta Lulus Pelatihan
            $lulusPelatihanBulanan = $getAnnualMonthlyData(new JumlahKepesertaanPelatihan, 'tahun', 'bulan', 'jumlah', ['status_kelulusan' => 1]);
            $lulusPelatihanKumulatif = $this->calculateCumulative($lulusPelatihanBulanan);

            // Data Chart Sertifikasi Kompetensi
            $sertifikasiBulanan = $getAnnualMonthlyData(new JumlahSertifikasiKompetensi, 'tahun', 'bulan', 'jumlah_sertifikasi');
            $sertifikasiKumulatif = $this->calculateCumulative($sertifikasiBulanan);
        }

        // Ambil tahun yang tersedia untuk filter
        $yearsPelatihan = JumlahKepesertaanPelatihan::select('tahun')->distinct();
        $yearsSertifikasi = JumlahSertifikasiKompetensi::select('tahun')->distinct();
        
        $availableYears = $yearsPelatihan
            ->union($yearsSertifikasi) // Menggabungkan tahun dari kedua tabel
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($availableYears->isEmpty() && !$availableYears->contains($currentYear)) {
            $availableYears->push($currentYear);
            $availableYears = $availableYears->sortDesc()->values(); // Pastikan diurutkan lagi setelah push
        }
        
        $viewData = compact(
            'totalPesertaPelatihan', 'totalLulusPelatihan', 'totalSertifikasi',
            'availableYears', 'selectedYear', 'selectedMonth'
            // 'chartLabels' tidak perlu dikirim terpisah jika setiap chart_data sudah punya labels
        );

        $chartData = [
            // Setiap chart sekarang membawa labelnya sendiri untuk fleksibilitas jika ada filter bulan
            'peserta_pelatihan' => ['labels' => $chartLabels, 'bulanan' => $pesertaPelatihanBulanan, 'kumulatif' => $pesertaPelatihanKumulatif],
            'lulus_pelatihan' => ['labels' => $chartLabels, 'bulanan' => $lulusPelatihanBulanan, 'kumulatif' => $lulusPelatihanKumulatif],
            'sertifikasi' => ['labels' => $chartLabels, 'bulanan' => $sertifikasiBulanan, 'kumulatif' => $sertifikasiKumulatif],
        ];
        
        // Data Pie Chart (Komposisi) - Opsional, contoh
        // 1. Komposisi Peserta Pelatihan berdasarkan Penyelenggara
        $pesertaPerPenyelenggara = JumlahKepesertaanPelatihan::select('penyelenggara_pelatihan', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('penyelenggara_pelatihan')
            ->get()
            ->map(function ($item) {
                // Asumsi ada accessor di model atau mapping manual
                $penyelenggaraText = $item->penyelenggara_pelatihan == 1 ? 'Internal' : ($item->penyelenggara_pelatihan == 2 ? 'Eksternal' : 'Lainnya');
                return ['name' => $penyelenggaraText, 'value' => (int)$item->total];
            })->toArray();

        // 2. Komposisi Sertifikasi berdasarkan Jenis LSP
        $sertifikasiPerJenisLsp = JumlahSertifikasiKompetensi::select('jenis_lsp', DB::raw('SUM(jumlah_sertifikasi) as total'))
            ->where('tahun', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('jenis_lsp')
            ->get()
            ->map(function ($item) {
                 // Asumsi ada accessor di model atau mapping manual
                $lspText = 'LSP P'.$item->jenis_lsp; // Contoh: LSP P1, LSP P2, LSP P3
                return ['name' => $lspText, 'value' => (int)$item->total];
            })->toArray();
        
        $pieChartData = [
            'peserta_penyelenggara' => $pesertaPerPenyelenggara,
            'sertifikasi_jenis_lsp' => $sertifikasiPerJenisLsp,
        ];

        return view('dashboards.binalavotas', array_merge($viewData, ['chartData' => $chartData], ['pieChartData' => $pieChartData]));
    }
}