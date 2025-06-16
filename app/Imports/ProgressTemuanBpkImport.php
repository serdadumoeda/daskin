<?php

namespace App\Imports;

use App\Models\ProgressTemuanBpk;
use App\Models\UnitKerjaEselonI;
use App\Models\SatuanKerja;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log; // Untuk logging jika ada masalah

class ProgressTemuanBpkImport implements ToModel, WithHeadingRow, WithValidation, WithMultipleSheets
{
    use Importable;

    // Simpan pemetaan kode ke ID untuk efisiensi jika diperlukan,
    // atau validasi keberadaan kode.
    // Jika Excel berisi KODE (UKE1-XXX, SK-XXX) maka kita bisa langsung pakai.
    // Jika Excel berisi NAMA, maka perlu pencarian ID berdasarkan nama.
    // Asumsi Excel akan berisi KODE Unit Kerja dan Satuan Kerja.

    private $unitKerjaEselonIKodes;
    private $satuanKerjaKodes;

    public function __construct()
    {
        // Ambil semua kode yang valid untuk validasi exists
        $this->unitKerjaEselonIKodes = UnitKerjaEselonI::pluck('kode_uke1')->toArray();
        $this->satuanKerjaKodes = SatuanKerja::pluck('kode_sk')->toArray();
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    //Make import by first sheet
    public function sheets(): array
    {
        return [
            0 => new ProgressTemuanBpkImport(), // 0 = first sheet
        ];
    }

    public function model(array $row)
    {
        // Cek apakah kode unit kerja dan satuan kerja ada di database
        // Ini bisa juga dilakukan di rules() tapi kadang lebih fleksibel di sini untuk logging
        if (!in_array($row['kode_unit_kerja_eselon_i'], $this->unitKerjaEselonIKodes)) {
            Log::warning("Import ProgressTemuanBPK: Kode Unit Kerja Eselon I '{$row['kode_unit_kerja_eselon_i']}' tidak ditemukan. Baris dilewati: " . json_encode($row));
            return null; // Lewati baris ini
        }
        if (!in_array($row['kode_satuan_kerja'], $this->satuanKerjaKodes)) {
            Log::warning("Import ProgressTemuanBPK: Kode Satuan Kerja '{$row['kode_satuan_kerja']}' tidak ditemukan. Baris dilewati: " . json_encode($row));
            return null; // Lewati baris ini
        }

        // Validasi tambahan atau transformasi data bisa dilakukan di sini jika perlu
        // sebelum mapping ke model.

        $temuanAdminKasus = (int)($row['temuan_administratif_kasus'] ?? 0);
        $tindakLanjutAdminKasus = (int)($row['tindak_lanjut_administratif_kasus'] ?? 0);
        $temuanKerugianRp = (float)($row['temuan_kerugian_negara_rp'] ?? 0);
        $tindakLanjutKerugianRp = (float)($row['tindak_lanjut_kerugian_negara_rp'] ?? 0);

        $persentaseAdmin = ($temuanAdminKasus > 0)
            ? round(($tindakLanjutAdminKasus / $temuanAdminKasus) * 100, 2)
            : 0;
        $persentaseKerugian = ($temuanKerugianRp > 0)
            ? round(($tindakLanjutKerugianRp / $temuanKerugianRp) * 100, 2)
            : 0;
            
        // Konversi bulan dari nama (jika di Excel berupa nama) atau pastikan angka 1-12
        $bulan = $row['bulan'];
        if (!is_numeric($bulan) || $bulan < 1 || $bulan > 12) {
            // Coba konversi nama bulan ke angka, contoh sederhana:
            $bulanMap = [
                'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
                'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
                'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
            ];
            $bulan = $bulanMap[strtolower(trim($bulan))] ?? null;
            if ($bulan === null) {
                 Log::warning("Import ProgressTemuanBPM: Format bulan '{$row['bulan']}' tidak valid. Baris dilewati: " . json_encode($row));
                return null;
            }
        }


        return new ProgressTemuanBpk([
            'tahun'                                 => $row['tahun'],
            'bulan'                                 => $bulan,
            'kode_unit_kerja_eselon_i'              => $row['kode_unit_kerja_eselon_i'],
            'kode_satuan_kerja'                     => $row['kode_satuan_kerja'],
            'temuan_administratif_kasus'            => $temuanAdminKasus,
            'temuan_kerugian_negara_rp'             => $temuanKerugianRp,
            'tindak_lanjut_administratif_kasus'     => $tindakLanjutAdminKasus,
            'tindak_lanjut_kerugian_negara_rp'      => $tindakLanjutKerugianRp,
            'persentase_tindak_lanjut_administratif'=> $persentaseAdmin,
            'persentase_tindak_lanjut_kerugian_negara' => $persentaseKerugian,
            // Timestamp akan diisi otomatis oleh Eloquent jika $timestamps = true di model
            // Namun jika menggunakan ToModel dengan array, dan model memiliki $timestamps=true
            // Laravel Excel akan mencoba mengisi created_at/updated_at jika ada di $row
            // Jika tidak ada dan ingin diisi saat import, lebih baik tambahkan secara manual:
            // 'created_at' => Carbon::now(),
            // 'updated_at' => Carbon::now(),
            // Atau, jika menggunakan mass assignment dan $fillable, pastikan ada di sana.
            // Untuk ToModel, Eloquent akan handle timestamps saat save().
        ]);
    }

    public function rules(): array
    {
        return [
            'tahun' => 'required|integer|digits:4',
            'bulan' => 'required', // Validasi bulan lebih lanjut di model() atau dengan custom rule
            'kode_unit_kerja_eselon_i' => 'required|string', // Validasi 'exists' bisa berat jika banyak row, jadi dicek di model()
            'kode_satuan_kerja' => 'required|string',
            'temuan_administratif_kasus' => 'required|integer|min:0',
            'temuan_kerugian_negara_rp' => 'required|numeric|min:0',
            'tindak_lanjut_administratif_kasus' => 'required|integer|min:0',
            'tindak_lanjut_kerugian_negara_rp' => 'required|numeric|min:0',
            // Persentase dihitung, tidak perlu di Excel atau validasi di sini
        ];
    }

    public function headingRow(): int
    {
        return 1; // Asumsi baris pertama adalah heading
    }
}