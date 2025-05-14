<?php

namespace App\Imports;

use App\Models\JumlahKajianRekomendasi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JumlahKajianRekomendasiImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $substansiTextToKey;
    private $jenisOutputTextToKey;

    public function __construct()
    {
        $this->substansiTextToKey = array_change_key_case(array_flip(JumlahKajianRekomendasi::getSubstansiOptions()), CASE_LOWER);
        foreach (JumlahKajianRekomendasi::getSubstansiOptions() as $key => $value) {
            $this->substansiTextToKey[strtolower((string)$key)] = $key; // map angka ke angka juga
        }

        $this->jenisOutputTextToKey = array_change_key_case(array_flip(JumlahKajianRekomendasi::getJenisOutputOptions()), CASE_LOWER);
         foreach (JumlahKajianRekomendasi::getJenisOutputOptions() as $key => $value) {
            $this->jenisOutputTextToKey[strtolower((string)$key)] = $key;
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
            Log::warning("Import JumlahKajianRekomendasi: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $substansiInput = strtolower(trim($row['substansi'] ?? ''));
        $substansi = $this->substansiTextToKey[$substansiInput] ?? null;
        if ($substansi === null && !empty($row['substansi'])) {
            Log::warning("Import JumlahKajianRekomendasi: Substansi '{$row['substansi']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $jenisOutputInput = strtolower(trim($row['jenis_output'] ?? $row['kajianrekomendasi'] ?? ''));
        $jenisOutput = $this->jenisOutputTextToKey[$jenisOutputInput] ?? null;
        if ($jenisOutput === null && !empty($row['jenis_output'] ?? $row['kajianrekomendasi'])) {
            Log::warning("Import JumlahKajianRekomendasi: Jenis Output '{$row['jenis_output']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new JumlahKajianRekomendasi([
            'tahun'             => $row['tahun'],
            'bulan'             => $bulan,
            'substansi'         => $substansi,
            'jenis_output'      => $jenisOutput,
            'jumlah'            => (int)($row['jumlah'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.substansi' => ['required', Rule::in(array_merge(array_keys($this->substansiTextToKey), array_values($this->substansiTextToKey)))],
            '*.jenis_output' => ['required', Rule::in(array_merge(array_keys($this->jenisOutputTextToKey), array_values($this->jenisOutputTextToKey)))],
            '*.kajianrekomendasi' => ['nullable', Rule::in(array_merge(array_keys($this->jenisOutputTextToKey), array_values($this->jenisOutputTextToKey)))], // Alternatif header
            '*.jumlah' => 'required|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.substansi.in' => 'Substansi tidak valid.',
            '*.jenis_output.in' => 'Jenis Output (Kajian/Rekomendasi) tidak valid.',
            '*.kajianrekomendasi.in' => 'Kolom Kajian/Rekomendasi tidak valid.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
