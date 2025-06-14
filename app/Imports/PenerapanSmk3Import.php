<?php

namespace App\Imports;

use App\Models\PenerapanSmk3;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PenerapanSmk3Import implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    // Opsi valid untuk dropdown/validasi, bisa diambil dari config atau helper
    protected $kategoriPenilaianOptions = ['awal', 'transisi', 'lanjutan'];
    protected $tingkatPencapaianOptions = ['baik', 'memuaskan'];
    // Jenis penghargaan bisa lebih banyak, ini contoh dari PDF
    protected $jenisPenghargaanOptions = [
        'sertifikat emas', 
        'sertifikat emas dan bendera emas', 
        'sertifikat perak', 
        'sertifikat perak dan bendera perak'
    ];


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
            Log::warning("Import PenerapanSMK3: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        // Validasi manual untuk teks, karena Rule::in case sensitive
        $kategoriPenilaian = strtolower(trim($row['kategori_penilaian'] ?? ''));
        if (!in_array($kategoriPenilaian, $this->kategoriPenilaianOptions)) {
            Log::warning("Import PenerapanSMK3: Kategori Penilaian '{$row['kategori_penilaian']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $tingkatPencapaian = strtolower(trim($row['tingkat_pencapaian'] ?? ''));
        if (!in_array($tingkatPencapaian, $this->tingkatPencapaianOptions)) {
            Log::warning("Import PenerapanSMK3: Tingkat Pencapaian '{$row['tingkat_pencapaian']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        // Jenis penghargaan bisa lebih kompleks, untuk impor sederhana kita asumsikan teksnya cocok
        $jenisPenghargaan = trim($row['jenis_penghargaan'] ?? '');
        // if (!in_array(strtolower($jenisPenghargaan), array_map('strtolower', $this->jenisPenghargaanOptions))) {
        //     Log::warning("Import PenerapanSMK3: Jenis Penghargaan '{$jenisPenghargaan}' tidak dikenal. Baris dilewati: " . json_encode($row));
        //     return null;
        // }

        return new PenerapanSmk3([
            'tahun'               => $row['tahun'],
            'bulan'               => $bulan,
            'provinsi'            => $row['provinsi'] ?? null,
            'kbli'                => $row['kbli'] ?? null,
            'kategori_penilaian'  => ucfirst($kategoriPenilaian), // Simpan dengan huruf kapital di awal
            'tingkat_pencapaian'  => ucfirst($tingkatPencapaian), // Simpan dengan huruf kapital di awal
            'jenis_penghargaan'   => $jenisPenghargaan,
            'jumlah_perusahaan'   => (int)($row['jumlah_perusahaan'] ?? $row['jumlah'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.provinsi' => 'required|string|max:255',
            '*.kbli' => 'required|string|max:50',
            '*.kategori_penilaian' => ['required', 'string', Rule::in(array_map('ucfirst', $this->kategoriPenilaianOptions) + $this->kategoriPenilaianOptions)],
            '*.tingkat_pencapaian' => ['required', 'string', Rule::in(array_map('ucfirst', $this->tingkatPencapaianOptions) + $this->tingkatPencapaianOptions)],
            '*.jenis_penghargaan' => 'required|string|max:255',
            '*.jumlah_perusahaan' => 'nullable|integer|min:0',
            '*.jumlah' => 'nullable|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.kategori_penilaian.in' => 'Kategori Penilaian tidak valid (pilih: Awal, Transisi, Lanjutan).',
            '*.tingkat_pencapaian.in' => 'Tingkat Pencapaian tidak valid (pilih: Baik, Memuaskan).',
            // Tambahkan pesan kustom lainnya jika perlu
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
