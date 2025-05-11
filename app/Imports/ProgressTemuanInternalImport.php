<?php

namespace App\Imports;

use App\Models\ProgressTemuanInternal; // Model yang benar
use App\Models\UnitKerjaEselonI;
use App\Models\SatuanKerja;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ProgressTemuanInternalImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $unitKerjaEselonIKodes;
    private $satuanKerjaKodes;

    public function __construct()
    {
        // Mengambil kode yang valid untuk validasi 'exists' secara manual
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
        // Validasi keberadaan kode_unit_kerja_eselon_i
        // Pastikan header Excel 'kode_unit_kerja_eselon_i' ada dan tidak null
        $kodeUke1 = $row['kode_unit_kerja_eselon_i'] ?? null;
        if (empty($kodeUke1) || !in_array($kodeUke1, $this->unitKerjaEselonIKodes)) {
            Log::warning("Import ProgressTemuanInternal: Kode Unit Kerja Eselon I '{$kodeUke1}' tidak valid atau tidak ditemukan. Baris dilewati: " . json_encode($row));
            return null; 
        }
        
        // Validasi keberadaan kode_satuan_kerja
        // Pastikan header Excel 'kode_satuan_kerja' ada dan tidak null
        $kodeSk = $row['kode_satuan_kerja'] ?? null;
        if (empty($kodeSk) || !in_array($kodeSk, $this->satuanKerjaKodes)) {
            Log::warning("Import ProgressTemuanInternal: Kode Satuan Kerja '{$kodeSk}' tidak valid atau tidak ditemukan. Baris dilewati: " . json_encode($row));
            return null;
        }

        $temuanAdminKasus = (int)($row['temuan_administratif_kasus'] ?? 0);
        $tindakLanjutAdminKasus = (int)($row['tindak_lanjut_administratif_kasus'] ?? 0);
        $temuanKerugianRp = (float)($row['temuan_kerugian_negara_rp'] ?? 0);
        $tindakLanjutKerugianRp = (float)($row['tindak_lanjut_kerugian_negara_rp'] ?? 0);

        $persentaseAdmin = ($temuanAdminKasus > 0)
            ? round(($tindakLanjutAdminKasus / $temuanAdminKasus) * 100, 2)
            : 0;
        $persentaseKerugian = ($temuanKerugianRp > 0)
            ? round(($tindakLanjutKerugianRp / $temuanKerugianRp) * 100, 2)
            : 0;
            
        $bulanInput = $row['bulan'] ?? null;
        $bulan = null;
        if (is_numeric($bulanInput) && $bulanInput >= 1 && $bulanInput <= 12) {
            $bulan = (int)$bulanInput;
        } else if (is_string($bulanInput)) {
            $bulanMap = [
                'januari' => 1, 'jan' => 1,
                'februari' => 2, 'feb' => 2,
                'maret' => 3, 'mar' => 3,
                'april' => 4, 'apr' => 4,
                'mei' => 5,
                'juni' => 6, 'jun' => 6,
                'juli' => 7, 'jul' => 7,
                'agustus' => 8, 'agu' => 8, 'ags' => 8,
                'september' => 9, 'sep' => 9,
                'oktober' => 10, 'okt' => 10,
                'november' => 11, 'nov' => 11,
                'desember' => 12, 'des' => 12,
            ];
            $bulan = $bulanMap[strtolower(trim($bulanInput))] ?? null;
        }
        
        if ($bulan === null) {
            Log::warning("Import ProgressTemuanInternal: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        return new ProgressTemuanInternal([
            'tahun'                                 => $row['tahun'],
            'bulan'                                 => $bulan,
            'kode_unit_kerja_eselon_i'              => $kodeUke1,
            'kode_satuan_kerja'                     => $kodeSk,
            'temuan_administratif_kasus'            => $temuanAdminKasus,
            'temuan_kerugian_negara_rp'             => $temuanKerugianRp,
            'tindak_lanjut_administratif_kasus'     => $tindakLanjutAdminKasus,
            'tindak_lanjut_kerugian_negara_rp'      => $tindakLanjutKerugianRp,
            'persentase_tindak_lanjut_administratif'=> $persentaseAdmin,
            'persentase_tindak_lanjut_kerugian_negara' => $persentaseKerugian,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required', 
            '*.kode_unit_kerja_eselon_i' => 'required|string',
            '*.kode_satuan_kerja' => 'required|string',
            '*.temuan_administratif_kasus' => 'required|integer|min:0',
            '*.temuan_kerugian_negara_rp' => 'required|numeric|min:0',
            '*.tindak_lanjut_administratif_kasus' => 'required|integer|min:0|lte:*.temuan_administratif_kasus',
            '*.tindak_lanjut_kerugian_negara_rp' => 'required|numeric|min:0|lte:*.temuan_kerugian_negara_rp',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.tahun.required' => 'Kolom tahun wajib diisi untuk setiap baris.',
            '*.bulan.required' => 'Kolom bulan wajib diisi atau formatnya tidak valid untuk setiap baris.',
            '*.kode_unit_kerja_eselon_i.required' => 'Kolom kode_unit_kerja_eselon_i wajib diisi.',
            '*.kode_satuan_kerja.required' => 'Kolom kode_satuan_kerja wajib diisi.',
            '*.temuan_administratif_kasus.required' => 'Kolom temuan_administratif_kasus wajib diisi.',
            '*.temuan_administratif_kasus.integer' => 'Kolom temuan_administratif_kasus harus berupa angka.',
            '*.temuan_administratif_kasus.min' => 'Kolom temuan_administratif_kasus minimal 0.',
            '*.tindak_lanjut_administratif_kasus.lte' => 'Tindak lanjut kasus admin tidak boleh melebihi temuan kasus admin.',
            '*.tindak_lanjut_kerugian_negara_rp.lte' => 'Tindak lanjut kerugian negara tidak boleh melebihi temuan kerugian negara.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
