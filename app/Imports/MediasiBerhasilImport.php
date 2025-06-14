<?php

namespace App\Imports;

use App\Models\MediasiBerhasil;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MediasiBerhasilImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $jenisPerselisihanOptions;
    protected $hasilMediasiOptions;

    public function __construct()
    {
        $this->jenisPerselisihanOptions = array_keys(MediasiBerhasil::getJenisPerselisihanOptions());
        $this->hasilMediasiOptions = array_keys(MediasiBerhasil::getHasilMediasiOptions());
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
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
            Log::warning("Import MediasiBerhasil: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $jenisPerselisihanInput = trim($row['jenis_perselisihan'] ?? '');
        $validJenisPerselisihan = null;
        foreach (MediasiBerhasil::getJenisPerselisihanOptions() as $key => $value) {
            if (strcasecmp($jenisPerselisihanInput, $value) == 0 || strcasecmp($jenisPerselisihanInput, $key) == 0) {
                $validJenisPerselisihan = $key; // Simpan key yang konsisten (atau $value jika preferensi)
                break;
            }
        }
        if ($validJenisPerselisihan === null && !empty($jenisPerselisihanInput)) {
             Log::warning("Import MediasiBerhasil: Jenis Perselisihan '{$jenisPerselisihanInput}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $hasilMediasiInput = trim($row['hasil_mediasi'] ?? '');
        $validHasilMediasi = null;
        foreach (MediasiBerhasil::getHasilMediasiOptions() as $key => $value) {
             if (strcasecmp($hasilMediasiInput, $value) == 0 || strcasecmp($hasilMediasiInput, $key) == 0) {
                $validHasilMediasi = $key; // Simpan key yang konsisten
                break;
            }
        }
         if ($validHasilMediasi === null && !empty($hasilMediasiInput)) {
             Log::warning("Import MediasiBerhasil: Hasil Mediasi '{$hasilMediasiInput}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new MediasiBerhasil([
            'tahun'                     => $row['tahun'],
            'bulan'                     => $bulan,
            'provinsi'                  => $row['provinsi'] ?? null,
            'kbli'                      => $row['kbli'] ?? null,
            'jenis_perselisihan'        => $validJenisPerselisihan,
            'hasil_mediasi'             => $validHasilMediasi,
            'jumlah_mediasi'            => (int)($row['jumlah_mediasi'] ?? 0),
            'jumlah_mediasi_berhasil'   => (int)($row['jumlah_mediasi_berhasil'] ?? $row['jumlah_mediasi_yang_berhasil'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.provinsi' => 'required|string|max:255',
            '*.kbli' => 'required|string|max:50',
            '*.jenis_perselisihan' => ['required', 'string', Rule::in($this->jenisPerselisihanOptions)],
            '*.hasil_mediasi' => ['required', 'string', Rule::in($this->hasilMediasiOptions)],
            '*.jumlah_mediasi' => 'required|integer|min:0',
            // Coba kedua kemungkinan nama header untuk jumlah mediasi berhasil
            '*.jumlah_mediasi_berhasil' => 'exclude_if:*.jumlah_mediasi_yang_berhasil,present|required_without:*.jumlah_mediasi_yang_berhasil|integer|min:0|lte:*.jumlah_mediasi',
            '*.jumlah_mediasi_yang_berhasil' => 'exclude_if:*.jumlah_mediasi_berhasil,present|required_without:*.jumlah_mediasi_berhasil|integer|min:0|lte:*.jumlah_mediasi',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.jenis_perselisihan.in' => 'Jenis Perselisihan tidak valid.',
            '*.hasil_mediasi.in' => 'Hasil Mediasi tidak valid (pilih: PB, Anjuran).',
            '*.jumlah_mediasi_berhasil.lte' => 'Jumlah mediasi berhasil tidak boleh melebihi jumlah mediasi.',
            '*.jumlah_mediasi_yang_berhasil.lte' => 'Jumlah mediasi yang berhasil tidak boleh melebihi jumlah mediasi.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
