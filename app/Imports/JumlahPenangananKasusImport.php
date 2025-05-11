<?php

namespace App\Imports;

use App\Models\JumlahPenangananKasus;
use App\Models\SatuanKerja; // Untuk validasi kode_satuan_kerja
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class JumlahPenangananKasusImport implements ToModel, WithHeadingRow, WithValidation
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
            Log::warning("Import JumlahPenangananKasus: Kode Satuan Kerja '{$kodeSk}' tidak valid atau tidak ditemukan. Baris dilewati: " . json_encode($row));
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
            Log::warning("Import JumlahPenangananKasus: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new JumlahPenangananKasus([
            'tahun'             => $row['tahun'],
            'bulan'             => $bulan,
            'kode_satuan_kerja' => $kodeSk,
            'jenis_perkara'     => $row['jenis_perkara'] ?? null,
            'jumlah_perkara'    => (int)($row['jumlah_perkara'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.kode_satuan_kerja' => 'required|string',
            '*.jenis_perkara' => 'required|string|max:255',
            '*.jumlah_perkara' => 'required|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.tahun.required' => 'Kolom tahun wajib diisi.',
            '*.bulan.required' => 'Kolom bulan wajib diisi atau formatnya tidak valid.',
            '*.kode_satuan_kerja.required' => 'Kolom kode_satuan_kerja wajib diisi.',
            '*.jenis_perkara.required' => 'Kolom jenis_perkara wajib diisi.',
            '*.jumlah_perkara.required' => 'Kolom jumlah_perkara wajib diisi.',
            '*.jumlah_perkara.integer' => 'Kolom jumlah_perkara harus berupa angka.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
