<?php

namespace App\Imports;

use App\Models\PerusahaanMenerapkanSusu;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class PerusahaanMenerapkanSusuImport implements ToModel, WithHeadingRow, WithValidation
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
            Log::warning("Import PerusahaanMenerapkanSusu: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        // Sesuaikan nama header dari Excel jika berbeda, contoh: 'jumlah_perusahaan'
        $jumlahPerusahaan = (int)($row['jumlah_perusahaan_susu'] ?? $row['jumlah_perusahaan'] ?? $row['jumlah'] ?? 0);

        return new PerusahaanMenerapkanSusu([
            'tahun'                     => $row['tahun'],
            'bulan'                     => $bulan,
            'provinsi'                  => $row['provinsi'] ?? null,
            'kbli'                      => $row['kbli'] ?? null,
            'jumlah_perusahaan_susu'    => $jumlahPerusahaan,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.provinsi' => 'required|string|max:255',
            '*.kbli' => 'required|string|max:50',
            // Validasi untuk kolom jumlah, bisa salah satu dari beberapa nama header Excel
            '*.jumlah_perusahaan_susu' => 'exclude_if:*.jumlah_perusahaan,present|exclude_if:*.jumlah,present|required_without_all:*.jumlah_perusahaan,*.jumlah|integer|min:0',
            '*.jumlah_perusahaan' => 'exclude_if:*.jumlah_perusahaan_susu,present|exclude_if:*.jumlah,present|required_without_all:*.jumlah_perusahaan_susu,*.jumlah|integer|min:0',
            '*.jumlah' => 'exclude_if:*.jumlah_perusahaan_susu,present|exclude_if:*.jumlah_perusahaan,present|required_without_all:*.jumlah_perusahaan_susu,*.jumlah_perusahaan|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.tahun.required' => 'Kolom tahun wajib diisi.',
            '*.bulan.required' => 'Kolom bulan wajib diisi atau formatnya tidak valid.',
            '*.provinsi.required' => 'Kolom provinsi wajib diisi.',
            '*.kbli.required' => 'Kolom KBLI wajib diisi.',
            '*.jumlah_perusahaan_susu.required_without_all' => 'Kolom jumlah perusahaan (jumlah_perusahaan_susu, jumlah_perusahaan, atau jumlah) wajib diisi.',
            '*.jumlah_perusahaan.required_without_all' => 'Kolom jumlah perusahaan (jumlah_perusahaan_susu, jumlah_perusahaan, atau jumlah) wajib diisi.',
            '*.jumlah.required_without_all' => 'Kolom jumlah perusahaan (jumlah_perusahaan_susu, jumlah_perusahaan, atau jumlah) wajib diisi.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
