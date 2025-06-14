<?php

namespace App\Imports;

use App\Models\JumlahPhk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class JumlahPhkImport implements ToModel, WithHeadingRow, WithValidation
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
            Log::warning("Import JumlahPHK: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        // Sesuaikan nama header dari Excel jika berbeda
        $jumlahPerusahaan = (int)($row['jumlah_perusahaan_phk'] ?? $row['jumlah_perusahaan'] ?? 0);
        $jumlahTkPhk = (int)($row['jumlah_tk_phk'] ?? $row['jumlah_tenaga_kerja_yang_di_phk'] ?? 0);

        return new JumlahPhk([
            'tahun'                   => $row['tahun'],
            'bulan'                   => $bulan,
            'provinsi'                => $row['provinsi'] ?? null,
            'jumlah_perusahaan_phk'   => $jumlahPerusahaan,
            'jumlah_tk_phk'           => $jumlahTkPhk,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.provinsi' => 'required|string|max:255',
            // Validasi untuk kolom jumlah, bisa salah satu dari dua nama header Excel
            '*.jumlah_perusahaan_phk' => 'exclude_if:*.jumlah_perusahaan,present|required_without:*.jumlah_perusahaan|integer|min:0',
            '*.jumlah_perusahaan' => 'exclude_if:*.jumlah_perusahaan_phk,present|required_without:*.jumlah_perusahaan_phk|integer|min:0',
            
            '*.jumlah_tk_phk' => 'exclude_if:*.jumlah_tenaga_kerja_yang_di_phk,present|required_without:*.jumlah_tenaga_kerja_yang_di_phk|integer|min:0',
            '*.jumlah_tenaga_kerja_yang_di_phk' => 'exclude_if:*.jumlah_tk_phk,present|required_without:*.jumlah_tk_phk|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.tahun.required' => 'Kolom tahun wajib diisi.',
            '*.bulan.required' => 'Kolom bulan wajib diisi atau formatnya tidak valid.',
            '*.provinsi.required' => 'Kolom provinsi wajib diisi.',
            '*.jumlah_perusahaan_phk.required_without' => 'Kolom jumlah_perusahaan_phk atau jumlah_perusahaan wajib diisi.',
            '*.jumlah_perusahaan.required_without' => 'Kolom jumlah_perusahaan atau jumlah_perusahaan_phk wajib diisi.',
            '*.jumlah_tk_phk.required_without' => 'Kolom jumlah_tk_phk atau jumlah_tenaga_kerja_yang_di_phk wajib diisi.',
            '*.jumlah_tenaga_kerja_yang_di_phk.required_without' => 'Kolom jumlah_tenaga_kerja_yang_di_phk atau jumlah_tk_phk wajib diisi.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
