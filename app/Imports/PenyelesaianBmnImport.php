<?php

namespace App\Imports;

use App\Models\PenyelesaianBmn;
use App\Models\SatuanKerja;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class PenyelesaianBmnImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $satuanKerjaKodes;

    public function __construct()
    {
        $this->satuanKerjaKodes = SatuanKerja::pluck('kode_sk')->toArray();
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $kodeSk = $row['kode_satuan_kerja'] ?? null;
        if (empty($kodeSk) || !in_array($kodeSk, $this->satuanKerjaKodes)) {
            Log::warning("Import PenyelesaianBMN: Kode Satuan Kerja '{$kodeSk}' tidak valid atau tidak ditemukan. Baris dilewati: " . json_encode($row));
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
            Log::warning("Import PenyelesaianBMN: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $statusPenggunaanAset = (int)($row['status_penggunaan_aset'] ?? 0);
        $statusAsetDigunakan = null;
        $nup = $row['nup'] ?? null;

        if ($statusPenggunaanAset == 1) { // Aset Digunakan
            $statusAsetDigunakan = (int)($row['status_aset_digunakan'] ?? 0);
            if (!in_array($statusAsetDigunakan, [1, 2])) { // 1: Sudah PSP, 2: Belum PSP
                 Log::warning("Import PenyelesaianBMN: Status Aset Digunakan '{$statusAsetDigunakan}' tidak valid untuk Aset Digunakan. Baris dilewati: " . json_encode($row));
                return null;
            }
            if ($statusAsetDigunakan == 2 && empty($nup)) { // Belum PSP, NUP wajib
                Log::warning("Import PenyelesaianBMN: NUP wajib diisi jika Status Aset Digunakan adalah 'Belum PSP'. Baris dilewati: " . json_encode($row));
                return null;
            }
        } else if ($statusPenggunaanAset == 2) { // Aset Tidak Digunakan
            $statusAsetDigunakan = null; // Pastikan null
            $nup = null; // Pastikan null
        } else {
            Log::warning("Import PenyelesaianBMN: Status Penggunaan Aset '{$statusPenggunaanAset}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }


        return new PenyelesaianBmn([
            'tahun'                     => $row['tahun'],
            'bulan'                     => $bulan,
            'kode_satuan_kerja'         => $kodeSk,
            'status_penggunaan_aset'    => $statusPenggunaanAset,
            'status_aset_digunakan'     => $statusAsetDigunakan,
            'nup'                       => $nup,
            'kuantitas'                 => (int)($row['kuantitas'] ?? 0),
            'nilai_aset_rp'             => (float)($row['nilai_aset_rp'] ?? 0.00),
            'total_aset_rp'             => (float)($row['total_aset_rp'] ?? 0.00),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.kode_satuan_kerja' => 'required|string',
            '*.status_penggunaan_aset' => 'required|integer|in:1,2',
            '*.status_aset_digunakan' => 'nullable|integer|in:1,2',
            '*.nup' => 'nullable|string|max:255',
            '*.kuantitas' => 'required|integer|min:0',
            '*.nilai_aset_rp' => 'required|numeric|min:0',
            '*.total_aset_rp' => 'required|numeric|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.status_penggunaan_aset.in' => 'Status Penggunaan Aset tidak valid (pilih 1 atau 2).',
            '*.status_aset_digunakan.in' => 'Status Aset Digunakan tidak valid (pilih 1 atau 2).',
            // Tambahkan pesan kustom lainnya jika perlu
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
