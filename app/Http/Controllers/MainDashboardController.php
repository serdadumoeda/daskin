<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Model-model dari berbagai Eselon I
use App\Models\ProgressTemuanBpk;           // Itjen
use App\Models\ProgressTemuanInternal;      // Itjen
use App\Models\JumlahPenempatanKemnaker;    // Binapenta
use App\Models\JumlahKepesertaanPelatihan; // Binalavotas
use App\Models\LulusanPolteknakerBekerja;   // Sekjen (untuk Polteknaker)
use App\Models\JumlahKajianRekomendasi;     // Barenbang
use App\Models\MediasiBerhasil;             // PHI (Persentase Penyelesaian Kasus HI)
use App\Models\PelaporanWlkpOnline;         // Binwasnaker (Indikasi Kepatuhan)
use App\Models\IKPA;                        // Sekjen
// Tambahkan model lain yang relevan dengan IKU Permenaker jika ada

class MainDashboardController extends Controller
{
    // Fungsi helper untuk menghitung kumulatif jumlah
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

    // Fungsi helper untuk menghitung persentase penyelesaian kumulatif
    private function calculateCumulativePercentage(array $cumulativeTl, array $cumulativeTemuan): array
    {
        $percentage = [];
        for ($i = 0; $i < count($cumulativeTemuan); $i++) {
            if ($cumulativeTemuan[$i] > 0) {
                $percentageValue = round(($cumulativeTl[$i] / $cumulativeTemuan[$i]) * 100, 2);
                $percentage[] = min($percentageValue, 100); // Pastikan tidak lebih dari 100%
            } else {
                // Jika tidak ada temuan, dan TL juga 0, anggap 100% selesai. Jika ada TL tapi tidak ada temuan, ini anomali, anggap 0.
                $percentage[] = ($cumulativeTl[$i] == 0) ? 100 : 0; 
            }
        }
        return $percentage;
    }
    
