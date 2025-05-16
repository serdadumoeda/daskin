<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JumlahPenempatanKemnaker;
use App\Models\JumlahLowonganPasker;
// HAPUS: use App\Models\JumlahTkaDisetujui;
use App\Models\PersetujuanRptka; // TAMBAHKAN: Model baru
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str; // Tambahkan ini jika belum ada

class BinapentaDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->input('year_filter', $currentYear);
        $selectedMonth = $request->input('month_filter'); 

        // --- Data untuk Kartu Ringkasan ---
        $totalPenempatanKemnaker = JumlahPenempatanKemnaker::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');

        $totalLowonganPasker = JumlahLowonganPasker::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_lowongan');
        
        // HAPUS Data TKA Lama
        // $totalTkaDisetujui = JumlahTkaDisetujui::query()
        //     ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        //     ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
        //     ->sum('jumlah_tka');
        // $totalTkaTidakDisetujui = 0; // Ini placeholder, akan dihapus

        // HAPUS Penempatan Disabilitas dari sini jika tidak ingin ditampilkan lagi sebagai kartu terpisah
        // $totalPenempatanDisabilitas = JumlahPenempatanKemnaker::query()
        //     ->where('status_disabilitas', 1) 
        //     ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        //     ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
        //     ->sum('jumlah');

        // TAMBAHKAN Data Persetujuan RPTKA Diterima
        $totalRptkaDiterima = PersetujuanRptka::query()
            ->where('status_pengajuan', 1) // Asumsi 1 = Diterima
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');


        // --- Data untuk Chart ---
        // 1. Tren Penempatan Kemnaker per Bulan (Tetap)
        $penempatanPerBulan = JumlahPenempatanKemnaker::select('bulan', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
            
        $penempatanChartLabels = [];
        $penempatanChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $penempatanChartLabels[] = Carbon::create()->month($m)->format('M');
            $penempatanChartDataValues[] = $penempatanPerBulan->get($m, 0); 
        }

        // 2. Tren Lowongan Pasker per Bulan (Tetap)
        $lowonganPaskerPerBulan = JumlahLowonganPasker::select('bulan', DB::raw('SUM(jumlah_lowongan) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $lowonganPaskerChartLabels = [];
        $lowonganPaskerChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $lowonganPaskerChartLabels[] = Carbon::create()->month($m)->format('M');
            $lowonganPaskerChartDataValues[] = $lowonganPaskerPerBulan->get($m, 0);
        }
        
        // HAPUS Chart TKA Disetujui Lama
        // $tkaDisetujuiPerBulan = JumlahTkaDisetujui::select('bulan', DB::raw('SUM(jumlah_tka) as total'))
        //     ->where('tahun', $selectedYear)
        //     ->groupBy('bulan')
        //     ->orderBy('bulan')
        //     ->pluck('total', 'bulan');
        // $tkaDisetujuiChartLabels = [];
        // $tkaDisetujuiChartDataValues = [];
        // for ($m=1; $m <= 12 ; $m++) { 
        //     $tkaDisetujuiChartLabels[] = Carbon::create()->month($m)->format('M');
        //     $tkaDisetujuiChartDataValues[] = $tkaDisetujuiPerBulan->get($m, 0);
        // }

        // TAMBAHKAN Chart Tren Persetujuan RPTKA (Diterima) per Bulan
        $rptkaDiterimaPerBulan = PersetujuanRptka::select('bulan', DB::raw('SUM(jumlah) as total'))
            ->where('status_pengajuan', 1) // Diterima
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $rptkaDiterimaChartLabels = [];
        $rptkaDiterimaChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $rptkaDiterimaChartLabels[] = Carbon::create()->month($m)->format('M');
            $rptkaDiterimaChartDataValues[] = $rptkaDiterimaPerBulan->get($m, 0);
        }


        // 4. Komposisi Penempatan Berdasarkan Jenis Kelamin (Tetap)
        $penempatanPerJenisKelamin = JumlahPenempatanKemnaker::select('jenis_kelamin', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('jenis_kelamin')
            ->get()
            ->map(function ($item) {
                // Asumsi model JumlahPenempatanKemnaker punya accessor jenis_kelamin_text
                $jkText = (new JumlahPenempatanKemnaker(['jenis_kelamin' => $item->jenis_kelamin]))->jenis_kelamin_text ?? 'N/A';
                return ['name' => $jkText, 'value' => $item->total];
            });
        
        // 5. Komposisi Lowongan Pasker Berdasarkan Lapangan Usaha (Tetap)
        $lowonganPerKbli = JumlahLowonganPasker::select('lapangan_usaha_kbli', DB::raw('SUM(jumlah_lowongan) as total'))
            ->where('tahun', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('lapangan_usaha_kbli')
            ->orderBy('total', 'desc')
            ->limit(5) 
            ->get()
            ->map(function ($item) {
                // Asumsi model JumlahLowonganPasker punya accessor lapangan_usaha_kbli_text
                // Jika tidak, tampilkan kode KBLI langsung atau deskripsi dari tabel KBLI jika ada relasi
                $kbliText = (new JumlahLowonganPasker(['lapangan_usaha_kbli' => $item->lapangan_usaha_kbli]))->lapangan_usaha_kbli_text ?? $item->lapangan_usaha_kbli;
                return ['name' => Str::limit($kbliText, 20), 'value' => $item->total];
            });


        $availableYears = PersetujuanRptka::select('tahun')->distinct() 
                            ->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYears->isEmpty() && !$availableYears->contains($currentYear)) {
            $availableYears->push($currentYear);
            $availableYears = $availableYears->sortDesc();
        }
        
        return view('dashboards.binapenta', compact(
            'totalPenempatanKemnaker',
            'totalLowonganPasker',
            // HAPUS 'totalTkaDisetujui',
            // HAPUS 'totalTkaTidakDisetujui',
            // HAPUS 'totalPenempatanDisabilitas',
            'totalRptkaDiterima', // TAMBAHKAN

            'penempatanChartLabels',
            'penempatanChartDataValues',
            'lowonganPaskerChartLabels',
            'lowonganPaskerChartDataValues',
            // HAPUS 'tkaDisetujuiChartLabels',
            // HAPUS 'tkaDisetujuiChartDataValues',
            'rptkaDiterimaChartLabels', // TAMBAHKAN
            'rptkaDiterimaChartDataValues', // TAMBAHKAN

            'penempatanPerJenisKelamin',
            'lowonganPerKbli',
            'availableYears',
            'selectedYear',
            'selectedMonth'
        ));
    }
}