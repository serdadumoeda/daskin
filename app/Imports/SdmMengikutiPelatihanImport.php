<?php

namespace App\Imports;

use App\Models\SdmMengikutiPelatihan;
use App\Models\UnitKerjaEselonI;
use App\Models\SatuanKerja;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class SdmMengikutiPelatihanImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $unitKerjaEselonIKodes;
    private $satuanKerjaKodes;
    private $jenisPelatihanMap;

    public function __construct()
    {
        $this->unitKerjaEselonIKodes = UnitKerjaEselonI::pluck('kode_uke1')->toArray();
        $this->satuanKerjaKodes = SatuanKerja::pluck('kode_sk')->toArray();
        $this->jenisPelatihanMap = [
            'diklat dasar' => 1,
            'dasar' => 1,
            'diklat kepemimpinan' => 2,
            'kepemimpinan' => 2,
            'pim' => 2,
            'diklat fungsional' => 3,
            'fungsional' => 3,
        ];
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
            Log::warning("Import SdmMengikutiPelatihan: Kode Unit Kerja Eselon I '{$kodeUke1}' tidak valid atau tidak ditemukan. Baris dilewati: " . json_encode($row));
            return null; 
        }
        
        $kodeSk = $row['kode_satuan_kerja'] ?? null;
        if (empty($kodeSk) || !in_array($kodeSk, $this->satuanKerjaKodes)) {
            Log::warning("Import SdmMengikutiPelatihan: Kode Satuan Kerja '{$kodeSk}' tidak valid atau tidak ditemukan. Baris dilewati: " . json_encode($row));
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
            Log::warning("Import SdmMengikutiPelatihan: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $jenisPelatihanInput = strtolower(trim($row['jenis_pelatihan'] ?? ''));
        $jenisPelatihan = $this->jenisPelatihanMap[$jenisPelatihanInput] ?? (is_numeric($jenisPelatihanInput) && in_array((int)$jenisPelatihanInput, [1,2,3]) ? (int)$jenisPelatihanInput : null);

        if ($jenisPelatihan === null) {
            Log::warning("Import SdmMengikutiPelatihan: Jenis Pelatihan '{$row['jenis_pelatihan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new SdmMengikutiPelatihan([
            'tahun'                       => $row['tahun'],
            'bulan'                       => $bulan,
            'kode_unit_kerja_eselon_i'    => $kodeUke1,
            'kode_satuan_kerja'           => $kodeSk,
            'jenis_pelatihan'             => $jenisPelatihan,
            'jumlah_peserta'              => (int)($row['jumlah_peserta'] ?? $row['jumlah_peserta_pelatihan'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.kode_unit_kerja_eselon_i' => 'required|string',
            '*.kode_satuan_kerja' => 'required|string',
            '*.jenis_pelatihan' => 'required', // Validasi lebih lanjut di model()
            '*.jumlah_peserta' => 'nullable|integer|min:0',
            '*.jumlah_peserta_pelatihan' => 'nullable|integer|min:0', // Alternatif nama header
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.jenis_pelatihan.required' => 'Kolom jenis_pelatihan wajib diisi atau formatnya tidak dikenal.',
            // Tambahkan pesan kustom lainnya jika perlu
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
