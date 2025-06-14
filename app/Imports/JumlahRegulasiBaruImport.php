<?php

namespace App\Imports;

use App\Models\JumlahRegulasiBaru;
use App\Models\SatuanKerja; // Untuk validasi kode_satuan_kerja
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class JumlahRegulasiBaruImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $satuanKerjaKodes;

    public function __construct()
    {
        $this->satuanKerjaKodes = SatuanKerja::pluck('kode_sk')->toArray();
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $kodeSk = $row['kode_satuan_kerja'] ?? null;
        if (empty($kodeSk) || !in_array($kodeSk, $this->satuanKerjaKodes)) {
            Log::warning("Import JumlahRegulasiBaru: Kode Satuan Kerja '{$kodeSk}' tidak valid atau tidak ditemukan. Baris dilewati: " . json_encode($row));
            return null;
        }

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
            Log::warning("Import JumlahRegulasiBaru: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        $jenisRegulasi = (int)($row['jenis_regulasi'] ?? 0);
        if ($jenisRegulasi < 1 || $jenisRegulasi > 4) { 
            Log::warning("Import JumlahRegulasiBaru: Jenis Regulasi '{$jenisRegulasi}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        return new JumlahRegulasiBaru([
            'tahun'             => $row['tahun'],
            'bulan'             => $bulan,
            'kode_satuan_kerja' => $kodeSk,
            'jenis_regulasi'    => $jenisRegulasi,
            'jumlah_regulasi'   => (int)($row['jumlah_regulasi'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.kode_satuan_kerja' => 'required|string',
            '*.jenis_regulasi' => 'required|integer|min:1|max:4', 
            '*.jumlah_regulasi' => 'required|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.tahun.required' => 'Kolom tahun wajib diisi.',
            '*.bulan.required' => 'Kolom bulan wajib diisi atau formatnya tidak valid.',
            '*.kode_satuan_kerja.required' => 'Kolom kode_satuan_kerja wajib diisi.',
            '*.jenis_regulasi.required' => 'Kolom jenis_regulasi wajib diisi.',
            '*.jenis_regulasi.integer' => 'Kolom jenis_regulasi harus berupa angka (1-4).',
            '*.jenis_regulasi.min' => 'Kolom jenis_regulasi minimal 1.',
            '*.jenis_regulasi.max' => 'Kolom jenis_regulasi maksimal 4.',
            '*.jumlah_regulasi.required' => 'Kolom jumlah_regulasi wajib diisi.',
            '*.jumlah_regulasi.integer' => 'Kolom jumlah_regulasi harus berupa angka.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
