<?php

namespace App\Imports;

use App\Models\ProgressMou;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate; // Untuk konversi tanggal Excel
use Illuminate\Support\Facades\Log;

class ProgressMouImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

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
                'januari' => 1, 'jan' => 1, '1' => 1,
                'februari' => 2, 'feb' => 2, '2' => 2,
                'maret' => 3, 'mar' => 3, '3' => 3,
                'april' => 4, 'apr' => 4, '4' => 4,
                'mei' => 5, '5' => 5,
                'juni' => 6, 'jun' => 6, '6' => 6,
                'juli' => 7, 'jul' => 7, '7' => 7,
                'agustus' => 8, 'agu' => 8, 'ags' => 8, '8' => 8,
                'september' => 9, 'sep' => 9, '9' => 9,
                'oktober' => 10, 'okt' => 10, '10' => 10,
                'november' => 11, 'nov' => 11, '11' => 11,
                'desember' => 12, 'des' => 12, '12' => 12,
            ];
            $bulan = $bulanMap[strtolower(trim($bulanInput))] ?? null;
        }
        
        if ($bulan === null && !empty($bulanInput)) { // Hanya log jika ada input tapi tidak valid
            Log::warning("Import ProgressMou: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        // Jika bulan kosong dari excel dan field bulan di db nullable, biarkan null. Jika required, validasi akan gagal.

        $tanggalMulai = null;
        if (!empty($row['tanggal_mulai_perjanjian'])) {
            try {
                $tanggalMulai = is_numeric($row['tanggal_mulai_perjanjian']) ? Carbon::instance(ExcelDate::excelToDateTimeObject($row['tanggal_mulai_perjanjian'])) : Carbon::parse($row['tanggal_mulai_perjanjian']);
            } catch (\Exception $e) {
                Log::warning("Import ProgressMou: Gagal parse tanggal_mulai_perjanjian '{$row['tanggal_mulai_perjanjian']}'. Baris dilewati: " . json_encode($row) . " Error: " . $e->getMessage());
                return null;
            }
        }
        
        $tanggalSelesai = null;
        if (!empty($row['tanggal_selesai_perjanjian'])) {
            try {
                $tanggalSelesai = is_numeric($row['tanggal_selesai_perjanjian']) ? Carbon::instance(ExcelDate::excelToDateTimeObject($row['tanggal_selesai_perjanjian'])) : Carbon::parse($row['tanggal_selesai_perjanjian']);
            } catch (\Exception $e) {
                // Tanggal selesai bisa null, jadi tidak perlu return null jika parsing gagal, biarkan validasi yang menangani
                 Log::info("Import ProgressMou: Gagal parse tanggal_selesai_perjanjian '{$row['tanggal_selesai_perjanjian']}', akan diset null. Row: " . json_encode($row));
            }
        }

        return new ProgressMou([
            'tahun'                       => $row['tahun'],
            'bulan'                       => $bulan,
            'judul_mou'                   => $row['judul_mou'],
            'tanggal_mulai_perjanjian'    => $tanggalMulai ? $tanggalMulai->toDateString() : null,
            'tanggal_selesai_perjanjian'  => $tanggalSelesai ? $tanggalSelesai->toDateString() : null,
            'pihak_terlibat'              => $row['pihak_terlibat'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required', // Validasi lebih lanjut di model()
            '*.judul_mou' => 'required|string|max:2000', // Sesuaikan max length jika perlu
            '*.tanggal_mulai_perjanjian' => 'required', // Validasi format tanggal akan lebih baik di model() setelah parsing
            '*.tanggal_selesai_perjanjian' => 'nullable|after_or_equal:*.tanggal_mulai_perjanjian',
            '*.pihak_terlibat' => 'nullable|string|max:2000', // Sesuaikan max length
        ];
    }
    
    public function customValidationMessages()
    {
        return [
            '*.tahun.required' => 'Kolom tahun wajib diisi.',
            '*.bulan.required' => 'Kolom bulan wajib diisi atau formatnya tidak valid.',
            '*.judul_mou.required' => 'Kolom judul_mou wajib diisi.',
            '*.tanggal_mulai_perjanjian.required' => 'Kolom tanggal_mulai_perjanjian wajib diisi atau formatnya tidak valid.',
            '*.tanggal_selesai_perjanjian.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
