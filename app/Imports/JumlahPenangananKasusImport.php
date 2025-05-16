<?php

namespace App\Imports;

use App\Models\JumlahPenangananKasus;
// SatuanKerja tidak lagi diperlukan untuk validasi substansi sebagai string
// use App\Models\SatuanKerja; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class JumlahPenangananKasusImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    // $satuanKerjaKodes tidak lagi diperlukan
    // private $satuanKerjaKodes;

    public function __construct()
    {
        // $this->satuanKerjaKodes = SatuanKerja::pluck('kode_sk')->toArray(); // Dihapus
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Mengambil 'substansi' dari Excel, menggantikan 'kode_satuan_kerja'
        $substansi = $row['substansi'] ?? null; 

        // Validasi sederhana untuk substansi jika diperlukan (misal tidak boleh kosong)
        // Jika 'substansi' punya daftar nilai tetap, validasi lebih lanjut bisa ditambahkan di sini atau di rules()
        if (empty($substansi)) {
            Log::warning("Import JumlahPenangananKasus: Kolom 'substansi' kosong. Baris dilewati: " . json_encode($row));
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
            'substansi'         => $substansi, // Menggunakan $substansi dari Excel
            'jenis_perkara'     => $row['jenis_perkara'] ?? null,
            'jumlah_perkara'    => (int)($row['jumlah_perkara'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            // Mengganti validasi kode_satuan_kerja menjadi substansi
            '*.substansi' => 'required|string|max:255', 
            '*.jenis_perkara' => 'required|string|max:255',
            '*.jumlah_perkara' => 'required|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.tahun.required' => 'Kolom tahun wajib diisi.',
            '*.bulan.required' => 'Kolom bulan wajib diisi atau formatnya tidak valid.',
            // Pesan validasi untuk substansi
            '*.substansi.required' => 'Kolom substansi wajib diisi.',
            '*.substansi.string' => 'Kolom substansi harus berupa teks.',
            '*.substansi.max' => 'Kolom substansi tidak boleh lebih dari 255 karakter.',
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