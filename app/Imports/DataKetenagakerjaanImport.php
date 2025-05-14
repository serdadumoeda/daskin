<?php

namespace App\Imports;

use App\Models\DataKetenagakerjaan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class DataKetenagakerjaanImport implements ToModel, WithHeadingRow, WithValidation
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

    private function cleanNumeric($value) {
        if ($value === null || $value === '') return null;
        if (is_string($value)) {
            $cleaned = str_replace('.', '', $value); 
            $cleaned = str_replace(',', '.', $cleaned); 
            return is_numeric($cleaned) ? (float)$cleaned : null;
        }
        return is_numeric($value) ? (float)$value : null;
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
            Log::warning("Import DataKetenagakerjaan: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new DataKetenagakerjaan([
            'tahun'                       => $this->cleanNumeric($row['tahun'] ?? null),
            'bulan'                       => $bulan,
            'penduduk_15_atas'            => $this->cleanNumeric($row['penduduk_berumur_15_tahun_ke_atas'] ?? $row['penduduk_15_atas'] ?? null),
            'angkatan_kerja'              => $this->cleanNumeric($row['angkatan_kerja'] ?? null),
            'bukan_angkatan_kerja'        => $this->cleanNumeric($row['bukan_angkatan_kerja'] ?? null),
            'sekolah'                     => $this->cleanNumeric($row['sekolah'] ?? null),
            'mengurus_rumah_tangga'       => $this->cleanNumeric($row['mengurus_rumah_tangga'] ?? null),
            'lainnya_bak'                 => $this->cleanNumeric($row['lainnya_bak'] ?? $row['lainnya'] ?? null),
            'tpak'                        => $this->cleanNumeric($row['tingkat_partisipasi_angkatan_kerja_tpak'] ?? $row['tpak'] ?? null),
            'bekerja'                     => $this->cleanNumeric($row['bekerja'] ?? null),
            'pengangguran_terbuka'        => $this->cleanNumeric($row['pengangguran_terbuka'] ?? null),
            'tpt'                         => $this->cleanNumeric($row['tingkat_pengangguran_terbuka_tpt'] ?? $row['tpt'] ?? null),
            'tingkat_kesempatan_kerja'    => $this->cleanNumeric($row['tingkat_kesempatan_kerja'] ?? null),
        ]);
    }

    public function rules(): array
    {
        // Sesuaikan nama header dengan file Excel Anda
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.penduduk_berumur_15_tahun_ke_atas' => 'nullable', 
            '*.penduduk_15_atas' => 'nullable',
            '*.angkatan_kerja' => 'nullable',
            '*.bukan_angkatan_kerja' => 'nullable',
            '*.sekolah' => 'nullable',
            '*.mengurus_rumah_tangga' => 'nullable',
            '*.lainnya_bak' => 'nullable',
            '*.lainnya' => 'nullable',
            '*.tingkat_partisipasi_angkatan_kerja_tpak' => 'nullable',
            '*.tpak' => 'nullable',
            '*.bekerja' => 'nullable',
            '*.pengangguran_terbuka' => 'nullable',
            '*.tingkat_pengangguran_terbuka_tpt' => 'nullable',
            '*.tpt' => 'nullable',
            '*.tingkat_kesempatan_kerja' => 'nullable',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
