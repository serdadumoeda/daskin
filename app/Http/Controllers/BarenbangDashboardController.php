<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JumlahKajianRekomendasi;
use App\Models\AplikasiIntegrasiSiapkerja; // Pastikan nama model sesuai
use App\Models\DataKetenagakerjaan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Mungkin tidak terpakai jika bulan dan tahun sudah integer

class BarenbangDashboardController extends Controller
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
    private function getMonthlyDataRegular($model, string $yearColumn, string $monthColumn, string $valueColumn, string $selectedYear, ?string $selectedMonth, string $aggregationType = 'SUM', array $filters = [])
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
            $selectExpr = $aggregationType === 'COUNT' ? DB::raw("COUNT({$valueColumn}) as total_value") : DB::raw("SUM({$valueColumn}) as total_value");
            
            $monthlyDataGrouped = $query->select($monthColumn, $selectExpr)
                ->groupBy($monthColumn)
                ->orderBy($monthColumn, 'asc')
                ->get()
                ->pluck('total_value', $monthColumn);
            
            $result = [];
            for ($m = 1; $m <= 12; $m++) {
                $result[] = (int)($monthlyDataGrouped->get($m) ?? 0);
            }
            return $result;
        }
    }

    public function index(Request $request)
    {
        $currentYear = date('Y');
        // Filter untuk Kajian, Rekomendasi, Aplikasi
        $selectedYearMain = $request->input('tahun_main', $currentYear);
        $selectedMonthMain = $request->input('bulan_main'); 

        // Filter untuk Data Ketenagakerjaan (Sakernas)
        $selectedYearSakernas = $request->input('tahun_sakernas', $currentYear);
        $selectedPeriodeSakernas = $request->input('periode_sakernas'); // 'feb' atau 'ags'

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        
        // --- Data untuk Kartu Ringkasan ---
        $totalKajian = JumlahKajianRekomendasi::query()
            ->where('jenis_output', 1) // 1: Kajian
            ->when($selectedYearMain, fn($q) => $q->where('tahun', $selectedYearMain))
            ->when($selectedMonthMain, fn($q) => $q->where('bulan', $selectedMonthMain))
            ->sum('jumlah');
        $totalRekomendasi = JumlahKajianRekomendasi::query()
            ->where('jenis_output', 2) // 2: Rekomendasi
            ->when($selectedYearMain, fn($q) => $q->where('tahun', $selectedYearMain))
            ->when($selectedMonthMain, fn($q) => $q->where('bulan', $selectedMonthMain))
            ->sum('jumlah');
        $totalAplikasiTerintegrasi = AplikasiIntegrasiSiapkerja::query()
            ->where('status_integrasi', 1) // 1: Terintegrasi
            ->when($selectedYearMain, fn($q) => $q->where('tahun', $selectedYearMain))
            ->when($selectedMonthMain, fn($q) => $q->where('bulan', $selectedMonthMain))
            ->count(); // Menghitung jumlah aplikasi (setiap baris adalah 1 aplikasi)
        
        // Kartu Data Ketenagakerjaan (ambil data terbaru jika tidak ada filter periode spesifik)
        $latestSakernasData = DataKetenagakerjaan::query()
            ->where('tahun', $selectedYearSakernas)
            ->when($selectedPeriodeSakernas, function($q) use ($selectedPeriodeSakernas) {
                return $q->where('bulan', $selectedPeriodeSakernas == 'feb' ? 2 : 8);
            })
            ->orderBy('bulan', 'desc') // Ambil data terbaru jika tidak ada periode spesifik
            ->first();

        // --- Logika Chart ---
        $chartLabelsMain = $selectedMonthMain ? [$months[(int)$selectedMonthMain - 1]] : $months;

        // 1. Chart Jumlah Kajian
        $kajianBulanan = $this->getMonthlyDataRegular(new JumlahKajianRekomendasi, 'tahun', 'bulan', 'jumlah', $selectedYearMain, $selectedMonthMain, 'SUM', ['jenis_output' => 1]);
        $kajianKumulatif = $this->calculateCumulative($kajianBulanan);

        // 2. Chart Jumlah Rekomendasi
        $rekomendasiBulanan = $this->getMonthlyDataRegular(new JumlahKajianRekomendasi, 'tahun', 'bulan', 'jumlah', $selectedYearMain, $selectedMonthMain, 'SUM', ['jenis_output' => 2]);
        $rekomendasiKumulatif = $this->calculateCumulative($rekomendasiBulanan);

        // 3. Chart Jumlah Aplikasi Terintegrasi
        // Untuk COUNT, 'id' atau '*' bisa digunakan sebagai valueColumn jika setiap baris dihitung
        $aplikasiBulanan = $this->getMonthlyDataRegular(new AplikasiIntegrasiSiapkerja, 'tahun', 'bulan', 'id', $selectedYearMain, $selectedMonthMain, 'COUNT', ['status_integrasi' => 1]);
        $aplikasiKumulatif = $this->calculateCumulative($aplikasiBulanan);

        // 4. Chart Data Ketenagakerjaan (Feb & Ags)
        $sakernasLabels = [];
        $angkatanKerjaData = []; $tpakData = []; $bekerjaData = []; 
        $pengangguranTerbukaData = []; $tptData = []; $bukanAngkatanKerjaData = [];
        $tingkatKesempatanKerjaData = [];

        $periodeSakernasQuery = DataKetenagakerjaan::query()->where('tahun', $selectedYearSakernas);
        if ($selectedPeriodeSakernas) {
            $bulanFilter = $selectedPeriodeSakernas == 'feb' ? 2 : 8;
            $periodeSakernasQuery->where('bulan', $bulanFilter);
            $sakernasLabels = [$selectedPeriodeSakernas == 'feb' ? 'Februari' : 'Agustus'];
        } else {
            $sakernasLabels = ['Februari', 'Agustus'];
        }

        $dataSakernas = $periodeSakernasQuery->orderBy('bulan', 'asc')->get();
        
        if ($selectedPeriodeSakernas) {
            $data = $dataSakernas->first();
            $angkatanKerjaData = [(float)($data->angkatan_kerja ?? 0)];
            $tpakData = [(float)($data->tpak ?? 0)];
            $bekerjaData = [(float)($data->bekerja ?? 0)];
            $pengangguranTerbukaData = [(float)($data->pengangguran_terbuka ?? 0)];
            $tptData = [(float)($data->tpt ?? 0)];
            $bukanAngkatanKerjaData = [(float)($data->bukan_angkatan_kerja ?? 0)];
            $tingkatKesempatanKerjaData = [(float)($data->tingkat_kesempatan_kerja ?? 0)];
        } else {
             $dataFeb = $dataSakernas->firstWhere('bulan', 2);
             $dataAgs = $dataSakernas->firstWhere('bulan', 8);

             $angkatanKerjaData = [(float)($dataFeb->angkatan_kerja ?? 0), (float)($dataAgs->angkatan_kerja ?? 0)];
             $tpakData = [(float)($dataFeb->tpak ?? 0), (float)($dataAgs->tpak ?? 0)];
             $bekerjaData = [(float)($dataFeb->bekerja ?? 0), (float)($dataAgs->bekerja ?? 0)];
             $pengangguranTerbukaData = [(float)($dataFeb->pengangguran_terbuka ?? 0), (float)($dataAgs->pengangguran_terbuka ?? 0)];
             $tptData = [(float)($dataFeb->tpt ?? 0), (float)($dataAgs->tpt ?? 0)];
             $bukanAngkatanKerjaData = [(float)($dataFeb->bukan_angkatan_kerja ?? 0), (float)($dataAgs->bukan_angkatan_kerja ?? 0)];
             $tingkatKesempatanKerjaData = [(float)($dataFeb->tingkat_kesempatan_kerja ?? 0), (float)($dataAgs->tingkat_kesempatan_kerja ?? 0)];
        }


        // Ambil tahun yang tersedia untuk filter
        $availableYearsMain = JumlahKajianRekomendasi::select('tahun')->distinct()
                            ->union(AplikasiIntegrasiSiapkerja::select('tahun')->distinct())
                            ->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYearsMain->isEmpty() && !$availableYearsMain->contains($currentYear)) {
            $availableYearsMain->push($currentYear);
            $availableYearsMain = $availableYearsMain->sortDesc()->values();
        }

        $availableYearsSakernas = DataKetenagakerjaan::select('tahun')->distinct()
                                    ->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYearsSakernas->isEmpty() && !$availableYearsSakernas->contains($currentYear)) {
            $availableYearsSakernas->push($currentYear);
            $availableYearsSakernas = $availableYearsSakernas->sortDesc()->values();
        }
        
        $viewData = compact(
            'totalKajian', 'totalRekomendasi', 'totalAplikasiTerintegrasi', 'latestSakernasData',
            'availableYearsMain', 'selectedYearMain', 'selectedMonthMain', 'chartLabelsMain',
            'availableYearsSakernas', 'selectedYearSakernas', 'selectedPeriodeSakernas'
        );

        $chartData = [
            'kajian' => ['labels' => $chartLabelsMain, 'bulanan' => $kajianBulanan, 'kumulatif' => $kajianKumulatif],
            'rekomendasi' => ['labels' => $chartLabelsMain, 'bulanan' => $rekomendasiBulanan, 'kumulatif' => $rekomendasiKumulatif],
            'aplikasi' => ['labels' => $chartLabelsMain, 'bulanan' => $aplikasiBulanan, 'kumulatif' => $aplikasiKumulatif],
            'sakernas' => [
                'labels' => $sakernasLabels,
                'angkatan_kerja' => $angkatanKerjaData,
                'tpak' => $tpakData,
                'bekerja' => $bekerjaData,
                'pengangguran_terbuka' => $pengangguranTerbukaData,
                'tpt' => $tptData,
                'bukan_angkatan_kerja' => $bukanAngkatanKerjaData,
                'tingkat_kesempatan_kerja' => $tingkatKesempatanKerjaData,
            ],
        ];
        
        return view('dashboards.barenbang', array_merge($viewData, ['chartData' => $chartData]));
    }
}