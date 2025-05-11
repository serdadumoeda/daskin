<?php

namespace App\Imports;

use App\Models\MonevMonitoringMedia;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class MonevMonitoringMediaImport implements ToModel, WithHeadingRow, WithValidation
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
            Log::warning("Import MonevMonitoringMedia: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $jenisMedia = (int)($row['jenis_media'] ?? 0);
        if (!in_array($jenisMedia, [1, 2, 3])) {
            Log::warning("Import MonevMonitoringMedia: Jenis Media '{$jenisMedia}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $sentimenPublik = (int)($row['sentimen_publik'] ?? 0);
        if (!in_array($sentimenPublik, [1, 2])) {
            Log::warning("Import MonevMonitoringMedia: Sentimen Publik '{$sentimenPublik}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new MonevMonitoringMedia([
            'tahun'           => $row['tahun'],
            'bulan'           => $bulan,
            'jenis_media'     => $jenisMedia,
            'sentimen_publik' => $sentimenPublik,
            'jumlah_berita'   => (int)($row['jumlah_berita'] ?? $row['jumlah'] ?? 0), // Handle jika header 'jumlah' atau 'jumlah_berita'
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.jenis_media' => 'required|integer|in:1,2,3', // 1: Cetak, 2: Online, 3: Elektronik
            '*.sentimen_publik' => 'required|integer|in:1,2', // 1: Positif, 2: Negatif
            // Coba kedua kemungkinan nama header untuk jumlah
            '*.jumlah_berita' => 'nullable|integer|min:0',
            '*.jumlah' => 'nullable|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.tahun.required' => 'Kolom tahun wajib diisi.',
            '*.bulan.required' => 'Kolom bulan wajib diisi atau formatnya tidak valid.',
            '*.jenis_media.required' => 'Kolom jenis_media wajib diisi.',
            '*.jenis_media.in' => 'Jenis Media tidak valid (pilih 1, 2, atau 3).',
            '*.sentimen_publik.required' => 'Kolom sentimen_publik wajib diisi.',
            '*.sentimen_publik.in' => 'Sentimen Publik tidak valid (pilih 1 atau 2).',
            // Pesan untuk jumlah_berita atau jumlah
            '*.jumlah_berita.integer' => 'Kolom jumlah_berita harus berupa angka.',
            '*.jumlah.integer' => 'Kolom jumlah harus berupa angka.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
