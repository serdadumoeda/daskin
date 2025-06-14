<?php

namespace App\Imports;

use App\Models\JumlahKepesertaanPelatihan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JumlahKepesertaanPelatihanImport implements ToModel, WithHeadingRow, WithValidation
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

    private function parsePenyelenggara($penyelenggaraInput) {
        if (empty($penyelenggaraInput)) return null;
        $inputLower = strtolower(trim($penyelenggaraInput));
        if ($inputLower == 'internal' || $penyelenggaraInput == '1') return 1;
        if ($inputLower == 'eksternal' || $penyelenggaraInput == '2') return 2;
        return null;
    }

    private function parseTipeLembaga($tipeInput) {
        if (empty($tipeInput)) return null;
        $inputLower = strtolower(trim($tipeInput));
        $map = [
            'uptp' => 1, '1' => 1,
            'uptd' => 2, '2' => 2,
            'blkln' => 3, '3' => 3,
            'lembaga pelatihan k/l' => 4, 'k/l' => 4, '4' => 4,
            'skpd' => 5, '5' => 5,
            'lpk swasta' => 6, 'swasta' => 6, '6' => 6,
            'blk komunitas' => 7, 'komunitas' => 7, '7' => 7,
        ];
        return $map[$inputLower] ?? null;
    }

    private function parseJenisKelamin($jkInput) {
        if (empty($jkInput)) return null;
        $jkInputLower = strtolower(trim($jkInput));
        if ($jkInputLower == 'laki-laki' || $jkInputLower == 'laki' || $jkInputLower == 'l' || $jkInput == '1') return 1;
        if ($jkInputLower == 'perempuan' || $jkInputLower == 'wanita' || $jkInputLower == 'p' || $jkInputLower == 'w' || $jkInput == '2') return 2;
        return null;
    }

    private function parseStatusKelulusan($statusInput) {
        if (empty($statusInput)) return null;
        $inputLower = strtolower(trim($statusInput));
        if ($inputLower == 'lulus' || $statusInput == '1') return 1;
        if ($inputLower == 'tidak lulus' || $statusInput == '2') return 2;
        return null;
    }


    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $bulan = $this->parseBulan($row['bulan'] ?? null);
        if ($bulan === null && !empty($row['bulan'])) {
            Log::warning("Import JumlahKepesertaanPelatihan: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $penyelenggara = $this->parsePenyelenggara($row['penyelenggara_pelatihan'] ?? null);
        if ($penyelenggara === null && !empty($row['penyelenggara_pelatihan'])) {
             Log::warning("Import JumlahKepesertaanPelatihan: Penyelenggara Pelatihan '{$row['penyelenggara_pelatihan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $tipeLembaga = $this->parseTipeLembaga($row['tipe_lembaga'] ?? null);
        if ($tipeLembaga === null && !empty($row['tipe_lembaga'])) {
             Log::warning("Import JumlahKepesertaanPelatihan: Tipe Lembaga '{$row['tipe_lembaga']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }
        
        $jenisKelamin = $this->parseJenisKelamin($row['jenis_kelamin'] ?? null);
        if ($jenisKelamin === null && !empty($row['jenis_kelamin'])) {
             Log::warning("Import JumlahKepesertaanPelatihan: Jenis Kelamin '{$row['jenis_kelamin']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        $statusKelulusan = $this->parseStatusKelulusan($row['status_kelulusan'] ?? null);
         if ($statusKelulusan === null && !empty($row['status_kelulusan'])) {
             Log::warning("Import JumlahKepesertaanPelatihan: Status Kelulusan '{$row['status_kelulusan']}' tidak valid. Baris dilewati: " . json_encode($row));
            return null;
        }

        return new JumlahKepesertaanPelatihan([
            'tahun'                       => $row['tahun'],
            'bulan'                       => $bulan,
            'penyelenggara_pelatihan'     => $penyelenggara,
            'tipe_lembaga'                => $tipeLembaga,
            'jenis_kelamin'               => $jenisKelamin,
            'provinsi_tempat_pelatihan'   => $row['provinsi_tempat_pelatihan'] ?? null,
            'kejuruan'                    => $row['kejuruan'] ?? null,
            'status_kelulusan'            => $statusKelulusan,
            'jumlah'                      => (int)($row['jumlah'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            '*.bulan' => 'required',
            '*.penyelenggara_pelatihan' => 'required',
            '*.tipe_lembaga' => 'required',
            '*.jenis_kelamin' => 'required',
            '*.provinsi_tempat_pelatihan' => 'required|string|max:255',
            '*.kejuruan' => 'required|string|max:255',
            '*.status_kelulusan' => 'required',
            '*.jumlah' => 'required|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.penyelenggara_pelatihan.required' => 'Kolom penyelenggara_pelatihan wajib diisi atau formatnya tidak valid.',
            '*.tipe_lembaga.required' => 'Kolom tipe_lembaga wajib diisi atau formatnya tidak valid.',
            '*.jenis_kelamin.required' => 'Kolom jenis_kelamin wajib diisi atau formatnya tidak valid.',
            '*.status_kelulusan.required' => 'Kolom status_kelulusan wajib diisi atau formatnya tidak valid.',
        ];
    }

    public function headingRow(): int
    {
        return 1; 
    }
}
