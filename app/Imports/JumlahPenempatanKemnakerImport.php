<?php

namespace App\Imports;

use App\Models\JumlahPenempatanKemnaker;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JumlahPenempatanKemnakerImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private function parseBulan($bulanInput)
    {
        if (empty($bulanInput)) return null;
        if (is_numeric($bulanInput) && $bulanInput >= 1 && $bulanInput <= 12) {
            return (int)$bulanInput;
        }
        if (is_string($bulanInput)) {
            $bulanMap = [
                'januari' => 1, 'jan' => 1, '1' => 1, 'februari' => 2, 'feb' => 2, '2' => 2,
                'maret' => 3, 'mar' => 3, '3' => 3, 'april' => 4, 'apr' => 4, '4' => 4,
                'mei' => 5, '5' => 5, 'juni' => 6, 'jun' => 6, '6' => 6,
                'juli' => 7, 'jul' => 7, '7' => 7, 'agustus' => 8, 'agu' => 8, 'ags' => 8, '8' => 8,
                'september' => 9, 'sep' => 9, '9' => 9, 'oktober' => 10, 'okt' => 10, '10' => 10,
                'november' => 11, 'nov' => 11, '11' => 11, 'desember' => 12, 'des' => 12, '12' => 12,
            ];
            return $bulanMap[strtolower(trim($bulanInput))] ?? null;
        }
        return null;
    }

    private function parseJenisKelamin($jkInput) {
        if (empty($jkInput)) return null;
        $jkInputLower = strtolower(trim($jkInput));
        if ($jkInputLower == 'laki-laki' || $jkInputLower == 'laki' || $jkInputLower == 'l' || $jkInput == '1') return 1;
        if ($jkInputLower == 'perempuan' || $jkInputLower == 'wanita' || $jkInputLower == 'p' || $jkInputLower == 'w' || $jkInput == '2') return 2;
        return null;
    }

    private function parseStatusDisabilitas($sdInput) {
        if (empty($sdInput)) return null; // Jika kosong, biarkan validasi yang menangani
        $sdInputLower = strtolower(trim($sdInput));
        if ($sdInputLower == 'ya' || $sdInputLower == 'disabilitas' || $sdInput == '1') return 1;
        if ($sdInputLower == 'tidak' || $sdInputLower == 'non disabilitas' || $sdInputLower == 'non-disabilitas' || $sdInput == '2') return 2;
        return null;
    }


    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $bulan = $this->parseBulan($row['bulan'] ?? null);
        if ($bulan === null && !empty($row['bulan'])) {
            Log::warning("Import JumlahPenempatanKemnaker: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $jenisKelamin = $this->parseJenisKelamin($row['jenis_kelamin'] ?? null);
        if ($jenisKelamin === null && !empty($row['jenis_kelamin'])) {
            Log::warning("Import JumlahPenempatanKemnaker: Jenis Kelamin '{$row['jenis_kelamin']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $statusDisabilitas = $this->parseStatusDisabilitas($row['status_disabilitas'] ?? null);
         if ($statusDisabilitas === null && !empty($row['status_disabilitas'])) {
            Log::warning("Import JumlahPenempatanKemnaker: Status Disabilitas '{$row['status_disabilitas']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        $ragamDisabilitas = $row['ragam_disabilitas'] ?? null;
        if ($statusDisabilitas == 2) { 
            $ragamDisabilitas = null;
        } else if ($statusDisabilitas == 1 && empty($ragamDisabilitas)) {
            // Biarkan validasi rules yang menangani jika ragam kosong saat status Ya
        }
        
        return new JumlahPenempatanKemnaker([
            'tahun'                 => $row['tahun'],
            'bulan'                 => $bulan,
            'jenis_kelamin'         => $jenisKelamin,
            'provinsi_domisili'     => $row['provinsi_domisili'] ?? null,
            'lapangan_usaha_kbli'   => $row['lapangan_usaha_kbli'] ?? $row['kbli'] ?? null,
            'status_disabilitas'    => $statusDisabilitas,
            'ragam_disabilitas'     => $ragamDisabilitas,
            'jumlah'                => (int)($row['jumlah'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.jenis_kelamin' => 'required', 
            '*.provinsi_domisili' => 'required|string|max:255',
            '*.lapangan_usaha_kbli' => 'nullable|string|max:255', 
            '*.kbli' => 'nullable|string|max:255', 
            '*.status_disabilitas' => 'required', 
            '*.ragam_disabilitas' => ['nullable', Rule::requiredIf(function () use (&$row) { // Perlu cara untuk akses $row di sini, atau validasi di model()
                // Ini akan sulit diimplementasikan langsung di rules() tanpa akses ke $row[$status_disabilitas]
                // Lebih baik validasi kondisional ini dilakukan di method model() atau dengan custom rule.
                // Untuk sementara, kita buat nullable dan validasi di model().
                return false; 
            }), 'string', 'max:255', Rule::in(array_keys(JumlahPenempatanKemnaker::getRagamDisabilitasOptions()))],
            '*.jumlah' => 'required|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.tahun.required' => 'Kolom tahun wajib diisi.',
            '*.bulan.required' => 'Kolom bulan wajib diisi atau formatnya tidak valid.',
            '*.jenis_kelamin.required' => 'Kolom jenis_kelamin wajib diisi atau formatnya tidak valid.',
            '*.provinsi_domisili.required' => 'Kolom provinsi_domisili wajib diisi.',
            '*.status_disabilitas.required' => 'Kolom status_disabilitas wajib diisi atau formatnya tidak valid.',
            '*.ragam_disabilitas.required_if' => 'Kolom ragam_disabilitas wajib diisi jika status disabilitas adalah Ya/1.',
            '*.ragam_disabilitas.in' => 'Nilai ragam_disabilitas tidak valid.',
            '*.jumlah.required' => 'Kolom jumlah wajib diisi.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
