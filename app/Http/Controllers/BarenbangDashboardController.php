<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JumlahKajianRekomendasi;
use App\Models\DataKetenagakerjaan; // Pastikan model ini benar dan tabelnya ada
use App\Models\AplikasiIntegrasiSiapkerja;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarenbangDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->input('year_filter', $currentYear);
        $selectedMonth = $request->input('month_filter'); 

        // --- Data untuk Kartu Ringkasan ---
        $totalKajian = JumlahKajianRekomendasi::query()
            ->where('jenis_output', 1) // 1: Kajian
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');

        $totalRekomendasi = JumlahKajianRekomendasi::query()
            ->where('jenis_output', 2) // 2: Rekomendasi
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');
        
        $latestKetenagakerjaan = DataKetenagakerjaan::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, 
                fn($q) => $q->where('bulan', $selectedMonth), 
                // Jika bulan tidak dipilih, ambil data bulan terakhir di tahun terpilih
                fn($q) => $q->where('tahun', $selectedYear)->orderBy('bulan', 'desc')
            )
            ->orderBy('tahun', 'desc')->orderBy('bulan', 'desc') 
            ->first();

        $totalAplikasiTerintegrasi = AplikasiIntegrasiSiapkerja::query()
            ->where('status_integrasi', 1) // 1: Terintegrasi
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->count(); 

        // --- Data untuk Chart ---
        $chartLabelsBulan = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $chartLabelsBulan[] = Carbon::create()->month($m)->format('M');
        }

        // 1. Tren Kajian vs Rekomendasi per Bulan
        $kajianPerBulan = JumlahKajianRekomendasi::select('bulan', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)->where('jenis_output', 1) // Kajian
            ->groupBy('bulan')->orderBy('bulan')->pluck('total', 'bulan');
        $rekomendasiPerBulan = JumlahKajianRekomendasi::select('bulan', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)->where('jenis_output', 2) // Rekomendasi
            ->groupBy('bulan')->orderBy('bulan')->pluck('total', 'bulan');
        
        $kajianChartData = [];
        $rekomendasiChartData = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $kajianChartData[] = $kajianPerBulan->get($m, 0); 
            $rekomendasiChartData[] = $rekomendasiPerBulan->get($m, 0);
        }

        // 2. Tren TPAK dan TPT per Bulan
        // Menggunakan nama kolom yang sudah direvisi di migrasi dan model
        $ketenagakerjaanTrend = DataKetenagakerjaan::select(
                'bulan', 
                'tpak',  // Nama kolom yang benar
                'tpt'       // Nama kolom yang benar
            )
            ->where('tahun', $selectedYear)
            ->orderBy('bulan')
            ->get();
        
        $tpakChartData = [];
        $tptChartData = [];
        $ketenagakerjaanBulanLabels = []; // Label bulan untuk chart ini

        $dataBulanKetenagakerjaan = []; // Untuk menyimpan data per bulan
        for ($m=1; $m <= 12 ; $m++) {
            $ketenagakerjaanBulanLabels[] = Carbon::create()->month($m)->format('M');
            // Inisialisasi dengan null agar chart tidak menggambar garis jika tidak ada data
            $dataBulanKetenagakerjaan[$m] = ['tpak' => null, 'tpt' => null]; 
        }
        foreach($ketenagakerjaanTrend as $data) {
            // Menggunakan nama kolom yang benar dari model/database
            $dataBulanKetenagakerjaan[$data->bulan]['tpak'] = $data->tingkat_partisipasi_angkatan_kerja; 
            $dataBulanKetenagakerjaan[$data->bulan]['tpt'] = $data->tingkat_pengangguran_terbuka;   
        }
        foreach($dataBulanKetenagakerjaan as $data) {
            $tpakChartData[] = $data['tpak'];
            $tptChartData[] = $data['tpt'];
        }


        // 3. Komposisi Aplikasi Terintegrasi per Jenis Instansi
        $aplikasiPerJenisInstansi = AplikasiIntegrasiSiapkerja::select('jenis_instansi', DB::raw('COUNT(*) as total'))
            ->where('status_integrasi', 1) // Hanya yang terintegrasi
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('jenis_instansi')
            ->get()
            ->map(function ($item) {
                $jenisText = (new AplikasiIntegrasiSiapkerja(['jenis_instansi' => $item->jenis_instansi]))->jenis_instansi_text;
                return ['name' => $jenisText, 'value' => $item->total];
            });


        $availableYears = DataKetenagakerjaan::select('tahun')->distinct() 
                            ->orderBy('tahun', 'desc')->pluck('tahun');
        
        return view('dashboards.barenbang', compact(
            'totalKajian',
            'totalRekomendasi',
            'latestKetenagakerjaan',
            'totalAplikasiTerintegrasi',
            'chartLabelsBulan', // Untuk chart Kajian & Rekomendasi
            'kajianChartData',
            'rekomendasiChartData',
            'ketenagakerjaanBulanLabels', // Untuk chart TPAK & TPT
            'tpakChartData',
            'tptChartData',
            'aplikasiPerJenisInstansi',
            'availableYears',
            'selectedYear',
            'selectedMonth'
        ));
    }
}
