<?php

namespace App\Imports;

use App\Models\PersentaseKehadiran;
use App\Models\UnitKerjaEselonI;
use App\Models\SatuanKerja;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class PersentaseKehadiranImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $unitKerjaEselonIKodes;
    private $satuanKerjaKodes;

    public function __construct()
    {
        $this->unitKerjaEselonIKodes = UnitKerjaEselonI::pluck('kode_uke1')->toArray();
        $this->satuanKerjaKodes = SatuanKerja::pluck('kode_sk')->toArray();
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $kodeUke1 = $row['kode_unit_kerja_eselon_i'] ?? null;
        if (empty($kodeUke1) || !in_array($kodeUke1, $this->unitKerjaEselonIKodes)) {
            Log::warning("Import PersentaseKehadiran: Kode Unit Kerja Eselon I '{$kodeUke1}' tidak valid atau tidak ditemukan. Baris dilewati: " . json_encode($row));
            return null; 
        }
        
        $kodeSk = $row['kode_satuan_kerja'] ?? null;
        if (empty($kodeSk) || !in_array($kodeSk, $this->satuanKerjaKodes)) {
            Log::warning("Import PersentaseKehadiran: Kode Satuan Kerja '{$kodeSk}' tidak valid atau tidak ditemukan. Baris dilewati: " . json_encode($row));
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
            Log::warning("Import PersentaseKehadiran: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $statusAsn = (int)($row['status_asn'] ?? 0);
        if (!in_array($statusAsn, [1, 2])) {
             Log::warning("Import PersentaseKehadiran: Status ASN '{$statusAsn}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        $statusKehadiran = (int)($row['status_kehadiran'] ?? 0);
        if ($statusKehadiran < 1 || $statusKehadiran > 6) {
             Log::warning("Import PersentaseKehadiran: Status Kehadiran '{$statusKehadiran}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        return new PersentaseKehadiran([
            'tahun'                       => $row['tahun'],
            'bulan'                       => $bulan,
            'kode_unit_kerja_eselon_i'    => $kodeUke1,
            'kode_satuan_kerja'           => $kodeSk,
            'status_asn'                  => $statusAsn,
            'status_kehadiran'            => $statusKehadiran,
            'jumlah_orang'                => (int)($row['jumlah_orang'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.kode_unit_kerja_eselon_i' => 'required|string',
            '*.kode_satuan_kerja' => 'required|string',
            '*.status_asn' => 'required|integer|in:1,2',
            '*.status_kehadiran' => 'required|integer|in:1,2,3,4,5,6',
            '*.jumlah_orang' => 'required|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.status_asn.in' => 'Status ASN tidak valid (pilih 1 untuk ASN, 2 untuk Non ASN).',
            '*.status_kehadiran.in' => 'Status Kehadiran tidak valid (pilih 1-6).',
            // Tambahkan pesan kustom lainnya jika perlu
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
