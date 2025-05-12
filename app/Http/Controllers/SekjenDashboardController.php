<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgressMou;
use App\Models\JumlahRegulasiBaru;
use App\Models\JumlahPenangananKasus;
use App\Models\PenyelesaianBmn;
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
        
        $totalNilaiPenyelesaianBmn = PenyelesaianBmn::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('total_aset_rp'); // Asumsi ini adalah nilai yang ingin ditampilkan

        // Rata-rata % Kehadiran (WFO)
        // Ini memerlukan logika lebih kompleks jika ingin rata-rata persentase sebenarnya.
        // Untuk sederhana, kita hitung total orang WFO dibagi total orang (jika ada data total pegawai per satker)
        // Atau, tampilkan jumlah orang WFO.
        $totalWFO = PersentaseKehadiran::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->where('status_kehadiran', 1) // 1: WFO
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
                // Menggunakan accessor dari model
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

        // 4. Tren Penyelesaian BMN (Total Nilai Aset) per Bulan
        $bmnPerBulan = PenyelesaianBmn::select('bulan', DB::raw('SUM(total_aset_rp) as total_nilai'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total_nilai', 'bulan');
        $bmnChartLabels = [];
        $bmnChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $bmnChartLabels[] = Carbon::create()->month($m)->format('M');
            $bmnChartDataValues[] = $bmnPerBulan->get($m, 0);
        }
        
        // 5. Kehadiran (Contoh: Jumlah WFO vs Non-WFO (Cuti, DL, Sakit, dll) per bulan)
        $kehadiranData = PersentaseKehadiran::select('bulan', 'status_kehadiran', DB::raw('SUM(jumlah_orang) as total_orang'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan', 'status_kehadiran')
            ->orderBy('bulan')
            ->get();
        
        $kehadiranChartLabels = [];
        $kehadiranWFOData = [];
        $kehadiranLainData = []; // Cuti, DL, Sakit, dll.
        $tempKehadiran = [];

        for ($m=1; $m <= 12 ; $m++) { 
            $kehadiranChartLabels[] = Carbon::create()->month($m)->format('M');
            $tempKehadiran[$m] = ['wfo' => 0, 'lain' => 0];
        }
        foreach($kehadiranData as $data) {
            if ($data->status_kehadiran == 1) { // WFO
                $tempKehadiran[$data->bulan]['wfo'] += $data->total_orang;
            } else {
                $tempKehadiran[$data->bulan]['lain'] += $data->total_orang;
            }
        }
        foreach($tempKehadiran as $dataBulan) {
            $kehadiranWFOData[] = $dataBulan['wfo'];
            $kehadiranLainData[] = $dataBulan['lain'];
        }


        $availableYears = ProgressMou::select('tahun')->distinct() // Ambil dari salah satu tabel utama Sekjen
                            ->orderBy('tahun', 'desc')->pluck('tahun');
        
        return view('dashboards.sekjen', compact(
            'totalMoU',
            'totalRegulasi',
            'totalKasusDitangani',
            'totalNilaiPenyelesaianBmn',
            'totalWFO', // Kirim total WFO untuk kartu
            'totalBeritaMonev',
            'totalLulusanBekerja',
            'totalSdmPelatihan',
            'mouChartLabels',
            'mouChartDataValues',
            'regulasiPerJenis', // Ini sudah array of objects {name: ..., value: ...}
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
