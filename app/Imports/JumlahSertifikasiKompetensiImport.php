<?php

namespace App\Imports;

use App\Models\JumlahSertifikasiKompetensi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JumlahSertifikasiKompetensiImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $jenisLspTextToKey;
    private $jenisKelaminTextToKey;

    public function __construct()
    {
        $this->jenisLspTextToKey = array_change_key_case(array_flip(JumlahSertifikasiKompetensi::getJenisLspOptions()), CASE_LOWER);
        // Tambahkan juga pemetaan dari key ke key (untuk kasus jika input sudah berupa key/angka)
        foreach (JumlahSertifikasiKompetensi::getJenisLspOptions() as $key => $value) {
            $this->jenisLspTextToKey[strtolower((string)$key)] = $key;
        }
        
        $this->jenisKelaminTextToKey = array_change_key_case(array_flip(JumlahSertifikasiKompetensi::getJenisKelaminOptions()), CASE_LOWER);
         foreach (JumlahSertifikasiKompetensi::getJenisKelaminOptions() as $key => $value) {
            $this->jenisKelaminTextToKey[strtolower((string)$key)] = $key;
            if(strtolower($value) == 'laki') $this->jenisKelaminTextToKey['laki'] = $key; // Alias
            if(strtolower($value) == 'pria') $this->jenisKelaminTextToKey['pria'] = $key; // Alias
            if(strtolower($value) == 'wanita') $this->jenisKelaminTextToKey['wanita'] = $key; // Alias
        }

    }

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

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $bulan = $this->parseBulan($row['bulan'] ?? null);
        if ($bulan === null && !empty($row['bulan'])) {
            Log::warning("Import JumlahSertifikasiKompetensi: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $jenisLspInput = strtolower(trim($row['jenis_lsp'] ?? ''));
        $jenisLsp = $this->jenisLspTextToKey[$jenisLspInput] ?? null;
        if ($jenisLsp === null && !empty($row['jenis_lsp'])) {
            Log::warning("Import JumlahSertifikasiKompetensi: Jenis LSP '{$row['jenis_lsp']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $jenisKelaminInput = strtolower(trim($row['jenis_kelamin'] ?? ''));
        $jenisKelamin = $this->jenisKelaminTextToKey[$jenisKelaminInput] ?? null;
        if ($jenisKelamin === null && !empty($row['jenis_kelamin'])) {
            Log::warning("Import JumlahSertifikasiKompetensi: Jenis Kelamin '{$row['jenis_kelamin']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new JumlahSertifikasiKompetensi([
            'tahun'                 => $row['tahun'],
            'bulan'                 => $bulan,
            'jenis_lsp'             => $jenisLsp,
            'jenis_kelamin'         => $jenisKelamin,
            'provinsi'              => $row['provinsi'] ?? null,
            'lapangan_usaha_kbli'   => $row['lapangan_usaha_kbli'] ?? $row['kbli'] ?? null,
            'jumlah_sertifikasi'    => (int)($row['jumlah_sertifikasi'] ?? $row['jumlah'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.jenis_lsp' => ['required', 'string_or_numeric', Rule::in(array_merge(array_keys($this->jenisLspTextToKey), array_values($this->jenisLspTextToKey)))],
            '*.jenis_kelamin' => ['required', 'string_or_numeric', Rule::in(array_merge(array_keys($this->jenisKelaminTextToKey), array_values($this->jenisKelaminTextToKey)))],
            '*.provinsi' => 'required|string|max:255',
            '*.lapangan_usaha_kbli' => 'nullable|string|max:255',
            '*.kbli' => 'nullable|string|max:255',
            '*.jumlah_sertifikasi' => 'exclude_if:*.jumlah,present|required_without:*.jumlah|integer|min:0',
            '*.jumlah' => 'exclude_if:*.jumlah_sertifikasi,present|required_without:*.jumlah_sertifikasi|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.jenis_lsp.in' => 'Jenis LSP tidak valid (P1, P2, atau P3).',
            '*.jenis_kelamin.in' => 'Jenis Kelamin tidak valid (Laki-laki atau Perempuan).',
            '*.jumlah_sertifikasi.required_without' => 'Kolom jumlah_sertifikasi atau jumlah wajib diisi.',
            '*.jumlah.required_without' => 'Kolom jumlah atau jumlah_sertifikasi wajib diisi.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
