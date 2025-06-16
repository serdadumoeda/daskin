<?php

namespace App\Imports;

use App\Models\PelaporanWlkpOnline;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PelaporanWlkpOnlineImport implements ToModel, WithHeadingRow, WithValidation
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
            Log::warning("Import PelaporanWlkpOnline: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        // Kunci perbaikan ada di sini: Pastikan menggunakan header yang benar dari Excel
        // dan mapping ke kolom database 'jumlah_perusahaan_melapor'
        $jumlahPerusahaan = (int)($row['jumlah_perusahaan_melapor'] ?? $row['jumlah'] ?? 0);

        return new PelaporanWlkpOnline([
            'tahun'                       => $row['tahun'],
            'bulan'                       => $bulan,
            'provinsi'                    => $row['provinsi'] ?? null,
            'jumlah_perusahaan_melapor'   => $jumlahPerusahaan, // Ini nama kolom di database
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.provinsi' => 'required|string|max:255',
            '*.jumlah_perusahaan_melapor' => 'exclude_if:*.jumlah,present|required_without:*.jumlah|integer|min:0',
            '*.jumlah' => 'exclude_if:*.jumlah_perusahaan_melapor,present|required_without:*.jumlah_perusahaan_melapor|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.jumlah_perusahaan_melapor.required_without' => 'Kolom jumlah_perusahaan_melapor atau jumlah wajib diisi.',
            '*.jumlah.required_without' => 'Kolom jumlah atau jumlah_perusahaan_melapor wajib diisi.',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}
