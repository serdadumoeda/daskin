<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JumlahKepesertaanPelatihan;
use App\Models\JumlahSertifikasiKompetensi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BinalavotasDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->input('year_filter', $currentYear);
        $selectedMonth = $request->input('month_filter'); // Bisa null untuk semua bulan

        // --- Data untuk Kartu Ringkasan ---
        $totalLulusInternal = JumlahKepesertaanPelatihan::query()
            ->where('penyelenggara_pelatihan', 1) // 1: Internal
            ->where('status_kelulusan', 1)      // 1: Lulus
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');

        $totalLulusEksternal = JumlahKepesertaanPelatihan::query()
            ->where('penyelenggara_pelatihan', 2) // 2: Eksternal
            ->where('status_kelulusan', 1)      // 1: Lulus
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');
        
        $totalSertifikasi = JumlahSertifikasiKompetensi::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_sertifikasi');

        // --- Data untuk Chart ---
        $chartLabels = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $chartLabels[] = Carbon::create()->month($m)->format('M');
        }

        // 1. Tren Lulus Pelatihan Internal per Bulan
        $lulusInternalPerBulan = JumlahKepesertaanPelatihan::select('bulan', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)
            ->where('penyelenggara_pelatihan', 1)
            ->where('status_kelulusan', 1)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $lulusInternalChartData = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $lulusInternalChartData[] = $lulusInternalPerBulan->get($m, 0); 
        }

        // 2. Tren Lulus Pelatihan Eksternal per Bulan
        $lulusEksternalPerBulan = JumlahKepesertaanPelatihan::select('bulan', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)
            ->where('penyelenggara_pelatihan', 2)
            ->where('status_kelulusan', 1)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $lulusEksternalChartData = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $lulusEksternalChartData[] = $lulusEksternalPerBulan->get($m, 0);
        }
        
        // 3. Tren Sertifikasi Kompetensi per Bulan
        $sertifikasiPerBulan = JumlahSertifikasiKompetensi::select('bulan', DB::raw('SUM(jumlah_sertifikasi) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $sertifikasiChartData = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $sertifikasiChartData[] = $sertifikasiPerBulan->get($m, 0);
        }
        
        // 4. Komposisi Tipe Lembaga untuk Pelatihan Lulus (Internal & Eksternal Gabungan)
        $pelatihanPerTipeLembaga = JumlahKepesertaanPelatihan::select('tipe_lembaga', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)
            ->where('status_kelulusan', 1)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('tipe_lembaga')
            ->get()
            ->map(function ($item) {
                $tipeText = (new JumlahKepesertaanPelatihan(['tipe_lembaga' => $item->tipe_lembaga]))->tipe_lembaga_text;
                return ['name' => $tipeText, 'value' => $item->total];
            });

        // 5. Komposisi Jenis LSP untuk Sertifikasi
        $sertifikasiPerJenisLsp = JumlahSertifikasiKompetensi::select('jenis_lsp', DB::raw('SUM(jumlah_sertifikasi) as total'))
            ->where('tahun', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('jenis_lsp')
            ->get()
            ->map(function ($item) {
                $lspText = (new JumlahSertifikasiKompetensi(['jenis_lsp' => $item->jenis_lsp]))->jenis_lsp_text;
                return ['name' => $lspText, 'value' => $item->total];
            });


        $availableYears = JumlahKepesertaanPelatihan::select('tahun')->distinct()
                            ->union(JumlahSertifikasiKompetensi::select('tahun')->distinct())
                            ->orderBy('tahun', 'desc')->pluck('tahun');
        
        return view('dashboards.binalavotas', compact(
            'totalLulusInternal',
            'totalLulusEksternal',
            'totalSertifikasi',
            'chartLabels', // Digunakan untuk semua chart tren bulanan
            'lulusInternalChartData',
            'lulusEksternalChartData',
            'sertifikasiChartData',
            'pelatihanPerTipeLembaga',
            'sertifikasiPerJenisLsp',
            'availableYears',
            'selectedYear',
            'selectedMonth'
        ));
    }
}
