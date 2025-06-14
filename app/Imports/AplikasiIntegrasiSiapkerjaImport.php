<?php

namespace App\Imports;

use App\Models\AplikasiIntegrasiSiapkerja;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AplikasiIntegrasiSiapkerjaImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $jenisInstansiTextToKey;
    private $statusIntegrasiTextToKey;

    public function __construct()
    {
        $this->jenisInstansiTextToKey = array_change_key_case(array_flip(AplikasiIntegrasiSiapkerja::getJenisInstansiOptions()), CASE_LOWER);
        foreach (AplikasiIntegrasiSiapkerja::getJenisInstansiOptions() as $key => $value) {
            $this->jenisInstansiTextToKey[strtolower((string)$key)] = $key;
        }

        $this->statusIntegrasiTextToKey = array_change_key_case(array_flip(AplikasiIntegrasiSiapkerja::getStatusIntegrasiOptions()), CASE_LOWER);
        foreach (AplikasiIntegrasiSiapkerja::getStatusIntegrasiOptions() as $key => $value) {
            $this->statusIntegrasiTextToKey[strtolower((string)$key)] = $key;
             if(strtolower($value) == 'belum') $this->statusIntegrasiTextToKey['belum'] = $key; // Alias
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
            Log::warning("Import AplikasiIntegrasiSiapkerja: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $jenisInstansiInput = strtolower(trim($row['jenis_instansi'] ?? ''));
        $jenisInstansi = $this->jenisInstansiTextToKey[$jenisInstansiInput] ?? null;
        if ($jenisInstansi === null && !empty($row['jenis_instansi'])) {
            Log::warning("Import AplikasiIntegrasiSiapkerja: Jenis Instansi '{$row['jenis_instansi']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $statusIntegrasiInput = strtolower(trim($row['status_integrasi'] ?? ''));
        $statusIntegrasi = $this->statusIntegrasiTextToKey[$statusIntegrasiInput] ?? null;
         if ($statusIntegrasi === null && !empty($row['status_integrasi'])) {
            Log::warning("Import AplikasiIntegrasiSiapkerja: Status Integrasi '{$row['status_integrasi']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new AplikasiIntegrasiSiapkerja([
            'tahun'                   => $row['tahun'],
            'bulan'                   => $bulan,
            'jenis_instansi'          => $jenisInstansi,
            'nama_instansi'           => $row['nama_instansi'] ?? null,
            'nama_aplikasi_website'   => $row['nama_aplikasi_website'] ?? $row['nama_aplikasiwebsite'] ?? null,
            'status_integrasi'        => $statusIntegrasi,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.jenis_instansi' => ['required', Rule::in(array_merge(array_keys($this->jenisInstansiTextToKey), array_values($this->jenisInstansiTextToKey)))],
            '*.nama_instansi' => 'required|string|max:255',
            '*.nama_aplikasi_website' => 'nullable|string|max:255',
            '*.nama_aplikasiwebsite' => 'nullable|string|max:255', // Alternatif header
            '*.status_integrasi' => ['required', Rule::in(array_merge(array_keys($this->statusIntegrasiTextToKey), array_values($this->statusIntegrasiTextToKey)))],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.jenis_instansi.in' => 'Jenis Instansi tidak valid.',
            '*.status_integrasi.in' => 'Status Integrasi tidak valid (Terintegrasi atau Belum terintegrasi).',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
