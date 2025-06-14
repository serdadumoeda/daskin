<?php

namespace App\Imports;

use App\Models\PengaduanPelanggaranNorma;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class PengaduanPelanggaranNormaImport implements ToModel, WithHeadingRow, WithValidation
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

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $bulanPengaduan = $this->parseBulan($row['bulan_pengaduan'] ?? null);
        $bulanTindakLanjut = $this->parseBulan($row['bulan_tindak_lanjut'] ?? null);

        if (empty($row['tahun_pengaduan']) || $bulanPengaduan === null) {
            Log::warning("Import PengaduanPelanggaranNorma: Tahun atau Bulan Pengaduan tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        // Jika tahun tindak lanjut diisi, bulan tindak lanjut juga harus diisi (atau sebaliknya)
        // Untuk impor, kita bisa biarkan nullable dan validasi di rules atau controller.
        // Di sini kita hanya pastikan jika ada input bulan_tindak_lanjut tapi tidak valid, kita null-kan.
        if ($bulanTindakLanjut === null && !empty($row['bulan_tindak_lanjut'])) {
             Log::info("Import PengaduanPelanggaranNorma: Format bulan_tindak_lanjut '{$row['bulan_tindak_lanjut']}' tidak valid, akan diset null. Row: " . json_encode($row));
        }
        if (empty($row['tahun_tindak_lanjut']) && $bulanTindakLanjut !== null) {
            Log::warning("Import PengaduanPelanggaranNorma: Bulan Tindak Lanjut diisi tapi Tahun Tindak Lanjut kosong. Baris dilewati: " . json_encode($row));
            return null;
        }
        if (!empty($row['tahun_tindak_lanjut']) && $bulanTindakLanjut === null) {
            Log::warning("Import PengaduanPelanggaranNorma: Tahun Tindak Lanjut diisi tapi Bulan Tindak Lanjut kosong/tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }


        return new PengaduanPelanggaranNorma([
            'tahun_pengaduan'       => $row['tahun_pengaduan'],
            'bulan_pengaduan'       => $bulanPengaduan,
            'tahun_tindak_lanjut'   => !empty($row['tahun_tindak_lanjut']) ? (int)$row['tahun_tindak_lanjut'] : null,
            'bulan_tindak_lanjut'   => $bulanTindakLanjut,
            'provinsi'              => $row['provinsi'] ?? null,
            'kbli'                  => $row['kbli'] ?? null,
            'jenis_pelanggaran'     => $row['jenis_pelanggaran'] ?? null,
            'jenis_tindak_lanjut'   => $row['jenis_tindak_lanjut'] ?? null,
            'hasil_tindak_lanjut'   => $row['hasil_tindak_lanjut'] ?? null,
            'jumlah_kasus'          => (int)($row['jumlah_kasus'] ?? $row['jumlah'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun_pengaduan' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan_pengaduan' => 'required',
            '*.tahun_tindak_lanjut' => 'nullable|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan_tindak_lanjut' => 'nullable|required_with:*.tahun_tindak_lanjut',
            '*.provinsi' => 'required|string|max:255',
            '*.kbli' => 'required|string|max:50',
            '*.jenis_pelanggaran' => 'required|string|max:255',
            '*.jenis_tindak_lanjut' => 'required|string|max:255',
            '*.hasil_tindak_lanjut' => 'required|string|max:255',
            '*.jumlah_kasus' => 'nullable|integer|min:0',
            '*.jumlah' => 'nullable|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.bulan_tindak_lanjut.required_with' => 'Bulan tindak lanjut wajib diisi jika tahun tindak lanjut diisi.',
            // Tambahkan pesan kustom lainnya jika perlu
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
