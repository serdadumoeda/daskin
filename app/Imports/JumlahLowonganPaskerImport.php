<?php

namespace App\Imports;

use App\Models\JumlahLowonganPasker;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;
// Rule tidak digunakan secara eksplisit di sini untuk validasi enum
// use Illuminate\Validation\Rule;

class JumlahLowonganPaskerImport implements ToModel, WithHeadingRow, WithValidation
{
    private array $jenisKelaminMap;
    private array $statusDisabilitasMap;

    public function __construct()
    {
        $this->jenisKelaminMap = array_flip(array_map('strtolower', JumlahLowonganPasker::JENIS_KELAMIN_OPTIONS));
        $this->statusDisabilitasMap = array_flip(array_map('strtolower', JumlahLowonganPasker::STATUS_DISABILITAS_OPTIONS));
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

        // jenis_kelamin bisa dari Excel sebagai teks "Laki-laki" atau angka 1
        // Kolom di excel mungkin 'jenis_kelamin' atau 'gender'
        $jenisKelaminInput = $row['jenis_kelamin'] ?? ($row['gender'] ?? '');
        $jenisKelaminId = $this->getId($this->jenisKelaminMap, $jenisKelaminInput);
        
        $statusDisabilitasInput = $row['status_disabilitas'] ?? '';
        $statusDisabilitasId = $this->getId($this->statusDisabilitasMap, $statusDisabilitasInput);

        return new JumlahLowonganPasker([
            'tahun'                 => $row['tahun'] ?? null,
            'bulan'                 => $bulan,
            'jenis_kelamin'         => $jenisKelaminId,
            'provinsi_penempatan'   => trim($row['provinsi_penempatan'] ?? ''),
            'lapangan_usaha_kbli'   => trim($row['lapangan_usaha_kbli'] ?? ''),
            'status_disabilitas'    => $statusDisabilitasId,
            'jumlah_lowongan'       => (int)($row['jumlah_lowongan'] ?? ($row['jumlah'] ?? 0)), // Akumulasi dari 'jumlah' jika ada
        ]);
    }

    public function rules(): array
    {
        $validateEnum = function ($map, $options, $attributeName) {
            return ['required', function ($attribute, $value, $fail) use ($map, $options, $attributeName) {
                if ($this->getId($map, $value) === null) {
                    $fail("Nilai {$attribute} '{$value}' tidak valid untuk {$attributeName}. Pilihan yang ada (case insensitive untuk teks): " . implode(', ', $options));
                }
            }];
        };

        return [
            '*.tahun' => 'required|integer|digits:4',
            '*.bulan' => 'required',
            // Pastikan header Excel sesuai: 'jenis_kelamin' atau 'gender'
            '*.jenis_kelamin' => $validateEnum($this->jenisKelaminMap, JumlahLowonganPasker::JENIS_KELAMIN_OPTIONS, 'Jenis Kelamin'), 
            // '*.gender' => $validateEnum($this->jenisKelaminMap, JumlahLowonganPasker::JENIS_KELAMIN_OPTIONS, 'Gender'), // Alternatif jika header 'gender'
            '*.provinsi_penempatan' => 'required|string|max:100',
            '*.lapangan_usaha_kbli' => 'required|string|max:255',
            '*.status_disabilitas' => $validateEnum($this->statusDisabilitasMap, JumlahLowonganPasker::STATUS_DISABILITAS_OPTIONS, 'Status Disabilitas'),
            // Pastikan header Excel sesuai: 'jumlah_lowongan' atau 'jumlah'
            '*.jumlah_lowongan' => 'nullable|integer|min:0',
            '*.jumlah' => 'nullable|integer|min:0', // Jika header Excel adalah 'jumlah'
        ];
    }
    
    public function customValidationMessages()
    {
        return [ 
            '*.jenis_kelamin.required' => 'Kolom Jenis Kelamin (atau Gender) wajib diisi.',
            // ...
        ];
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