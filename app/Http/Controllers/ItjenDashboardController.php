<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Pastikan namespace dan nama model sudah benar
use App\Models\ProgressTemuanBpk;
use App\Models\ProgressTemuanInternal;
use Illuminate\Support\Facades\DB;
// Carbon tidak digunakan secara langsung, bisa dihapus jika tidak ada penggunaan lain

class ItjenDashboardController extends Controller
{
    // Fungsi helper untuk menghitung kumulatif jumlah
    private function calculateCumulativeSum(array $data): array
    {
        $cumulative = [];
        $sum = 0;
        foreach ($data as $value) {
            $sum += is_numeric($value) ? $value : 0;
            $cumulative[] = $sum;
        }
        return $cumulative;
    }

    // Fungsi helper untuk menghitung persentase penyelesaian kumulatif
    private function calculateCumulativePercentage(array $cumulativeTl, array $cumulativeTemuan): array
    {
        $percentage = [];
        for ($i = 0; $i < count($cumulativeTemuan); $i++) {
            if ($cumulativeTemuan[$i] > 0) {
                $percentage[] = round(($cumulativeTl[$i] / $cumulativeTemuan[$i]) * 100, 2);
            } else {
                $percentage[] = 0; // Jika tidak ada temuan, persentase 0 atau bisa juga 100 jika TL juga 0
            }
        }
        return $percentage;
    }

    // Fungsi helper generik untuk mengambil data bulanan (SUM)
    private function getMonthlyData($model, string $yearColumn, string $monthColumn, array $valueColumns, string $selectedYear, ?string $selectedMonth)
    {
        $query = $model::query()->where($yearColumn, $selectedYear);

        if ($selectedMonth) {
            $query->where($monthColumn, $selectedMonth);
            $data = [];
            foreach($valueColumns as $alias => $valueColumnName) {
                $data[$alias] = [(int)$query->sum($valueColumnName)];
            }
            return $data;
        } else {
            $selects = [$monthColumn];
            foreach($valueColumns as $alias => $valueColumnName) {
                $selects[] = DB::raw("SUM({$valueColumnName}) as {$alias}");
            }

            $monthlyDataGrouped = $query->select($selects)
                ->groupBy($monthColumn)
                ->orderBy($monthColumn, 'asc')
                ->get()
                ->keyBy($monthColumn);

            $result = [];
            foreach($valueColumns as $alias => $valueColumnName) {
                $monthlyValues = [];
                for ($m = 1; $m <= 12; $m++) {
                    $monthlyValues[] = (int)($monthlyDataGrouped->get($m)->$alias ?? 0);
                }
                $result[$alias] = $monthlyValues;
            }
            return $result;
        }
    }

    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->input('tahun', $currentYear);
        $selectedMonth = $request->input('bulan');

        // --- Data untuk Kartu Ringkasan ---
        $queryBpk = ProgressTemuanBpk::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));

        $summaryBpk = (clone $queryBpk)->select(
                DB::raw('COALESCE(SUM(temuan_administratif_kasus),0) as total_temuan_admin_kasus'),
                DB::raw('COALESCE(SUM(tindak_lanjut_administratif_kasus),0) as total_tl_admin_kasus')
            )->first();
        $persentaseSelesaiBpkAdmin = ($summaryBpk->total_temuan_admin_kasus > 0) ? round(($summaryBpk->total_tl_admin_kasus / $summaryBpk->total_temuan_admin_kasus) * 100, 2) : 0;

        $queryInternal = ProgressTemuanInternal::query()
            ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth));

        $summaryInternal = (clone $queryInternal)->select(
                DB::raw('COALESCE(SUM(temuan_administratif_kasus),0) as total_temuan_admin_kasus'),
                DB::raw('COALESCE(SUM(tindak_lanjut_administratif_kasus),0) as total_tl_admin_kasus')
            )->first();
        $persentaseSelesaiInternalAdmin = ($summaryInternal->total_temuan_admin_kasus > 0) ? round(($summaryInternal->total_tl_admin_kasus / $summaryInternal->total_temuan_admin_kasus) * 100, 2) : 0;

        // --- Logika Chart ---
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartLabels = $selectedMonth ? [$months[(int)$selectedMonth - 1]] : $months;

        // Data untuk Chart Temuan BPK
        $bpkValueColumns = [
            'temuan_admin_kasus' => 'temuan_administratif_kasus',
            'tl_admin_kasus' => 'tindak_lanjut_administratif_kasus',
        ];
        $bpkChartMonthlyData = $this->getMonthlyData(new ProgressTemuanBpk, 'tahun', 'bulan', $bpkValueColumns, $selectedYear, $selectedMonth);

        $bpkData = [
            'labels' => $chartLabels,
            'temuan_admin_kasus' => $bpkChartMonthlyData['temuan_admin_kasus'],
            'tl_admin_kasus' => $bpkChartMonthlyData['tl_admin_kasus'],
        ];
        $kumulatifTemuanBpk = $this->calculateCumulativeSum($bpkData['temuan_admin_kasus']);
        $kumulatifTlBpk = $this->calculateCumulativeSum($bpkData['tl_admin_kasus']);
        $bpkData['persentase_kumulatif'] = $this->calculateCumulativePercentage($kumulatifTlBpk, $kumulatifTemuanBpk);


        // Data untuk Chart Temuan Internal
        $internalValueColumns = [
            'temuan_admin_kasus' => 'temuan_administratif_kasus',
            'tl_admin_kasus' => 'tindak_lanjut_administratif_kasus',
        ];
        $internalChartMonthlyData = $this->getMonthlyData(new ProgressTemuanInternal, 'tahun', 'bulan', $internalValueColumns, $selectedYear, $selectedMonth);

        $internalData = [
            'labels' => $chartLabels,
            'temuan_admin_kasus' => $internalChartMonthlyData['temuan_admin_kasus'],
            'tl_admin_kasus' => $internalChartMonthlyData['tl_admin_kasus'],
        ];
        $kumulatifTemuanInternal = $this->calculateCumulativeSum($internalData['temuan_admin_kasus']);
        $kumulatifTlInternal = $this->calculateCumulativeSum($internalData['tl_admin_kasus']);
        $internalData['persentase_kumulatif'] = $this->calculateCumulativePercentage($kumulatifTlInternal, $kumulatifTemuanInternal);


        // Ambil tahun yang tersedia untuk filter
        $availableYears = ProgressTemuanBpk::select('tahun')->distinct()
                            ->union(ProgressTemuanInternal::select('tahun')->distinct())
                            ->orderBy('tahun', 'desc')->pluck('tahun');

        if ($availableYears->isEmpty() && !$availableYears->contains($currentYear)) {
            $availableYears->push($currentYear);
            $availableYears = $availableYears->sortDesc()->values();
        }

        $viewData = compact(
            'summaryBpk', 'persentaseSelesaiBpkAdmin',
            'summaryInternal', 'persentaseSelesaiInternalAdmin',
            'availableYears', 'selectedYear', 'selectedMonth'
        );

        // Menggabungkan semua data chart untuk dikirim ke view
        $allChartData = [
            'bpk' => $bpkData,
            'internal' => $internalData,
        ];

        return view('dashboards.itjen', array_merge($viewData, ['allChartData' => $allChartData]));
    }
}
