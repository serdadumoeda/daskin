<?php

namespace App\Imports;

use App\Models\PersetujuanRptka;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;
// Rule tidak digunakan secara eksplisit di sini untuk validasi enum
// use Illuminate\Validation\Rule; 

class PersetujuanRptkaImport implements ToModel, WithHeadingRow, WithValidation
{
    private array $jenisKelaminMap;
    private array $jabatanMap;
    // Hapus lapanganUsahaMap
    // private array $lapanganUsahaMap; 
    private array $statusPengajuanMap;

    public function __construct()
    {
        $this->jenisKelaminMap = array_flip(array_map('strtolower', PersetujuanRptka::JENIS_KELAMIN_OPTIONS));
        $this->jabatanMap = array_flip(array_map('strtolower', PersetujuanRptka::JABATAN_OPTIONS));
        // Hapus inisialisasi lapanganUsahaMap
        // $this->lapanganUsahaMap = array_flip(array_map('strtolower', PersetujuanRptka::LAPANGAN_USAHA_KBLI_OPTIONS));
        $this->statusPengajuanMap = array_flip(array_map('strtolower', PersetujuanRptka::STATUS_PENGAJUAN_OPTIONS));
    }
    
    // Definisikan getId di sini karena dipanggil dari rules()
    protected function getId($map, $value) {
        if (is_numeric($value) && array_key_exists((int)$value, array_flip($map))) return (int)$value;
        return $map[strtolower(trim($value))] ?? null;
    }

    public function model(array $row)
    {
        $bulan = $row['bulan'] ?? null;
        if (!empty($bulan) && !is_numeric($bulan)) {
            $bulan = $this->convertMonthNameToNumber($bulan);
        }

        $jenisKelaminId = $this->getId($this->jenisKelaminMap, $row['jenis_kelamin'] ?? '');
        $jabatanId = $this->getId($this->jabatanMap, $row['jabatan'] ?? '');
        // Lapangan Usaha KBLI diambil sebagai string
        $lapanganUsahaKbli = trim($row['lapangan_usaha_kbli'] ?? ''); 
        $provinsiPenempatan = trim($row['provinsi_penempatan'] ?? ''); 
        $statusPengajuanId = $this->getId($this->statusPengajuanMap, $row['status_pengajuan_rptka'] ?? '');
        
        // Validasi sederhana bisa ditambahkan di sini jika diperlukan,
        // sebelum aturan validasi utama dijalankan.
        if (empty($lapanganUsahaKbli)) {
             Log::warning("Import RPTKA: Lapangan Usaha (KBLI) kosong. Baris dilewati: " . json_encode($row));
             return null; 
        }


        return new PersetujuanRptka([
            'tahun'                 => $row['tahun'] ?? null,
            'bulan'                 => $bulan,
            'jenis_kelamin'         => $jenisKelaminId,
            'negara_asal'           => $row['negara_asal'] ?? null,
            'jabatan'               => $jabatanId,
            'lapangan_usaha_kbli'   => $lapanganUsahaKbli, // Simpan sebagai string
            'provinsi_penempatan'   => $provinsiPenempatan,
            'status_pengajuan'      => $statusPengajuanId,
            'jumlah'                => (int)($row['jumlah'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        $validateEnum = function ($map, $options, $attributeName) {
            return ['required', function ($attribute, $value, $fail) use ($map, $options, $attributeName) {
                if ($this->getId($map, $value) === null) {
                    $fail("Nilai {$attribute} '{$value}' tidak valid untuk {$attributeName}.");
                }
            }];
        };
        
        return [
            '*.tahun' => 'required|integer|digits:4',
            '*.bulan' => 'required',
            '*.jenis_kelamin' => $validateEnum($this->jenisKelaminMap, PersetujuanRptka::JENIS_KELAMIN_OPTIONS, 'Jenis Kelamin'),
            '*.negara_asal' => 'required|string|max:100',
            '*.jabatan' => $validateEnum($this->jabatanMap, PersetujuanRptka::JABATAN_OPTIONS, 'Jabatan'),
            // Validasi lapangan_usaha_kbli sebagai string
            '*.lapangan_usaha_kbli' => 'required|string|max:255', 
            '*.provinsi_penempatan' => 'required|string|max:100', 
            '*.status_pengajuan_rptka' => $validateEnum($this->statusPengajuanMap, PersetujuanRptka::STATUS_PENGAJUAN_OPTIONS, 'Status Pengajuan RPTKA'),
            '*.jumlah' => 'required|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [ /* Pesan kustom jika diperlukan */ ];
    }

    public function headingRow(): int { return 1; }

    private function convertMonthNameToNumber($monthName): ?int {
        $monthName = strtolower(trim($monthName));
        $months = [
            'januari' => 1, 'jan' => 1, '1' => 1, 'februari' => 2, 'feb' => 2, '2' => 2,
            'maret' => 3, 'mar' => 3, '3' => 3, 'april' => 4, 'apr' => 4, '4' => 4,
            'mei' => 5, '5' => 5, 'juni' => 6, 'jun' => 6, '6' => 6,
            'juli' => 7, 'jul' => 7, '7' => 7, 'agustus' => 8, 'agu' => 8, 'ags' => 8, '8' => 8,
            'september' => 9, 'sep' => 9, '9' => 9, 'oktober' => 10, 'okt' => 10, '10' => 10,
            'november' => 11, 'nov' => 11, '11' => 11, 'desember' => 12, 'des' => 12, '12' => 12,
        ];
        return $months[$monthName] ?? (is_numeric($monthName) && $monthName >= 1 && $monthName <= 12 ? (int)$monthName : null);
    }
}