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
        $selectedMonth = $request->input('month_filter'); // Bisa null untuk semua bulan

        // --- Data untuk Kartu Ringkasan ---
        $totalWlkpReported = PelaporanWlkpOnline::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_perusahaan_melapor');

        $totalPengaduanNorma = PengaduanPelanggaranNorma::query()
            ->when($selectedYear, fn($q) => $q->where('tahun_pengaduan', $selectedYear)) // Sesuaikan kolom tahun
            ->when($selectedMonth, fn($q) => $q->where('bulan_pengaduan', $selectedMonth)) // Sesuaikan kolom bulan
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
        $limit = 4; // Tampilkan top 4 + Lainnya
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
        
        // 3. Jumlah Perusahaan Menerapkan SMK3 per Kategori Penilaian
        $smk3PerKategori = PenerapanSmk3::select('kategori_penilaian', DB::raw('SUM(jumlah_perusahaan) as total'))
            ->where('tahun', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('kategori_penilaian')
            ->pluck('total', 'kategori_penilaian');

        $smk3ChartLabels = $smk3PerKategori->keys()->toArray();
        $smk3ChartDataValues = $smk3PerKategori->values()->toArray();

        // 4. Distribusi Hasil Self Assessment Norma 100
        $assessmentResults = SelfAssessmentNorma100::select('hasil_assessment', DB::raw('SUM(jumlah_perusahaan) as total'))
            ->where('tahun', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('hasil_assessment')
            ->get()
            ->map(function ($item) {
                return ['name' => $item->hasil_assessment, 'value' => $item->total];
            });


        $availableYears = PelaporanWlkpOnline::select('tahun')->distinct() // Ambil dari salah satu tabel utama
                            ->orderBy('tahun', 'desc')->pluck('tahun');
        
        return view('dashboards.binwasnaker', compact(
            'totalWlkpReported',
            'totalPengaduanNorma',
            'totalPenerapanSmk3',
            'totalSelfAssessment',
            'wlkpChartLabels',
            'wlkpChartDataValues',
            'pengaduanChartData',
            'smk3ChartLabels',
            'smk3ChartDataValues',
            'assessmentResults',
            'availableYears',
            'selectedYear',
            'selectedMonth'
        ));
    }
}
