<?php

namespace App\Imports;

use App\Models\JumlahLowonganPasker;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JumlahLowonganPaskerImport implements ToModel, WithHeadingRow, WithValidation
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

    private function parseJenisKelaminDibutuhkan($jkInput) {
        if (empty($jkInput)) return null;
        $jkInputLower = strtolower(trim($jkInput));
        if (str_contains($jkInputLower, 'laki-laki/perempuan') || str_contains($jkInputLower, 'l/p') || $jkInput == '3') return 3;
        if (str_contains($jkInputLower, 'laki-laki') || str_contains($jkInputLower, 'laki') || $jkInputLower == 'l' || $jkInput == '1') return 1;
        if (str_contains($jkInputLower, 'perempuan') || str_contains($jkInputLower, 'wanita') || $jkInputLower == 'p' || $jkInputLower == 'w' || $jkInput == '2') return 2;
        return null;
    }

    private function parseStatusDisabilitasDibutuhkan($sdInput) {
        if (empty($sdInput)) return null;
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
            Log::warning("Import JumlahLowonganPasker: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $jenisKelamin = $this->parseJenisKelaminDibutuhkan($row['jenis_kelamin_dibutuhkan'] ?? null);
        if ($jenisKelamin === null && !empty($row['jenis_kelamin_dibutuhkan'])) {
            Log::warning("Import JumlahLowonganPasker: Jenis Kelamin Dibutuhkan '{$row['jenis_kelamin_dibutuhkan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $statusDisabilitas = $this->parseStatusDisabilitasDibutuhkan($row['status_disabilitas_dibutuhkan'] ?? null);
         if ($statusDisabilitas === null && !empty($row['status_disabilitas_dibutuhkan'])) {
            Log::warning("Import JumlahLowonganPasker: Status Disabilitas Dibutuhkan '{$row['status_disabilitas_dibutuhkan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new JumlahLowonganPasker([
            'tahun'                         => $row['tahun'],
            'bulan'                         => $bulan,
            'provinsi_perusahaan'           => $row['provinsi_perusahaan'] ?? null,
            'lapangan_usaha_kbli'           => $row['lapangan_usaha_kbli'] ?? $row['kbli'] ?? null,
            'jabatan'                       => $row['jabatan'] ?? null,
            'jenis_kelamin_dibutuhkan'      => $jenisKelamin,
            'status_disabilitas_dibutuhkan' => $statusDisabilitas,
            'jumlah_lowongan'               => (int)($row['jumlah_lowongan'] ?? $row['jumlah'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.provinsi_perusahaan' => 'required|string|max:255',
            '*.lapangan_usaha_kbli' => 'nullable|string|max:255',
            '*.kbli' => 'nullable|string|max:255',
            '*.jabatan' => 'required|string|max:255',
            '*.jenis_kelamin_dibutuhkan' => 'required',
            '*.status_disabilitas_dibutuhkan' => 'required',
            '*.jumlah_lowongan' => 'exclude_if:*.jumlah,present|required_without:*.jumlah|integer|min:0',
            '*.jumlah' => 'exclude_if:*.jumlah_lowongan,present|required_without:*.jumlah_lowongan|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.jenis_kelamin_dibutuhkan.required' => 'Kolom jenis_kelamin_dibutuhkan wajib diisi atau formatnya tidak valid.',
            '*.status_disabilitas_dibutuhkan.required' => 'Kolom status_disabilitas_dibutuhkan wajib diisi atau formatnya tidak valid.',
            '*.jumlah_lowongan.required_without' => 'Kolom jumlah_lowongan atau jumlah wajib diisi.',
            '*.jumlah.required_without' => 'Kolom jumlah atau jumlah_lowongan wajib diisi.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
