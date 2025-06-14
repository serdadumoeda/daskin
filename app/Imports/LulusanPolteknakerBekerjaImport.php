<?php

namespace App\Imports;

use App\Models\LulusanPolteknakerBekerja;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class LulusanPolteknakerBekerjaImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $bulanInput = $row['bulan'] ?? null;
        $bulan = null;
        if (is_numeric($bulanInput) && $bulanInput >= 1 && $bulanInput <= 12) {
            $bulan = (int)$bulanInput;
        } else if (is_string($bulanInput)) {
            $bulanMap = [
                'januari' => 1, 'jan' => 1, '1' => 1, 'februari' => 2, 'feb' => 2, '2' => 2,
                'maret' => 3, 'mar' => 3, '3' => 3, 'april' => 4, 'apr' => 4, '4' => 4,
                'mei' => 5, '5' => 5, 'juni' => 6, 'jun' => 6, '6' => 6,
                'juli' => 7, 'jul' => 7, '7' => 7, 'agustus' => 8, 'agu' => 8, 'ags' => 8, '8' => 8,
                'september' => 9, 'sep' => 9, '9' => 9, 'oktober' => 10, 'okt' => 10, '10' => 10,
                'november' => 11, 'nov' => 11, '11' => 11, 'desember' => 12, 'des' => 12, '12' => 12,
            ];
            $bulan = $bulanMap[strtolower(trim($bulanInput))] ?? null;
        }
        
        if ($bulan === null && !empty($bulanInput)) {
            Log::warning("Import LulusanPolteknakerBekerja: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $programStudi = (int)($row['program_studi'] ?? 0);
        if (!in_array($programStudi, [1, 2, 3])) { // 1: Relasi Industri, 2: K3, 3: MSDM
            Log::warning("Import LulusanPolteknakerBekerja: Program Studi '{$programStudi}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new LulusanPolteknakerBekerja([
            'tahun'                     => $row['tahun'],
            'bulan'                     => $bulan,
            'program_studi'             => $programStudi,
            'jumlah_lulusan'            => (int)($row['jumlah_lulusan'] ?? 0),
            'jumlah_lulusan_bekerja'    => (int)($row['jumlah_lulusan_bekerja'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.program_studi' => 'required|integer|in:1,2,3',
            '*.jumlah_lulusan' => 'required|integer|min:0',
            '*.jumlah_lulusan_bekerja' => 'required|integer|min:0|lte:*.jumlah_lulusan',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.tahun.required' => 'Kolom tahun wajib diisi.',
            '*.bulan.required' => 'Kolom bulan wajib diisi atau formatnya tidak valid.',
            '*.program_studi.required' => 'Kolom program_studi wajib diisi.',
            '*.program_studi.in' => 'Program Studi tidak valid (pilih 1, 2, atau 3).',
            '*.jumlah_lulusan.required' => 'Kolom jumlah_lulusan wajib diisi.',
            '*.jumlah_lulusan_bekerja.required' => 'Kolom jumlah_lulusan_bekerja wajib diisi.',
            '*.jumlah_lulusan_bekerja.lte' => 'Jumlah lulusan bekerja tidak boleh melebihi jumlah lulusan.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
