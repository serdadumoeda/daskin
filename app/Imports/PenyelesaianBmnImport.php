<?php

namespace App\Imports;

use App\Models\PenyelesaianBmn;
use App\Models\SatuanKerja; // Untuk validasi kode_satuan_kerja
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PenyelesaianBmnImport implements ToModel, WithHeadingRow, WithValidation
{
    private $jenisBmnMap;
    private $statusPenggunaanMap;
    private $hentiGunaMap = [
        'ya' => true, 'tidak' => false,
        '1' => true, '0' => false,
    ];
    private $satuanKerjaKodes;

    public function __construct()
    {
        $this->jenisBmnMap = array_flip(PenyelesaianBmn::JENIS_BMN_OPTIONS);
        $this->statusPenggunaanMap = array_flip(PenyelesaianBmn::STATUS_PENGGUNAAN_OPTIONS);
        // Ambil semua kode_sk yang valid dari tabel satuan_kerja
        $this->satuanKerjaKodes = SatuanKerja::pluck('kode_sk')->toArray();
    }

    public function model(array $row)
    {
        $tahun = $row['tahun'] ?? null;
        $bulan = $row['bulan'] ?? null;
        // Ubah nama kolom di Excel menjadi 'kode_satuan_kerja' atau mapping di sini
        $kodeSatuanKerja = $row['kode_satuan_kerja'] ?? ($row['unit_kerja'] ?? null); 
        $jenisBmnInput = strtolower(trim($row['jenis_bmn'] ?? ''));
        $hentiGunaInput = strtolower(trim($row['henti_guna'] ?? ''));
        $statusPenggunaanInput = strtolower(trim($row['status_penggunaan'] ?? ''));
        $penetapanStatus = $row['penetapan_status_penggunaan'] ?? null;
        $kuantitas = $row['kuantitas'] ?? null;
        $nilaiAset = $row['nilai_aset'] ?? null;

        if (!empty($bulan) && !is_numeric($bulan)) {
            $bulan = $this->convertMonthNameToNumber($bulan);
        }
        $jenisBmnId = is_numeric($jenisBmnInput) ? (int)$jenisBmnInput : ($this->jenisBmnMap[$jenisBmnInput] ?? null);
        $hentiGunaValue = $this->hentiGunaMap[$hentiGunaInput] ?? null;
        if ($hentiGunaInput !== '' && $hentiGunaValue === null) {
             Log::warning("Import PenyelesaianBMN: Nilai henti_guna '{$row['henti_guna']}' tidak valid. Baris dilewati: " . json_encode($row));
             return null;
        }
        $statusPenggunaanId = is_numeric($statusPenggunaanInput) ? (int)$statusPenggunaanInput : ($this->statusPenggunaanMap[$statusPenggunaanInput] ?? null);

        // Validasi kode_satuan_kerja
        if (!in_array($kodeSatuanKerja, $this->satuanKerjaKodes)) {
            Log::warning("Import PenyelesaianBMN: Kode Satuan Kerja '{$kodeSatuanKerja}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        if (empty($tahun) || empty($bulan) || empty($kodeSatuanKerja) || $jenisBmnId === null || $hentiGunaValue === null || $statusPenggunaanId === null || $kuantitas === null || $nilaiAset === null) {
            Log::warning("Import PenyelesaianBMN: Data tidak lengkap atau enum tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        return new PenyelesaianBmn([
            'tahun'                       => $tahun,
            'bulan'                       => $bulan,
            'kode_satuan_kerja'           => $kodeSatuanKerja, // Simpan kode_sk
            'jenis_bmn'                   => $jenisBmnId,
            'henti_guna'                  => $hentiGunaValue,
            'status_penggunaan'           => $statusPenggunaanId,
            'penetapan_status_penggunaan' => $penetapanStatus,
            'kuantitas'                   => (int)$kuantitas,
            'nilai_aset'                  => (float)str_replace([',', '.'], ['', '.'], $nilaiAset), // Lebih robust untuk format angka
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4',
            '*.bulan' => 'required',
            // Ubah validasi 'unit_kerja' menjadi 'kode_satuan_kerja'
            '*.kode_satuan_kerja' => ['required', 'string', Rule::in($this->satuanKerjaKodes)], 
            // Atau jika di excel masih 'unit_kerja' tapi isinya kode_sk
            // '*.unit_kerja' => ['required', 'string', Rule::in($this->satuanKerjaKodes)],
            '*.jenis_bmn' => ['required', function ($attribute, $value, $fail) {
                $key = is_numeric($value) ? (int)$value : ($this->jenisBmnMap[strtolower(trim($value))] ?? null);
                if ($key === null || !array_key_exists($key, PenyelesaianBmn::JENIS_BMN_OPTIONS)) {
                    $fail("Nilai {$attribute} '{$value}' tidak valid.");
                }
            }],
            '*.henti_guna' => ['required', function ($attribute, $value, $fail) {
                if (!array_key_exists(strtolower(trim($value)), $this->hentiGunaMap)) {
                     $fail("Nilai {$attribute} '{$value}' tidak valid. Pilihan: Ya/Tidak atau 1/0.");
                }
            }],
            '*.status_penggunaan' => ['required', function ($attribute, $value, $fail) {
                $key = is_numeric($value) ? (int)$value : ($this->statusPenggunaanMap[strtolower(trim($value))] ?? null);
                if ($key === null || !array_key_exists($key, PenyelesaianBmn::STATUS_PENGGUNAAN_OPTIONS)) {
                    $fail("Nilai {$attribute} '{$value}' tidak valid.");
                }
            }],
            '*.penetapan_status_penggunaan' => 'nullable|string|max:255',
            '*.kuantitas' => 'required|integer|min:0',
            '*.nilai_aset' => 'required|string', // Validasi numeric akan dilakukan setelah membersihkan format
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.kode_satuan_kerja.required' => 'Kolom kode_satuan_kerja (atau unit_kerja di Excel yang berisi kode) wajib diisi.',
            '*.kode_satuan_kerja.in' => 'Kolom kode_satuan_kerja (atau unit_kerja di Excel) tidak valid.',
            // ... pesan lainnya ...
        ];
    }

    public function headingRow(): int { return 1; }

    private function convertMonthNameToNumber($monthName): ?int {
        $monthName = strtolower(trim($monthName));
        $months = [
            'januari' => 1, 'jan' => 1, 'februari' => 2, 'feb' => 2, 'maret' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4, 'mei' => 5, 'juni' => 6, 'jun' => 6, 'juli' => 7, 'jul' => 7,
            'agustus' => 8, 'agu' => 8, 'ags' => 8, 'september' => 9, 'sep' => 9,
            'oktober' => 10, 'okt' => 10, 'november' => 11, 'nov' => 11, 'desember' => 12, 'des' => 12,
        ];
        return $months[$monthName] ?? (is_numeric($monthName) && $monthName >= 1 && $monthName <= 12 ? (int)$monthName : null);
    }
}