    // Fungsi helper generik untuk mengambil data bulanan (SUM, COUNT, AVG)
    private function getMonthlyTrendData($modelOrQueryBuilder, string $monthColumn, string $valueColumn, string $aggregationType = 'SUM', array $extraConditions = [])
    {
        // Jika $modelOrQueryBuilder adalah string nama model, buat query builder
        if (is_string($modelOrQueryBuilder)) {
            $queryBuilder = app($modelOrQueryBuilder)::query();
        } else {
            $queryBuilder = $modelOrQueryBuilder; // Sudah merupakan query builder
        }

        foreach($extraConditions as $column => $value) {
            if (is_array($value)) { // Untuk kondisi IN atau sejenisnya
                $queryBuilder->whereIn($column, $value);
            } else {
                $queryBuilder->where($column, $value);
            }
        }
        
        $selectExpr = '';
        if ($aggregationType === 'COUNT') {
            // Jika valueColumn adalah '*', count all. Jika spesifik, count distinct.
            $targetCol = ($valueColumn === '*' || strtolower($valueColumn) === 'id') ? '*' : "DISTINCT {$valueColumn}";
            $selectExpr = DB::raw("COUNT({$targetCol}) as total_value");
        } elseif ($aggregationType === 'AVG') {
            $selectExpr = DB::raw("AVG({$valueColumn}) as total_value");
        } else { // Default SUM
            $selectExpr = DB::raw("SUM({$valueColumn}) as total_value");
        }

        $monthlyDataGrouped = $queryBuilder
            ->select($monthColumn, $selectExpr)
            ->groupBy($monthColumn)
            ->orderBy($monthColumn, 'asc')
            ->get()
            ->pluck('total_value', $monthColumn);
        
        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $result[] = ($aggregationType === 'AVG' ? (float)($monthlyDataGrouped->get($m) ?? 0) : (int)($monthlyDataGrouped->get($m) ?? 0) );
        }
        return $result;
    }


    public function index(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $selectedYear = $request->input('tahun', $currentYear);
        $selectedMonthInput = $request->input('bulan');
        $selectedMonth = !empty($selectedMonthInput) ? (int)$selectedMonthInput : null;

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartLabels = $selectedMonth ? [$months[$selectedMonth - 1]] : $months;

        // --- Data untuk Kartu Ringkasan Utama Kementerian (IKU dari Permenaker) ---
        // 1. Tingkat Penyelesaian Tindak Lanjut Hasil Pemeriksaan BPK (Itjen)
        $summaryBpk = ProgressTemuanBpk::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->selectRaw('SUM(temuan_administratif_kasus) as total_temuan, SUM(tindak_lanjut_administratif_kasus) as total_tl')
            ->first();
        $persenSelesaiBpk = ($summaryBpk && $summaryBpk->total_temuan > 0) ? round(($summaryBpk->total_tl / $summaryBpk->total_temuan) * 100, 2) : 0;
        if($summaryBpk && $summaryBpk->total_temuan == 0 && $summaryBpk->total_tl == 0) $persenSelesaiBpk = 100;


        // 2. Tingkat Penyelesaian Tindak Lanjut Hasil Pemeriksaan Internal (Itjen)
        $summaryInternal = ProgressTemuanInternal::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->selectRaw('SUM(temuan_administratif_kasus) as total_temuan, SUM(tindak_lanjut_administratif_kasus) as total_tl')
            ->first();
        $persenSelesaiInternal = ($summaryInternal && $summaryInternal->total_temuan > 0) ? round(($summaryInternal->total_tl / $summaryInternal->total_temuan) * 100, 2) : 0;
        if($summaryInternal && $summaryInternal->total_temuan == 0 && $summaryInternal->total_tl == 0) $persenSelesaiInternal = 100;


        // 3. Jumlah Penempatan Tenaga Kerja Dalam Negeri (Binapenta)
        $totalPenempatanKemenaker = JumlahPenempatanKemnaker::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');

        // 4. Jumlah Peserta Pelatihan Berbasis Kompetensi (Binalavotas)
        $totalPesertaPelatihan = JumlahKepesertaanPelatihan::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');
        
        // 5. Jumlah Lulusan Pelatihan yang Bekerja/Wirausaha (Sekjen - Polteknaker)
        $totalLulusanBekerja = LulusanPolteknakerBekerja::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_lulusan_bekerja');
        
        // 6. Jumlah Rekomendasi Kebijakan (Barenbang - Jenis Output Rekomendasi)
        $totalRekomendasiKebijakan = JumlahKajianRekomendasi::query()
            ->where('jenis_output', 2) // 2 untuk Rekomendasi
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');
            
        // 7. Rata-rata IKPA (Sekjen)
        $avgIkpaKementerian = IKPA::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->avg('nilai_akhir');


        // --- Data untuk Chart Tren Utama Kementerian ---
        $chartData = [];

        // Chart 1: Tren Penyelesaian Temuan BPK (Persentase Kumulatif)
        $queryBpkChart = ProgressTemuanBpk::query()->where('tahun', $selectedYear);
        $bpkTemuanBulanan = $this->getMonthlyTrendData(clone $queryBpkChart, 'bulan', 'temuan_administratif_kasus');
        $bpkTlBulanan = $this->getMonthlyTrendData(clone $queryBpkChart, 'bulan', 'tindak_lanjut_administratif_kasus');
        $kumulatifTemuanBpk = $this->calculateCumulative($bpkTemuanBulanan);
        $kumulatifTlBpk = $this->calculateCumulative($bpkTlBulanan);
        $chartData['penyelesaian_bpk'] = [
            'labels' => $chartLabels,
            'bulanan_temuan' => $bpkTemuanBulanan, // Untuk konteks jika diperlukan
            'bulanan_tl' => $bpkTlBulanan,         // Untuk konteks jika diperlukan
            'persentase_kumulatif' => $this->calculateCumulativePercentage($kumulatifTlBpk, $kumulatifTemuanBpk)
        ];

        // Chart 2: Tren Penyelesaian Temuan Internal (Persentase Kumulatif)
        $queryInternalChart = ProgressTemuanInternal::query()->where('tahun', $selectedYear);
        $internalTemuanBulanan = $this->getMonthlyTrendData(clone $queryInternalChart, 'bulan', 'temuan_administratif_kasus');
        $internalTlBulanan = $this->getMonthlyTrendData(clone $queryInternalChart, 'bulan', 'tindak_lanjut_administratif_kasus');
        $kumulatifTemuanInternal = $this->calculateCumulative($internalTemuanBulanan);
        $kumulatifTlInternal = $this->calculateCumulative($internalTlBulanan);
        $chartData['penyelesaian_internal'] = [
            'labels' => $chartLabels,
            'bulanan_temuan' => $internalTemuanBulanan,
            'bulanan_tl' => $internalTlBulanan,
            'persentase_kumulatif' => $this->calculateCumulativePercentage($kumulatifTlInternal, $kumulatifTemuanInternal)
        ];
        
        // Chart 3: Tren Penempatan Tenaga Kerja (Binapenta)
        $queryPenempatan = JumlahPenempatanKemnaker::query()->where('tahun', $selectedYear);
        $penempatanBulanan = $this->getMonthlyTrendData(clone $queryPenempatan, 'bulan', 'jumlah');
        $chartData['penempatan_kemnaker'] = [
            'labels' => $chartLabels,
            'bulanan' => $penempatanBulanan,
            'kumulatif' => $this->calculateCumulative($penempatanBulanan)
        ];

        // Chart 4: Tren Peserta Pelatihan (Binalavotas)
        $queryPesertaPelatihan = JumlahKepesertaanPelatihan::query()->where('tahun', $selectedYear);
        $pesertaPelatihanBulanan = $this->getMonthlyTrendData(clone $queryPesertaPelatihan, 'bulan', 'jumlah');
        $chartData['peserta_pelatihan'] = [
            'labels' => $chartLabels,
            'bulanan' => $pesertaPelatihanBulanan,
            'kumulatif' => $this->calculateCumulative($pesertaPelatihanBulanan)
        ];
        
        // Chart 5: Tren Lulusan Polteknaker Bekerja (Sekjen)
        $queryLulusanBekerja = LulusanPolteknakerBekerja::query()->where('tahun', $selectedYear);
        $lulusanBekerjaBulanan = $this->getMonthlyTrendData(clone $queryLulusanBekerja, 'bulan', 'jumlah_lulusan_bekerja');
        $chartData['lulusan_bekerja'] = [
            'labels' => $chartLabels,
            'bulanan' => $lulusanBekerjaBulanan,
            'kumulatif' => $this->calculateCumulative($lulusanBekerjaBulanan)
        ];

        // Chart 6: Tren Rekomendasi Kebijakan (Barenbang)
        $queryRekomendasi = JumlahKajianRekomendasi::query()->where('tahun', $selectedYear)->where('jenis_output', 2);
        $rekomendasiBulanan = $this->getMonthlyTrendData(clone $queryRekomendasi, 'bulan', 'jumlah');
        $chartData['rekomendasi_kebijakan'] = [
            'labels' => $chartLabels,
            'bulanan' => $rekomendasiBulanan,
            'kumulatif' => $this->calculateCumulative($rekomendasiBulanan)
        ];
        
        // Chart 7: Tren Rata-rata IKPA (Sekjen)
        $queryIkpa = Ikpa::query()->where('tahun', $selectedYear);
        // Untuk IKPA, kita tampilkan nilai rata-rata bulanan, dan kumulatifnya adalah rata-rata dari rata-rata bulanan (kurang ideal, tapi untuk tren)
        // Atau bisa juga kumulatif jumlah nilai IKPA / jumlah bulan (perlu penyesuaian)
        $ikpaBulanan = $this->getMonthlyTrendData(clone $queryIkpa, 'bulan', 'nilai_akhir', 'AVG');
        $chartData['ikpa'] = [
            'labels' => $chartLabels,
            'bulanan' => $ikpaBulanan, // Ini adalah rata-rata bulanan
            'kumulatif' => $this->calculateCumulative($ikpaBulanan) // Ini adalah kumulatif dari rata-rata bulanan, interpretasinya perlu hati-hati
        ];


        // Ambil tahun yang tersedia untuk filter
        $distinctYearsQueries = [
            ProgressTemuanBpk::select('tahun')->distinct(), ProgressTemuanInternal::select('tahun')->distinct(),
            JumlahPenempatanKemnaker::select('tahun')->distinct(), JumlahKepesertaanPelatihan::select('tahun')->distinct(),
            LulusanPolteknakerBekerja::select('tahun')->distinct(), JumlahKajianRekomendasi::select('tahun')->distinct(),
            Ikpa::select('tahun')->distinct(),
        ];
        
        $availableYearsQuery = array_shift($distinctYearsQueries); 
        foreach ($distinctYearsQueries as $query) {
            if($query) $availableYearsQuery->union($query); // Pastikan query tidak null
        }
        $availableYears = $availableYearsQuery->orderBy('tahun', 'desc')->pluck('tahun');

        if ($availableYears->isEmpty() && !$availableYears->contains($currentYear)) {
            $availableYears->push($currentYear);
            $availableYears = $availableYears->sortDesc()->values();
        }
        
        $viewData = compact(
            'persenSelesaiBpk', 'persenSelesaiInternal', 'totalPenempatanKemenaker',
            'totalPesertaPelatihan', 'totalLulusanBekerja', 'totalRekomendasiKebijakan', 'avgIkpaKementerian',
            'availableYears', 'selectedYear', 'selectedMonth'
        );
        
        return view('dashboards.main', array_merge($viewData, ['chartData' => $chartData]));
    }
}