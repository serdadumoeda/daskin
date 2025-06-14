<?php

namespace App\Imports;

use App\Models\SelfAssessmentNorma100;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SelfAssessmentNorma100Import implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $skalaPerusahaanOptions = ['mikro', 'kecil', 'menengah', 'besar'];
    // Untuk hasil assessment, kita simpan teksnya langsung. Validasi bisa lebih ketat jika perlu.
    protected $hasilAssessmentOptions = ['rendah (<70)', 'sedang (71-90)', 'tinggi (91-100)'];


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
            Log::warning("Import SelfAssessmentNorma100: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $skalaPerusahaanInput = strtolower(trim($row['skala_perusahaan'] ?? ''));
        $skalaPerusahaanValid = null;
        foreach($this->skalaPerusahaanOptions as $skala){
            if($skalaPerusahaanInput == $skala){
                $skalaPerusahaanValid = ucfirst($skala); // Simpan dengan huruf kapital di awal
                break;
            }
        }
        if ($skalaPerusahaanValid === null && !empty($row['skala_perusahaan'])) {
            Log::warning("Import SelfAssessmentNorma100: Skala Perusahaan '{$row['skala_perusahaan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $hasilAssessmentInput = strtolower(trim($row['hasil_assessment'] ?? ''));
        $hasilAssessmentValid = null;
        // Mencocokkan dengan opsi yang ada, bisa lebih fleksibel jika diperlukan
        foreach ($this->hasilAssessmentOptions as $opsi) {
            if (str_contains($hasilAssessmentInput, strtolower(explode(' ', $opsi)[0]))) { // Cocokkan kata pertama (Rendah, Sedang, Tinggi)
                $hasilAssessmentValid = $opsi; // Simpan teks lengkap dari opsi
                break;
            }
        }
         if ($hasilAssessmentValid === null && !empty($row['hasil_assessment'])) {
            Log::warning("Import SelfAssessmentNorma100: Hasil Assessment '{$row['hasil_assessment']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }


        return new SelfAssessmentNorma100([
            'bulan'               => $bulan,
            'tahun'               => $row['tahun'],
            'provinsi'            => $row['provinsi'] ?? null,
            'kbli'                => $row['kbli'] ?? null,
            'skala_perusahaan'    => $skalaPerusahaanValid,
            'hasil_assessment'    => $hasilAssessmentValid,
            'jumlah_perusahaan'   => (int)($row['jumlah_perusahaan'] ?? $row['jumlah'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.bulan' => 'required',
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.provinsi' => 'required|string|max:255',
            '*.kbli' => 'required|string|max:50',
            '*.skala_perusahaan' => ['required', 'string', Rule::in(array_map('ucfirst', $this->skalaPerusahaanOptions) + $this->skalaPerusahaanOptions)],
            '*.hasil_assessment' => ['required', 'string', Rule::in($this->hasilAssessmentOptions + array_map('strtolower', $this->hasilAssessmentOptions))],
            '*.jumlah_perusahaan' => 'nullable|integer|min:0',
            '*.jumlah' => 'nullable|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.skala_perusahaan.in' => 'Skala Perusahaan tidak valid (pilih: Mikro, Kecil, Menengah, Besar).',
            '*.hasil_assessment.in' => 'Hasil Assessment tidak valid (pilih: Rendah (<70), Sedang (71-90), Tinggi (91-100)).',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
