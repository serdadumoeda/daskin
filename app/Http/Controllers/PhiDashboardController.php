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
        $totalTkPhk = JumlahPhk::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_tk_phk');
        $totalPerusahaanPhk = JumlahPhk::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perusahaan_phk');
        $totalPerselisihanDitindaklanjuti = PerselisihanDitindaklanjuti::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perselisihan');
        $totalMediasiBerhasil = MediasiBerhasil::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_mediasi_berhasil');
        $totalPerusahaanSusu = PerusahaanMenerapkanSusu::query()->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))->sum('jumlah_perusahaan_susu');

        // --- Logika untuk Chart Dinamis ---
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        if ($selectedMonth) {
            // === JIKA BULAN DIPILIH ===
            $chartLabels = [$months[$selectedMonth - 1]];
            
            $getSingleMonthData = function ($model, $columns, $year, $month) {
                $result = [];
                foreach ($columns as $alias => $column) {
                    // Pastikan $column adalah string nama kolom, bukan array
                    $actualColumn = is_array($column) ? ($column['column'] ?? null) : $column;
                    if ($actualColumn) {
                        $query = $model::query()->where('tahun', $year)->where('bulan', $month);
                         if (is_array($column) && isset($column['where'])) {
                            $query->where($column['where'][0], $column['where'][1]);
                        }
                        $result[$alias] = [$query->sum($actualColumn)];
                    } else {
                        $result[$alias] = [0]; // Default jika kolom tidak valid
                    }
                }
                return $result;
            };

            // Data PHK
            $phkData = $getSingleMonthData(new JumlahPhk, ['tk' => 'jumlah_tk_phk', 'perusahaan' => 'jumlah_perusahaan_phk'], $selectedYear, $selectedMonth);
            // Data Perselisihan
            $perselisihanData = $getSingleMonthData(new PerselisihanDitindaklanjuti, ['ditindaklanjuti' => 'jumlah_perselisihan', 'total_perselisihan' => 'jumlah_perselisihan'], $selectedYear, $selectedMonth);
            // Data Mediasi
            $mediasiData = $getSingleMonthData(new MediasiBerhasil, ['total' => 'jumlah_mediasi', 'berhasil' => 'jumlah_mediasi_berhasil'], $selectedYear, $selectedMonth);
            // Data SUSU
            $susuData = $getSingleMonthData(new PerusahaanMenerapkanSusu, ['susu' => 'jumlah_perusahaan_susu'], $selectedYear, $selectedMonth);

        } else {
            // === JIKA SEMUA BULAN (TAHUNAN) ===
            $chartLabels = $months;
            $getMonthlyData = function ($model, $columnConfigs, $year) { // columnConfigs adalah array asosiatif [alias => config_array]
                $data = $model::query()->where('tahun', $year)->select('bulan', DB::raw(
                    collect($columnConfigs)->map(function ($config, $alias) {
                        $actualColumn = is_array($config) ? ($config['column'] ?? null) : $config;
                        if (!$actualColumn) return "0 as {$alias}"; // Default jika kolom tidak valid
                        $condition = "1=1";
                        if (is_array($config) && isset($config['where'])) {
                            $condition = "{$config['where'][0]} = '{$config['where'][1]}'";
                        }
                        return "SUM(CASE WHEN {$condition} THEN {$actualColumn} ELSE 0 END) as {$alias}";
                    })->implode(', ')
                ))->groupBy('bulan')->orderBy('bulan', 'asc')->get()->keyBy('bulan');
            
                $result = [];
                foreach ($columnConfigs as $alias => $config) {
                    for ($m = 1; $m <= 12; $m++) {
                        $result[$alias][$m-1] = $data->get($m)->$alias ?? 0;
                    }
                }
                return $result;
            };
            

            // Data PHK
            $phkData = $getMonthlyData(new JumlahPhk, ['tk' => 'jumlah_tk_phk', 'perusahaan' => 'jumlah_perusahaan_phk'], $selectedYear);
            // Data Perselisihan
            $perselisihanData = $getMonthlyData(new PerselisihanDitindaklanjuti, ['ditindaklanjuti' => 'jumlah_perselisihan', 'total_perselisihan' => 'jumlah_perselisihan'], $selectedYear);
            // Data Mediasi
            $mediasiData = $getMonthlyData(new MediasiBerhasil, ['total' => 'jumlah_mediasi', 'berhasil' => 'jumlah_mediasi_berhasil'], $selectedYear);
            // Data SUSU
            $susuData = $getMonthlyData(new PerusahaanMenerapkanSusu, ['susu' => 'jumlah_perusahaan_susu'], $selectedYear);
        }

        $availableYears = JumlahPhk::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYears->isEmpty() && !$availableYears->contains($currentYear)) {
            $availableYears->push($currentYear);
        }
        
        $viewData = compact('totalTkPhk', 'totalPerusahaanPhk', 'totalPerselisihanDitindaklanjuti', 'totalMediasiBerhasil', 'totalPerusahaanSusu', 'availableYears', 'selectedYear', 'selectedMonth', 'chartLabels');
        
        $chartData = [
            'phk' => [
                'tk' => $phkData['tk'],
                'perusahaan' => $phkData['perusahaan'],
                'kumulatif' => $this->calculateCumulative($phkData['tk'])
            ],
            'perselisihan' => [
                'ditindaklanjuti' => $perselisihanData['ditindaklanjuti'],
                'total_perselisihan' => $perselisihanData['total_perselisihan'], // Data baru, akan identik dengan 'ditindaklanjuti'
                'kumulatif' => $this->calculateCumulative($perselisihanData['total_perselisihan']) // Kumulatif berdasarkan total
            ],
            'mediasi' => [
                'total' => $mediasiData['total'],
                'berhasil' => $mediasiData['berhasil'],
                'kumulatif' => $this->calculateCumulative($mediasiData['berhasil'])
            ],
            'susu' => [
                'susu' => $susuData['susu'],
                'kumulatif' => $this->calculateCumulative($susuData['susu'])
            ],
        ];
        
        return view('dashboards.phi', array_merge($viewData, ['chartData' => $chartData]));
    }
}