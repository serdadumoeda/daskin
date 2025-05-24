<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PelaporanWlkpOnline;
use App\Models\PengaduanPelanggaranNorma;
use App\Models\PenerapanSmk3;
use App\Models\SelfAssessmentNorma100; // Pastikan model ini ada dan benar
use Illuminate\Support\Facades\DB;

class BinwasnakerDashboardController extends Controller
{
    private function calculateCumulative(array $data): array
    {
        $cumulative = [];
        $sum = 0;
        foreach ($data as $value) {
            $sum += $value;
            $cumulative[] = $sum;
        }
        return $cumulative;
    }

    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->input('tahun', $currentYear);
        $selectedMonth = $request->input('bulan'); 

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
        
        $totalSelfAssessment = SelfAssessmentNorma100::query() // Menggunakan model yang benar
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_perusahaan'); // Menggunakan kolom yang benar

        // --- Logika Chart ---
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $chartWlkpLabels = []; $wlkpDataBulanan = []; $wlkpDataKumulatif = [];
        $pengaduanChartLabels = []; $pengaduanDataBulanan = []; $pengaduanDataKumulatif = [];
        $smk3ChartLabels = []; $smk3DataBulanan = []; $smk3DataKumulatif = [];
        $saChartLabels = []; $saDataBulanan = []; $saDataKumulatif = []; // Inisialisasi untuk SA

        if ($selectedMonth) {
            // === JIKA BULAN DIPILIH ===
            $singleMonthLabel = [$months[$selectedMonth - 1]];

            // WLKP
            $chartWlkpLabels = $singleMonthLabel;
            $jumlahWlkp = PelaporanWlkpOnline::query()->where('tahun', $selectedYear)->where('bulan', $selectedMonth)->sum('jumlah_perusahaan_melapor');
            $wlkpDataBulanan = [$jumlahWlkp];
            $wlkpDataKumulatif = [$jumlahWlkp];

            // Pengaduan
            $pengaduanChartLabels = $singleMonthLabel;
            $jumlahPengaduan = PengaduanPelanggaranNorma::query()->where('tahun_pengaduan', $selectedYear)->where('bulan_pengaduan', $selectedMonth)->sum('jumlah_kasus');
            $pengaduanDataBulanan = [$jumlahPengaduan];
            $pengaduanDataKumulatif = [$jumlahPengaduan];
            
            // SMK3
            $smk3ChartLabels = $singleMonthLabel;
            $jumlahSmk3 = PenerapanSmk3::query()->where('tahun', $selectedYear)->where('bulan', $selectedMonth)->sum('jumlah_perusahaan');
            $smk3DataBulanan = [$jumlahSmk3];
            $smk3DataKumulatif = [$jumlahSmk3];

            // Self Assessment
            $saChartLabels = $singleMonthLabel;
            $jumlahSa = SelfAssessmentNorma100::query()->where('tahun', $selectedYear)->where('bulan', $selectedMonth)->sum('jumlah_perusahaan');
            $saDataBulanan = [$jumlahSa];
            $saDataKumulatif = [$jumlahSa];

        } else {
            // === JIKA SEMUA BULAN (TAHUNAN) ===
            $chartWlkpLabels = $months;
            $pengaduanChartLabels = $months;
            $smk3ChartLabels = $months;
            $saChartLabels = $months; // Label untuk SA

            // Fungsi helper untuk mengambil data bulanan tahunan
            $getAnnualMonthlyData = function ($model, $year, $yearColumn, $monthColumn, $valueColumn) {
                $data = $model::query()
                    ->where($yearColumn, $year)
                    ->select($monthColumn, DB::raw("SUM({$valueColumn}) as total_value"))
                    ->groupBy($monthColumn)
                    ->orderBy($monthColumn, 'asc')
                    ->get()
                    ->pluck('total_value', $monthColumn);
                
                $monthlyValues = [];
                for ($m = 1; $m <= 12; $m++) {
                    $monthlyValues[] = $data->get($m, 0);
                }
                return $monthlyValues;
            };

            // Data WLKP
            $wlkpDataBulanan = $getAnnualMonthlyData(new PelaporanWlkpOnline, $selectedYear, 'tahun', 'bulan', 'jumlah_perusahaan_melapor');
            $wlkpDataKumulatif = $this->calculateCumulative($wlkpDataBulanan);

            // Data Pengaduan
            $pengaduanDataBulanan = $getAnnualMonthlyData(new PengaduanPelanggaranNorma, $selectedYear, 'tahun_pengaduan', 'bulan_pengaduan', 'jumlah_kasus');
            $pengaduanDataKumulatif = $this->calculateCumulative($pengaduanDataBulanan);

            // Data SMK3
            $smk3DataBulanan = $getAnnualMonthlyData(new PenerapanSmk3, $selectedYear, 'tahun', 'bulan', 'jumlah_perusahaan');
            $smk3DataKumulatif = $this->calculateCumulative($smk3DataBulanan);

            // Data Self Assessment
            $saDataBulanan = $getAnnualMonthlyData(new SelfAssessmentNorma100, $selectedYear, 'tahun', 'bulan', 'jumlah_perusahaan');
            $saDataKumulatif = $this->calculateCumulative($saDataBulanan);
        }

        // Ambil tahun yang tersedia untuk filter
        $yearsWlkp = PelaporanWlkpOnline::select('tahun')->distinct();
        $yearsPengaduan = PengaduanPelanggaranNorma::select('tahun_pengaduan as tahun')->distinct();
        $yearsSmk3 = PenerapanSmk3::select('tahun')->distinct();
        $yearsSa = SelfAssessmentNorma100::select('tahun')->distinct(); // Tambahkan tahun dari SA
        
        $availableYears = $yearsWlkp
            ->union($yearsPengaduan)
            ->union($yearsSmk3)
            ->union($yearsSa) // Gabungkan tahun dari SA
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($availableYears->isEmpty() && !$availableYears->contains($currentYear)) {
            $availableYears->push($currentYear);
            $availableYears = $availableYears->sortDesc()->values();
        }
        
        $viewData = compact(
            'totalWlkpReported', 'totalPengaduanNorma', 'totalPenerapanSmk3', 'totalSelfAssessment',
            'availableYears', 'selectedYear', 'selectedMonth'
        );

        $chartData = [
            'wlkp' => ['labels' => $chartWlkpLabels, 'bulanan' => $wlkpDataBulanan, 'kumulatif' => $wlkpDataKumulatif],
            'pengaduan' => ['labels' => $pengaduanChartLabels, 'bulanan' => $pengaduanDataBulanan, 'kumulatif' => $pengaduanDataKumulatif],
            'smk3' => ['labels' => $smk3ChartLabels, 'bulanan' => $smk3DataBulanan, 'kumulatif' => $smk3DataKumulatif],
            'sa' => ['labels' => $saChartLabels, 'bulanan' => $saDataBulanan, 'kumulatif' => $saDataKumulatif], // Pastikan ini dikirim
        ];
        
        // ==== SANGAT PENTING UNTUK DEBUGGING ====
        // Hapus komentar pada baris di bawah ini untuk melihat data SA yang dikirim ke view:
        // echo "<pre>DEBUGGING DATA SA:\n";
        // echo "Selected Year: " . htmlspecialchars($selectedYear) . "\n";
        // echo "Selected Month: " . htmlspecialchars($selectedMonth ?? 'Tidak ada bulan dipilih') . "\n";
        // print_r($chartData['sa']);
        // echo "</pre>";
        // die;
        // ========================================

        return view('dashboards.binwasnaker', array_merge($viewData, ['chartData' => $chartData]));
    }
}