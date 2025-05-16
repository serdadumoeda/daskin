<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JumlahPhk;
use App\Models\PerselisihanDitindaklanjuti;
use App\Models\MediasiBerhasil;
use App\Models\PerusahaanMenerapkanSusu;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhiDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->input('year_filter', $currentYear);
        $selectedMonth = $request->input('month_filter'); // Bisa null untuk semua bulan

        // --- Data untuk Kartu Ringkasan ---
        $totalTkPhk = JumlahPhk::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_tk_phk');
        $totalPerusahaanPhk = JumlahPhk::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_perusahaan_phk');

        $totalPerselisihanDitindaklanjuti = PerselisihanDitindaklanjuti::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_ditindaklanjuti');
        // totalPerselisihan mungkin tidak perlu ditampilkan di kartu jika judulnya sudah spesifik "yang ditindaklanjuti"
        // $totalPerselisihan = PerselisihanDitindaklanjuti::query()
        //     ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        //     ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
        //     ->sum('jumlah_perselisihan'); 
        
        $totalMediasiBerhasil = MediasiBerhasil::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_mediasi_berhasil');
        // $totalMediasi = MediasiBerhasil::query()
        //     ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
        //     ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
        //     ->sum('jumlah_mediasi');

        $totalPerusahaanSusu = PerusahaanMenerapkanSusu::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
            ->sum('jumlah_perusahaan_susu');

        // --- Data untuk Chart ---
        // 1. Tren PHK (Tenaga Kerja) per Bulan
        $phkPerBulan = JumlahPhk::select('bulan', DB::raw('SUM(jumlah_tk_phk) as total_tk_phk'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total_tk_phk', 'bulan');
            
        $phkChartLabels = [];
        $phkChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $phkChartLabels[] = Carbon::create()->month($m)->isoFormat('MMM');
            $phkChartDataValues[] = $phkPerBulan->get($m, 0); 
        }

        // 2. Tren Jumlah Perselisihan yang Ditindaklanjuti per Bulan (BARU)
        $perselisihanTlPerBulan = PerselisihanDitindaklanjuti::select('bulan', DB::raw('SUM(jumlah_ditindaklanjuti) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        
        $perselisihanTlChartLabels = []; // Akan sama dengan phkChartLabels
        $perselisihanTlChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $perselisihanTlChartLabels[] = Carbon::create()->month($m)->isoFormat('MMM');
            $perselisihanTlChartDataValues[] = $perselisihanTlPerBulan->get($m, 0);
        }
        
        // 3. Perbandingan Mediasi vs Mediasi Berhasil per Bulan
        $mediasiData = MediasiBerhasil::select(
                'bulan', 
                DB::raw('SUM(jumlah_mediasi) as total_mediasi'),
                DB::raw('SUM(jumlah_mediasi_berhasil) as total_berhasil')
            )
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $mediasiChartLabels = [];
        $mediasiTotalData = [];
        $mediasiBerhasilData = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $mediasiChartLabels[] = Carbon::create()->month($m)->isoFormat('MMM');
            $monthData = $mediasiData->firstWhere('bulan', $m);
            $mediasiTotalData[] = $monthData->total_mediasi ?? 0;
            $mediasiBerhasilData[] = $monthData->total_berhasil ?? 0;
        }

        // 4. Tren Perusahaan Menerapkan SUSU per Bulan
        $susuPerBulan = PerusahaanMenerapkanSusu::select('bulan', DB::raw('SUM(jumlah_perusahaan_susu) as total'))
            ->where('tahun', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
        $susuChartLabels = []; // Akan sama dengan phkChartLabels
        $susuChartDataValues = [];
        for ($m=1; $m <= 12 ; $m++) { 
            $susuChartLabels[] = Carbon::create()->month($m)->isoFormat('MMM');
            $susuChartDataValues[] = $susuPerBulan->get($m, 0);
        }

        $availableYears = JumlahPhk::select('tahun')->distinct()
                            ->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYears->isEmpty() && !$availableYears->contains($currentYear)) {
            $availableYears->push($currentYear);
            $availableYears = $availableYears->sortDesc();
        }
        
        // Opsi untuk filter jika ada, tapi tidak digunakan di dashboard ini berdasarkan permintaan
        // $jenisPerselisihanOptions = PerselisihanDitindaklanjuti::getJenisPerselisihanOptions();
        // $hasilMediasiOptions = MediasiBerhasil::getHasilMediasiOptions();

        return view('dashboards.phi', compact(
            'totalTkPhk',
            'totalPerusahaanPhk',
            'totalPerselisihanDitindaklanjuti',
            // 'totalPerselisihan', // Dihapus dari kartu
            'totalMediasiBerhasil',
            // 'totalMediasi', // Dihapus dari kartu
            'totalPerusahaanSusu',
            'phkChartLabels',
            'phkChartDataValues',
            // 'perselisihanPerJenis', // Dihapus, diganti tren
            'perselisihanTlChartLabels', // BARU
            'perselisihanTlChartDataValues', // BARU
            'mediasiChartLabels',
            'mediasiTotalData',
            'mediasiBerhasilData',
            'susuChartLabels',
            'susuChartDataValues',
            'availableYears',
            'selectedYear',
            'selectedMonth'
            // 'jenisPerselisihanOptions',
            // 'hasilMediasiOptions'
        ));
    }
}