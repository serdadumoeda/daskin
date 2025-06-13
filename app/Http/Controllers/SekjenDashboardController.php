<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IKPA; // Sesuaikan jika nama model berbeda
use App\Models\ProgressMou;
use App\Models\JumlahRegulasiBaru;
use App\Models\JumlahPenangananKasus;
use App\Models\PenyelesaianBmn;
use App\Models\PersentaseKehadiran;
use App\Models\MonevMonitoringMedia;
use App\Models\LulusanPolteknakerBekerja;
use App\Models\SdmMengikutiPelatihan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Mungkin tidak terpakai jika bulan dan tahun sudah integer

class SekjenDashboardController extends Controller
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

    // Fungsi helper generik untuk mengambil data bulanan (SUM atau COUNT)
    private function getMonthlyData($model, string $yearColumn, string $monthColumn, string $valueColumn, string $selectedYear, ?string $selectedMonth, string $aggregationType = 'SUM', array $filters = [])
    {
        $query = $model::query()->where($yearColumn, $selectedYear);

        foreach ($filters as $filterColumn => $filterValue) {
            $query->where($filterColumn, $filterValue);
        }

        if ($selectedMonth) {
            $query->where($monthColumn, $selectedMonth);
            if ($aggregationType === 'COUNT') {
                $data = [(int)$query->count()];
            } else {
                $data = [(int)$query->sum($valueColumn)];
            }
            return $data;
        } else {
            if ($aggregationType === 'COUNT') {
                $monthlyData = $query->select($monthColumn, DB::raw("COUNT({$valueColumn}) as total_value")) // valueColumn bisa jadi '*' atau 'id' untuk COUNT
                    ->groupBy($monthColumn)
                    ->orderBy($monthColumn, 'asc')
                    ->get()
                    ->pluck('total_value', $monthColumn);
            } else {
                $monthlyData = $query->select($monthColumn, DB::raw("SUM({$valueColumn}) as total_value"))
                    ->groupBy($monthColumn)
                    ->orderBy($monthColumn, 'asc')
                    ->get()
                    ->pluck('total_value', $monthColumn);
            }

            $result = [];
            for ($m = 1; $m <= 12; $m++) {
                $result[] = (int)($monthlyData->get($m) ?? 0);
            }
            return $result;
        }
    }


    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->input('tahun', $currentYear);
        $selectedMonth = $request->input('bulan');

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartLabels = $selectedMonth ? [$months[(int)$selectedMonth - 1]] : $months;

        // --- Data untuk Kartu Ringkasan ---
        $totalIkpa = IKPA::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->avg('nilai_akhir'); // Rata-rata IKPA
        $totalMouBaru = ProgressMou::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->count();
        $totalRegulasiBaru = JumlahRegulasiBaru::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_regulasi');
        $totalPenangananKasus = JumlahPenangananKasus::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perkara');
        $totalKuantitasBmn = PenyelesaianBmn::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('kuantitas');
        // Untuk persentase kehadiran, kartu mungkin lebih kompleks (rata-rata persentase WFO)
        $totalOrangHadirWFO = PersentaseKehadiran::query()->where('status_kehadiran', 1)->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_orang');
        $totalOrangCuti = PersentaseKehadiran::query()->where('status_kehadiran', 2)->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_orang');;
        $totalOrangDinasLuar = PersentaseKehadiran::query()->where('status_kehadiran', 3)->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_orang');;
        $totalOrangSakit = PersentaseKehadiran::query()->where('status_kehadiran', 4)->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_orang');;
        $totalOrangTugasBelajar = PersentaseKehadiran::query()->where('status_kehadiran', 5)->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_orang');;
        $totalOrangTanpaKeterangan = PersentaseKehadiran::query()->where('status_kehadiran', 6)->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_orang');;
        $totalBeritaMonev = MonevMonitoringMedia::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_berita');
        $totalLulusanBekerja = LulusanPolteknakerBekerja::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_lulusan_bekerja');
        $totalSdmPelatihan = SdmMengikutiPelatihan::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_peserta');

        // --- Logika Chart ---
        // 1. IKPA
        $ikpaBulanan = $this->getMonthlyData(new IKPA, 'tahun', 'bulan', 'nilai_akhir', $selectedYear, $selectedMonth, 'AVG'); // IKPA biasanya rata-rata
        $ikpaKumulatif = $this->calculateCumulative($ikpaBulanan); // Kumulatif dari rata-rata mungkin kurang bermakna, bisa dipertimbangkan ulang

        // 2. Progress MOU (Jumlah MOU baru per bulan)
        $mouBulanan = $this->getMonthlyData(new ProgressMou, 'tahun', 'bulan', 'id', $selectedYear, $selectedMonth, 'COUNT'); // Menghitung jumlah MOU baru
        $mouKumulatif = $this->calculateCumulative($mouBulanan);

        // 3. Jumlah Regulasi Baru
        $regulasiBulanan = $this->getMonthlyData(new JumlahRegulasiBaru, 'tahun', 'bulan', 'jumlah_regulasi', $selectedYear, $selectedMonth);
        $regulasiKumulatif = $this->calculateCumulative($regulasiBulanan);

        // 4. Jumlah Penanganan Kasus
        $penangananKasusBulanan = $this->getMonthlyData(new JumlahPenangananKasus, 'tahun', 'bulan', 'jumlah_perkara', $selectedYear, $selectedMonth);
        $penangananKasusKumulatif = $this->calculateCumulative($penangananKasusBulanan);

        // 5. Penyelesaian BMN (Kuantitas)
        $penyelesaianBmnBulanan = $this->getMonthlyData(new PenyelesaianBmn, 'tahun', 'bulan', 'kuantitas', $selectedYear, $selectedMonth);
        $penyelesaianBmnKumulatif = $this->calculateCumulative($penyelesaianBmnBulanan);

        // 6. Persentase Kehadiran (Jumlah Orang WFO)
        $kehadiranWFOBulanan = $this->getMonthlyData(new PersentaseKehadiran, 'tahun', 'bulan', 'jumlah_orang', $selectedYear, $selectedMonth, 'SUM', ['status_kehadiran' => 1]);
        $kehadiranWFOKumulatif = $this->calculateCumulative($kehadiranWFOBulanan);
        // Jika ingin total kehadiran semua status:
        // $kehadiranTotalBulanan = $this->getMonthlyData(new PersentaseKehadiran, 'tahun', 'bulan', 'jumlah_orang', $selectedYear, $selectedMonth, 'SUM');

        // 7. Monev Monitoring Media (Jumlah Berita)
        $monevMediaBulanan = $this->getMonthlyData(new MonevMonitoringMedia, 'tahun', 'bulan', 'jumlah_berita', $selectedYear, $selectedMonth);
        $monevMediaKumulatif = $this->calculateCumulative($monevMediaBulanan);

        // 8. Lulusan Polteknaker Bekerja
        $lulusanBekerjaBulanan = $this->getMonthlyData(new LulusanPolteknakerBekerja, 'tahun', 'bulan', 'jumlah_lulusan_bekerja', $selectedYear, $selectedMonth);
        $lulusanBekerjaKumulatif = $this->calculateCumulative($lulusanBekerjaBulanan);
        // Jika ingin membandingkan dengan total lulusan:
        // $totalLulusanBulanan = $this->getMonthlyData(new LulusanPolteknakerBekerja, 'tahun', 'bulan', 'jumlah_lulusan', $selectedYear, $selectedMonth);

        // 9. SDM Mengikuti Pelatihan
        $sdmPelatihanBulanan = $this->getMonthlyData(new SdmMengikutiPelatihan, 'tahun', 'bulan', 'jumlah_peserta', $selectedYear, $selectedMonth);
        $sdmPelatihanKumulatif = $this->calculateCumulative($sdmPelatihanBulanan);

        // Ambil tahun yang tersedia untuk filter
        // Ini perlu disesuaikan untuk mengambil tahun dari semua tabel yang relevan
        $yearsIkpa = IKPA::select('tahun')->distinct();
        $yearsMou = ProgressMou::select('tahun')->distinct();
        $yearsRegulasi = JumlahRegulasiBaru::select('tahun')->distinct();
        $yearsKasus = JumlahPenangananKasus::select('tahun')->distinct();
        $yearsBmn = PenyelesaianBmn::select('tahun')->distinct();
        $yearsKehadiran = PersentaseKehadiran::select('tahun')->distinct();
        $yearsMonev = MonevMonitoringMedia::select('tahun')->distinct();
        $yearsLulusan = LulusanPolteknakerBekerja::select('tahun')->distinct();
        $yearsSdm = SdmMengikutiPelatihan::select('tahun')->distinct();

        $availableYears = $yearsIkpa
            ->union($yearsMou)->union($yearsRegulasi)->union($yearsKasus)
            ->union($yearsBmn)->union($yearsKehadiran)->union($yearsMonev)
            ->union($yearsLulusan)->union($yearsSdm)
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($availableYears->isEmpty() && !$availableYears->contains($currentYear)) {
            $availableYears->push($currentYear);
            $availableYears = $availableYears->sortDesc()->values();
        }

        $viewData = compact(
            'totalIkpa', 'totalMouBaru', 'totalRegulasiBaru', 'totalPenangananKasus',
            'totalKuantitasBmn', 'totalOrangHadirWFO', 'totalBeritaMonev',
            'totalLulusanBekerja', 'totalSdmPelatihan',
            'availableYears', 'selectedYear', 'selectedMonth',
            'totalOrangCuti', 'totalOrangDinasLuar', 'totalOrangSakit',
            'totalOrangTugasBelajar', 'totalOrangTanpaKeterangan'
        );

        $chartData = [
            'ikpa' => ['labels' => $chartLabels, 'bulanan' => $ikpaBulanan, 'kumulatif' => $ikpaKumulatif],
            'mou' => ['labels' => $chartLabels, 'bulanan' => $mouBulanan, 'kumulatif' => $mouKumulatif],
            'regulasi' => ['labels' => $chartLabels, 'bulanan' => $regulasiBulanan, 'kumulatif' => $regulasiKumulatif],
            'penanganan_kasus' => ['labels' => $chartLabels, 'bulanan' => $penangananKasusBulanan, 'kumulatif' => $penangananKasusKumulatif],
            'penyelesaian_bmn' => ['labels' => $chartLabels, 'bulanan' => $penyelesaianBmnBulanan, 'kumulatif' => $penyelesaianBmnKumulatif],
            'kehadiran_wfo' => ['labels' => $chartLabels, 'bulanan' => $kehadiranWFOBulanan, 'kumulatif' => $kehadiranWFOKumulatif],
            'monev_media' => ['labels' => $chartLabels, 'bulanan' => $monevMediaBulanan, 'kumulatif' => $monevMediaKumulatif],
            'lulusan_bekerja' => ['labels' => $chartLabels, 'bulanan' => $lulusanBekerjaBulanan, 'kumulatif' => $lulusanBekerjaKumulatif],
            'sdm_pelatihan' => ['labels' => $chartLabels, 'bulanan' => $sdmPelatihanBulanan, 'kumulatif' => $sdmPelatihanKumulatif],
        ];

        return view('dashboards.sekjen', array_merge($viewData, ['chartData' => $chartData]));
    }
}
