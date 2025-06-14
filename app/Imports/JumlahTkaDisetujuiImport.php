<?php

namespace App\Imports;

use App\Models\JumlahTkaDisetujui;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class JumlahTkaDisetujuiImport implements ToModel, WithHeadingRow, WithValidation
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

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $bulan = $this->parseBulan($row['bulan'] ?? null);
        if ($bulan === null && !empty($row['bulan'])) {
            Log::warning("Import JumlahTkaDisetujui: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $jenisKelamin = $this->parseJenisKelamin($row['jenis_kelamin'] ?? null);
        if ($jenisKelamin === null && !empty($row['jenis_kelamin'])) {
            Log::warning("Import JumlahTkaDisetujui: Jenis Kelamin '{$row['jenis_kelamin']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        // Jika di Excel ada kolom 'status_pengajuan_rptka', dan Anda hanya ingin impor yang 'Diterima'
        // $statusPengajuan = strtolower(trim($row['status_pengajuan_rptka'] ?? ''));
        // if ($statusPengajuan !== 'diterima' && $statusPengajuan !== '1') {
        //     Log::info("Import JumlahTkaDisetujui: Status RPTKA bukan 'Diterima', baris dilewati: " . json_encode($row));
        //     return null; 
        // }

        return new JumlahTkaDisetujui([
            'tahun'                 => $row['tahun'],
            'bulan'                 => $bulan,
            'jenis_kelamin'         => $jenisKelamin,
            'negara_asal'           => $row['negara_asal'] ?? null,
            'jabatan'               => $row['jabatan'] ?? null,
            'lapangan_usaha_kbli'   => $row['lapangan_usaha_kbli'] ?? $row['kbli'] ?? null,
            'provinsi_penempatan'   => $row['provinsi_penempatan'] ?? null,
            'jumlah_tka'            => (int)($row['jumlah_tka_disetujui'] ?? $row['jumlah'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.jenis_kelamin' => 'required', 
            '*.negara_asal' => 'required|string|max:255',
            '*.jabatan' => 'required|string|max:255',
            '*.lapangan_usaha_kbli' => 'nullable|string|max:255',
            '*.kbli' => 'nullable|string|max:255',
            '*.provinsi_penempatan' => 'required|string|max:255',
            // '*.status_pengajuan_rptka' => 'required|string', // Jika ada di Excel
            '*.jumlah_tka_disetujui' => 'exclude_if:*.jumlah,present|required_without:*.jumlah|integer|min:0',
            '*.jumlah' => 'exclude_if:*.jumlah_tka_disetujui,present|required_without:*.jumlah_tka_disetujui|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.jenis_kelamin.required' => 'Kolom jenis_kelamin wajib diisi atau formatnya tidak valid.',
            '*.jumlah_tka_disetujui.required_without' => 'Kolom jumlah_tka_disetujui atau jumlah wajib diisi.',
            '*.jumlah.required_without' => 'Kolom jumlah atau jumlah_tka_disetujui wajib diisi.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
