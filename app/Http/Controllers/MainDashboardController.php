<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\helperController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Model-model dari berbagai Eselon I
use App\Models\ProgressTemuanBpk;           // Itjen
use App\Models\ProgressTemuanInternal;      // Itjen
use App\Models\JumlahPenempatanKemnaker;    // Binapenta
use App\Models\JumlahKepesertaanPelatihan;  // Binalavotas
use App\Models\LulusanPolteknakerBekerja;   // Sekjen (untuk Polteknaker)
use App\Models\JumlahKajianRekomendasi;     // Barenbang
use App\Models\MediasiBerhasil;             // PHI (Persentase Penyelesaian Kasus HI)
use App\Models\PelaporanWlkpOnline;         // Binwasnaker (Indikasi Kepatuhan)
use App\Models\IKPA;                        // Sekjen
use App\Models\JumlahLowonganPasker;
use App\Models\JumlahRegulasiBaru;
use App\Models\PerusahaanMenerapkanSusu;
use Illuminate\Database\Eloquent\Model;

// Tambahkan model lain yang relevan dengan IKU Permenaker jika ada

class MainDashboardController extends Controller
{

    public $months;
    // Fungsi helper untuk menghitung kumulatif jumlah
    public $lastMonths;
    public $monthFilter;
    public $isMonthFilter;
    public function __construct() {
        $this->isMonthFilter = true;
        $this->months =  ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $this->lastMonths = array_slice($this->months, 0, intval(date('m')) - 1);
        
    }
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
            if($this->isMonthFilter){
                 if($m != $this->monthFilter){
                    continue;
                }
            }
           
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

        $this->monthFilter = $selectedMonth;
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $lastMonths = array_slice($months, 0, intval(date('m')) - 1);
        $yearNow = date('Y');
        $chartLabels = $selectedMonth ? [$months[$selectedMonth - 1]] : $lastMonths;
        $this->isMonthFilter = false;

        if($selectedMonth && $selectedYear == $yearNow){
            $this->isMonthFilter = true;
            $chartLabels = $selectedMonth ? [$months[$selectedMonth - 1]] : $lastMonths;
        }elseif($selectedMonth && $selectedYear != $yearNow){
            $this->isMonthFilter = true;
            $chartLabels = $selectedMonth ? [$months[$selectedMonth - 1]] : $months;
        }elseif($selectedYear != $yearNow){
            $chartLabels = $selectedMonth ? [$months[$selectedMonth - 1]] : $months;
        }

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
        $helper = new helperController();
        $s_m = $selectedMonth;
        $s_y = $selectedYear;
        $totalPenempatanKemenaker = $helper->totalSummary('sum', 'jumlah', JumlahPenempatanKemnaker::class, $s_y, $s_m);
        // 4. Jumlah Peserta Pelatihan Berbasis Kompetensi (Binalavotas)
        $totalPesertaPelatihan = $helper->totalSummary('sum','jumlah', JumlahKepesertaanPelatihan::class, $s_y, $s_m);
        // 5. Jumlah Lulusan Pelatihan yang Bekerja/Wirausaha (Sekjen - Polteknaker)
        $totalLulusanBekerja = $helper->totalSummary('sum','jumlah_lulusan_bekerja',LulusanPolteknakerBekerja::class, $s_y, $s_m);
        // 6. Jumlah Rekomendasi Kebijakan (Barenbang - Jenis Output Rekomendasi)
        $totalRekomendasiKebijakan = $helper->totalSummary('sum','jumlah',JumlahKajianRekomendasi::class, $s_y, $s_m);
        $totalRekomendasiKebijakan = $helper->totalSummary('sum','jumlah',JumlahKajianRekomendasi::class, $s_y, $s_m);
        // 7. Rata-rata IKPA (Sekjen)
        $avgIkpaKementerian = $helper->totalSummary('avg','nilai_akhir',IKPA::class, $s_y, $s_m);

        
        $totalWlkpReported = PelaporanWlkpOnline::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perusahaan_melapor');
        $totalPerusahaanSusu = PerusahaanMenerapkanSusu::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perusahaan_susu');
        $totalLowonganPasker = JumlahLowonganPasker::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_lowongan');
        $totalRegulasi = JumlahRegulasiBaru::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_regulasi');
       
