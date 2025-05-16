<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgressMou;
use App\Models\JumlahRegulasiBaru;
use App\Models\JumlahPenangananKasus;
use App\Models\PenyelesaianBmn; // Pastikan ini di-use
use App\Models\PersentaseKehadiran;
use App\Models\MonevMonitoringMedia;
use App\Models\LulusanPolteknakerBekerja;
use App\Models\SdmMengikutiPelatihan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SekjenDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->input('year_filter', $currentYear);
        $selectedMonth = $request->input('month_filter'); // Bisa null untuk semua bulan

        // --- Data untuk Kartu Ringkasan ---
        $totalMoU = ProgressMou::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->count();

        $totalRegulasi = JumlahRegulasiBaru::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_regulasi');

        $totalKasusDitangani = JumlahPenangananKasus::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_perkara');
        
        // === PERBAIKAN DI SINI ===
        // Menggunakan kolom 'nilai_aset' sesuai dengan struktur tabel penyelesaian_bmn
        $totalNilaiPenyelesaianBmn = PenyelesaianBmn::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('nilai_aset'); // Diubah dari 'total_aset_rp' menjadi 'nilai_aset'
        // === AKHIR PERBAIKAN ===

        $totalWFO = PersentaseKehadiran::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->where('status_kehadiran', 1) 
            ->sum('jumlah_orang');
        
        $totalBeritaMonev = MonevMonitoringMedia::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_berita');

        $totalLulusanBekerja = LulusanPolteknakerBekerja::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_lulusan_bekerja');
        
        $totalSdmPelatihan = SdmMengikutiPelatihan::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_peserta');


        // --- Data untuk Chart ---
        // 1. Tren MoU per Bulan
        $mouPerBulan = ProgressMou::select('bulan', DB::raw('COUNT(*) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
            
        $mouChartLabels = [];
        $mouChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $mouChartLabels[] = Carbon::create()->month($m)->format('M');
            $mouChartDataValues[] = $mouPerBulan->get($m, 0); 
        }

        // 2. Komposisi Regulasi per Jenis
        $regulasiPerJenis = JumlahRegulasiBaru::select('jenis_regulasi', DB::raw('SUM(jumlah_regulasi) as total'))
            ->where('tahun', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('jenis_regulasi')
            ->get()
            ->map(function ($item) {
                $jenisText = (new JumlahRegulasiBaru(['jenis_regulasi' => $item->jenis_regulasi]))->jenis_regulasi_text;
                return ['name' => $jenisText, 'value' => $item->total];
            });
        
        // 3. Tren Jumlah Kasus Ditangani per Bulan
        $kasusPerBulan = JumlahPenangananKasus::select('bulan', DB::raw('SUM(jumlah_perkara) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $kasusChartLabels = [];
        $kasusChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $kasusChartLabels[] = Carbon::create()->month($m)->format('M');
            $kasusChartDataValues[] = $kasusPerBulan->get($m, 0);
        }

        // === PERBAIKAN DI SINI ===
        // 4. Tren Penyelesaian BMN (Total Nilai Aset) per Bulan
        // Menggunakan kolom 'nilai_aset'
        $bmnPerBulan = PenyelesaianBmn::select('bulan', DB::raw('SUM(nilai_aset) as total_nilai')) // Diubah dari 'total_aset_rp' menjadi 'nilai_aset'
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total_nilai', 'bulan');
        // === AKHIR PERBAIKAN ===
        
        $bmnChartLabels = [];
        $bmnChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $bmnChartLabels[] = Carbon::create()->month($m)->format('M');
            $bmnChartDataValues[] = $bmnPerBulan->get($m, 0);
        }
        
        // 5. Kehadiran
        $kehadiranData = PersentaseKehadiran::select('bulan', 'status_kehadiran', DB::raw('SUM(jumlah_orang) as total_orang'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan', 'status_kehadiran')
            ->orderBy('bulan')
            ->get();
        
        $kehadiranChartLabels = [];
        $kehadiranWFOData = [];
        $kehadiranLainData = [];
        $tempKehadiran = [];

        for ($m=1; $m <= 12 ; $m++) { 
            $kehadiranChartLabels[] = Carbon::create()->month($m)->format('M');
            $tempKehadiran[$m] = ['wfo' => 0, 'lain' => 0];
        }
        foreach($kehadiranData as $data) {
            if ($data->status_kehadiran == 1) {
                $tempKehadiran[$data->bulan]['wfo'] += $data->total_orang;
            } else {
                $tempKehadiran[$data->bulan]['lain'] += $data->total_orang;
            }
        }
        foreach($tempKehadiran as $dataBulan) {
            $kehadiranWFOData[] = $dataBulan['wfo'];
            $kehadiranLainData[] = $dataBulan['lain'];
        }

        $availableYearsQuery = PenyelesaianBmn::select('tahun')->distinct();
        $otherTablesYears = [
            ProgressMou::select('tahun')->distinct(),
            JumlahRegulasiBaru::select('tahun')->distinct(),
            // Tambahkan model lain jika perlu
        ];
        foreach ($otherTablesYears as $tableQuery) {
            $availableYearsQuery->union($tableQuery);
        }
        $availableYears = $availableYearsQuery->orderBy('tahun', 'desc')->pluck('tahun');
        
        return view('dashboards.sekjen', compact(
            'totalMoU',
            'totalRegulasi',
            'totalKasusDitangani',
            'totalNilaiPenyelesaianBmn',
            'totalWFO',
            'totalBeritaMonev',
            'totalLulusanBekerja',
            'totalSdmPelatihan',
            'mouChartLabels',
            'mouChartDataValues',
            'regulasiPerJenis',
            'kasusChartLabels',
            'kasusChartDataValues',
            'bmnChartLabels',
            'bmnChartDataValues',
            'kehadiranChartLabels',
            'kehadiranWFOData',
            'kehadiranLainData',
            'availableYears',
            'selectedYear',
            'selectedMonth'
        ));
    }
}