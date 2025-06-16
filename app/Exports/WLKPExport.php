<?php

namespace App\Exports;

use App\Models\PelaporanWlkpOnline;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WLKPExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return ['tahun', 'bulan', 'provinsi', 'jumlah_perusahaan_melapor'];
    }
}
