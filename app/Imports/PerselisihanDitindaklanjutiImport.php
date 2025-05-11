<?php

namespace App\Imports;

use App\Models\PerselisihanDitindaklanjuti;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PerselisihanDitindaklanjutiImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $jenisPerselisihanOptions;
    protected $caraPenyelesaianOptions;

    public function __construct()
    {
        // Ambil opsi dari model untuk validasi
        $this->jenisPerselisihanOptions = array_keys(PerselisihanDitindaklanjuti::getJenisPerselisihanOptions());
        $this->caraPenyelesaianOptions = array_keys(PerselisihanDitindaklanjuti::getCaraPenyelesaianOptions());
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
            Log::warning("Import PerselisihanDitindaklanjuti: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        // Validasi manual untuk jenis perselisihan dan cara penyelesaian jika teks di Excel
        // bisa bervariasi (misal, case insensitive atau singkatan)
        $jenisPerselisihanInput = trim($row['jenis_perselisihan'] ?? '');
        $validJenisPerselisihan = null;
        foreach (PerselisihanDitindaklanjuti::getJenisPerselisihanOptions() as $key => $value) {
            if (strcasecmp($jenisPerselisihanInput, $value) == 0 || strcasecmp($jenisPerselisihanInput, $key) == 0) {
                $validJenisPerselisihan = $value; // Simpan teks yang konsisten
                break;
            }
        }
        if ($validJenisPerselisihan === null && !empty($jenisPerselisihanInput)) {
             Log::warning("Import PerselisihanDitindaklanjuti: Jenis Perselisihan '{$jenisPerselisihanInput}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $caraPenyelesaianInput = trim($row['cara_penyelesaian'] ?? '');
        $validCaraPenyelesaian = null;
        foreach (PerselisihanDitindaklanjuti::getCaraPenyelesaianOptions() as $key => $value) {
             if (strcasecmp($caraPenyelesaianInput, $value) == 0 || strcasecmp($caraPenyelesaianInput, $key) == 0) {
                $validCaraPenyelesaian = $value; // Simpan teks yang konsisten
                break;
            }
        }
         if ($validCaraPenyelesaian === null && !empty($caraPenyelesaianInput)) {
             Log::warning("Import PerselisihanDitindaklanjuti: Cara Penyelesaian '{$caraPenyelesaianInput}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new PerselisihanDitindaklanjuti([
            'tahun'                     => $row['tahun'],
            'bulan'                     => $bulan,
            'provinsi'                  => $row['provinsi'] ?? null,
            'kbli'                      => $row['kbli'] ?? null,
            'jenis_perselisihan'        => $validJenisPerselisihan,
            'cara_penyelesaian'         => $validCaraPenyelesaian,
            'jumlah_perselisihan'       => (int)($row['jumlah_perselisihan'] ?? 0),
            'jumlah_ditindaklanjuti'    => (int)($row['jumlah_ditindaklanjuti'] ?? $row['jumlah_yang_ditindaklanjuti'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.provinsi' => 'required|string|max:255',
            '*.kbli' => 'required|string|max:50',
            '*.jenis_perselisihan' => ['required', 'string', Rule::in(PerselisihanDitindaklanjuti::getJenisPerselisihanOptions())],
            '*.cara_penyelesaian' => ['required', 'string', Rule::in(PerselisihanDitindaklanjuti::getCaraPenyelesaianOptions())],
            '*.jumlah_perselisihan' => 'required|integer|min:0',
            // Coba kedua kemungkinan nama header untuk jumlah ditindaklanjuti
            '*.jumlah_ditindaklanjuti' => 'exclude_if:*.jumlah_yang_ditindaklanjuti,present|required_without:*.jumlah_yang_ditindaklanjuti|integer|min:0|lte:*.jumlah_perselisihan',
            '*.jumlah_yang_ditindaklanjuti' => 'exclude_if:*.jumlah_ditindaklanjuti,present|required_without:*.jumlah_ditindaklanjuti|integer|min:0|lte:*.jumlah_perselisihan',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.jenis_perselisihan.in' => 'Jenis Perselisihan tidak valid.',
            '*.cara_penyelesaian.in' => 'Cara Penyelesaian tidak valid.',
            '*.jumlah_ditindaklanjuti.lte' => 'Jumlah yang ditindaklanjuti tidak boleh melebihi jumlah perselisihan.',
            '*.jumlah_yang_ditindaklanjuti.lte' => 'Jumlah yang ditindaklanjuti tidak boleh melebihi jumlah perselisihan.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
