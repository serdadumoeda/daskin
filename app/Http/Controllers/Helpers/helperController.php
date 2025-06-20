<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class helperController extends Controller
{
    public function totalSummary($typeCalc,$field, string $modelDb, $selectedYear, $selectedMonth){
        // 3. Jumlah Penempatan Tenaga Kerja Dalam Negeri (Binapenta)
        $totalCalc = 0;
        if($typeCalc == 'sum'){
            $totalCalc = $modelDb::query()
                ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
                ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
                ->sum($field);
        }elseif($typeCalc == 'avg'){
            $totalCalc = $modelDb::query()
                ->when($selectedYear, fn($q) => $q->where('tahun', $selectedYear))
                ->when($selectedMonth, fn($q) => $q->where('bulan', $selectedMonth))
                ->avg($field);
        }

        return $totalCalc;
    }
}
