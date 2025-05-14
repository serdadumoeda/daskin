<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JumlahPenempatanKemnaker;
use App\Models\JumlahLowonganPasker;
use App\Models\JumlahTkaDisetujui;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BinapentaDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->input('year_filter', $currentYear);
        $selectedMonth = $request->input('month_filter'); // Bisa null untuk semua bulan

        // --- Data untuk Kartu Ringkasan ---
        $totalPenempatanKemnaker = JumlahPenempatanKemnaker::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');

        $totalLowonganPasker = JumlahLowonganPasker::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_lowongan');
        
        $totalTkaDisetujui = JumlahTkaDisetujui::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_tka');
        
        // Placeholder untuk TKA Tidak Disetujui
        $totalTkaTidakDisetujui = 0; // Ganti dengan query jika tabel sudah ada

        $totalPenempatanDisabilitas = JumlahPenempatanKemnaker::query()
            ->where('status_disabilitas', 1) // 1 = Ya (Disabilitas)
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah');

        // --- Data untuk Chart ---
        // 1. Tren Penempatan Kemnaker per Bulan
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

        // 2. Tren Lowongan Pasker per Bulan
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
        
        // 3. Tren TKA Disetujui per Bulan
        $tkaDisetujuiPerBulan = JumlahTkaDisetujui::select('bulan', DB::raw('SUM(jumlah_tka) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $tkaDisetujuiChartLabels = [];
        $tkaDisetujuiChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $tkaDisetujuiChartLabels[] = Carbon::create()->month($m)->format('M');
            $tkaDisetujuiChartDataValues[] = $tkaDisetujuiPerBulan->get($m, 0);
        }

        // 4. Komposisi Penempatan Berdasarkan Jenis Kelamin (Pie Chart)
        $penempatanPerJenisKelamin = JumlahPenempatanKemnaker::select('jenis_kelamin', DB::raw('SUM(jumlah) as total'))
            ->where('tahun', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('jenis_kelamin')
            ->get()
            ->map(function ($item) {
                $jkText = (new JumlahPenempatanKemnaker(['jenis_kelamin' => $item->jenis_kelamin]))->jenis_kelamin_text;
                return ['name' => $jkText, 'value' => $item->total];
            });
        
        // 5. Komposisi Lowongan Pasker Berdasarkan Lapangan Usaha (Top 5 KBLI)
        $lowonganPerKbli = JumlahLowonganPasker::select('lapangan_usaha_kbli', DB::raw('SUM(jumlah_lowongan) as total'))
            ->where('tahun', $selectedYear)
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->groupBy('lapangan_usaha_kbli')
            ->orderBy('total', 'desc')
            ->limit(5) // Ambil top 5
            ->get()
            ->map(function ($item) {
                return ['name' => Str::limit($item->lapangan_usaha_kbli, 20), 'value' => $item->total]; // Limit text length for chart
            });


        $availableYears = JumlahPenempatanKemnaker::select('tahun')->distinct() 
                            ->orderBy('tahun', 'desc')->pluck('tahun');
        
        return view('dashboards.binapenta', compact(
            'totalPenempatanKemnaker',
            'totalLowonganPasker',
            'totalTkaDisetujui',
            'totalTkaTidakDisetujui',
            'totalPenempatanDisabilitas',
            'penempatanChartLabels',
            'penempatanChartDataValues',
            'lowonganPaskerChartLabels',
            'lowonganPaskerChartDataValues',
            'tkaDisetujuiChartLabels',
            'tkaDisetujuiChartDataValues',
            'penempatanPerJenisKelamin',
            'lowonganPerKbli',
            'availableYears',
            'selectedYear',
            'selectedMonth'
        ));
    }
}