        // $totalPenempatanKemenaker = JumlahPenempatanKemnaker::query()
        //     ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        //     ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
        //     ->sum('jumlah');


        // $totalPesertaPelatihan = JumlahKepesertaanPelatihan::query()
        //     ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        //     ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
        //     ->sum('jumlah');
        
        // $totalLulusanBekerja = LulusanPolteknakerBekerja::query()
        //     ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        //     ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
        //     ->sum('jumlah_lulusan_bekerja');
        
        // $totalRekomendasiKebijakan = JumlahKajianRekomendasi::query()
        //     ->where('jenis_output', 2) // 2 untuk Rekomendasi
        //     ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        //     ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
        //     ->sum('jumlah');
            
        // $avgIkpaKementerian = IKPA::query()
        //     ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        //     ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
        //     ->avg('nilai_akhir');

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
        $queryPenempatan = JumlahPenempatanKemnaker::query()
        ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));
        $penempatanBulanan = $this->getMonthlyTrendData(clone $queryPenempatan, 'bulan', 'jumlah');
        $chartData['penempatan_kemnaker'] = [
            'labels' => $chartLabels,
            'bulanan' => $penempatanBulanan,
            'kumulatif' => $this->calculateCumulative($penempatanBulanan)
        ];


        // Chart 4: Tren Peserta Pelatihan (Binalavotas)
        $queryPesertaPelatihan = JumlahKepesertaanPelatihan::query()
         ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));
        $pesertaPelatihanBulanan = $this->getMonthlyTrendData(clone $queryPesertaPelatihan, 'bulan', 'jumlah');
        $chartData['peserta_pelatihan'] = [
            'labels' => $chartLabels,
            'bulanan' => $pesertaPelatihanBulanan,
            'kumulatif' => $this->calculateCumulative($pesertaPelatihanBulanan)
        ];
        
        // Chart 5: Tren Lulusan Polteknaker Bekerja (Sekjen)
        $queryLulusanBekerja = LulusanPolteknakerBekerja::query()
         ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));
        $lulusanBekerjaBulanan = $this->getMonthlyTrendData(clone $queryLulusanBekerja, 'bulan', 'jumlah_lulusan_bekerja');
        $chartData['lulusan_bekerja'] = [
            'labels' => $chartLabels,
            'bulanan' => $lulusanBekerjaBulanan, // Ini adalah rata-rata bulanan
            'kumulatif' => $this->calculateCumulative($lulusanBekerjaBulanan) // Ini adalah kumulatif dari rata-rata bulanan, interpretasinya perlu hati-hati
        ];

        // Chart 4: WLKP Online (Binwasnaker K3)
        $queryWLKP = PelaporanWlkpOnline::query()->where('tahun', $selectedYear);
        $WLKPBulanan = $this->getMonthlyTrendData(clone $queryWLKP, 'bulan', 'jumlah_perusahaan_melapor', 'AVG');
        $chartData['wlkp'] = [
            'labels' => $chartLabels,
            'bulanan' => $WLKPBulanan, // Ini adalah rata-rata bulanan
            'kumulatif' => $this->calculateCumulative($WLKPBulanan) // Ini adalah kumulatif dari rata-rata bulanan, interpretasinya perlu hati-hati
        ];

        //Chart 5: Regulasi (Sekjen)
        $queryRegulasi = JumlahRegulasiBaru::query()->where('tahun', $selectedYear);
        $RegulasiBulanan = $this->getMonthlyTrendData(clone $queryRegulasi, 'bulan', 'jumlah_regulasi', 'AVG');
        $chartData['regulasi'] = [
            'labels' => $chartLabels,
            'bulanan' => $RegulasiBulanan,
            'kumulatif' => $this->calculateCumulative($RegulasiBulanan)
        ];

        // Chart 6: Tren Rekomendasi Kebijakan (Barenbang)
        $queryRekomendasi = JumlahKajianRekomendasi::query()
        ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
        ->where('jenis_output', 2);
        $rekomendasiBulanan = $this->getMonthlyTrendData(clone $queryRekomendasi, 'bulan', 'jumlah');
        $chartData['rekomendasi_kebijakan'] = [
            'labels' => $chartLabels,
            'bulanan' => $rekomendasiBulanan,
            'kumulatif' => $this->calculateCumulative($rekomendasiBulanan)
        ];
        
        // Chart 7: Tren Rata-rata IKPA (Sekjen)
        $queryIkpa = Ikpa::query()
        ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));
        // Untuk IKPA, kita tampilkan nilai rata-rata bulanan, dan kumulatifnya adalah rata-rata dari rata-rata bulanan (kurang ideal, tapi untuk tren)
        // Atau bisa juga kumulatif jumlah nilai IKPA / jumlah bulan (perlu penyesuaian)
        $ikpaBulanan = $this->getMonthlyTrendData(clone $queryIkpa, 'bulan', 'nilai_akhir', 'AVG');
        $chartData['ikpa'] = [
            'labels' => $chartLabels,
            'bulanan' => $ikpaBulanan, // Ini adalah rata-rata bulanan
            'kumulatif' => $this->calculateCumulative($ikpaBulanan) // Ini adalah kumulatif dari rata-rata bulanan, interpretasinya perlu hati-hati
        ];

        $querySusu = PerusahaanMenerapkanSusu::query()
         ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));
        $susuBulanan = $this->getMonthlyTrendData(clone $querySusu, 'bulan', 'jumlah_perusahaan_susu', 'AVG');
        $chartData['susu'] = [
            'labels' => $chartLabels,
            'bulanan' => $susuBulanan, // Ini adalah rata-rata bulanan
            'kumulatif' => $this->calculateCumulative($susuBulanan) // Ini adalah kumulatif dari rata-rata bulanan, interpretasinya perlu hati-hati
        ];



        $queryWLKP = PelaporanWlkpOnline::query()
         ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));
        $WLKPBulanan = $this->getMonthlyTrendData(clone $queryWLKP, 'bulan', 'jumlah_perusahaan_melapor', 'AVG');
        $chartData['wlkp'] = [
            'labels' => $chartLabels,
            'bulanan' => $WLKPBulanan, // Ini adalah rata-rata bulanan
            'kumulatif' => $this->calculateCumulative($WLKPBulanan) // Ini adalah kumulatif dari rata-rata bulanan, interpretasinya perlu hati-hati
        ];

        // Chart 8: Tren Rata-rata Loker (Binapenta)
        $queryLoker = JumlahLowonganPasker::query()
         ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));
        $LokerBulanan = $this->getMonthlyTrendData(clone $queryLoker, 'bulan', 'jumlah_lowongan', 'AVG');
        $chartData['lowongan_pasker'] = [
            'labels' => $chartLabels,
            'bulanan' => $LokerBulanan, // Ini adalah rata-rata bulanan
            'kumulatif' => $this->calculateCumulative($LokerBulanan) // Ini adalah kumulatif dari rata-rata bulanan, interpretasinya perlu hati-hati
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
        
        $monthsForFilter = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

             $lastMonths = array_slice($monthsForFilter, 0, intval(date('m')) - 1);
            $monthsForFilter = $lastMonths;
         $viewData = compact(
            'persenSelesaiBpk', 'persenSelesaiInternal', 'totalPenempatanKemenaker',
            'totalPesertaPelatihan', 'totalLulusanBekerja', 'totalRekomendasiKebijakan', 'avgIkpaKementerian',
            'availableYears', 'selectedYear', 'selectedMonth', 'totalPerusahaanSusu', 'totalWlkpReported', 'totalLowonganPasker', 'totalRegulasi', 'monthsForFilter'
        );
        
        return view('dashboards.main', array_merge($viewData, ['chartData' => $chartData]));
    }

    public function filterBulan(Request $request){
        $get_tahun = $request->post('tahun');
        $monthsForFilter = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        if($get_tahun == date('Y')){
             $lastMonths = array_slice($monthsForFilter, 0, intval(date('m')) - 1);
            return array('month' => $lastMonths);
        }
        return array('month' => $monthsForFilter);
    }
}