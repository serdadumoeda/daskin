<?php

namespace App\Http\Controllers;

use App\Models\PenyelesaianBmn;
use App\Models\SatuanKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\PenyelesaianBmnImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PenyelesaianBmnController extends Controller
{
    private $routeNamePrefix = 'sekretariat-jenderal.penyelesaian-bmn.';

    private function getJenisBmnOptions(): array
    {
        return PenyelesaianBmn::JENIS_BMN_OPTIONS;
    }

    private function getHentiGunaOptions(): array
    {
        return PenyelesaianBmn::HENTI_GUNA_OPTIONS;
    }

    private function getStatusPenggunaanOptions(): array
    {
        return PenyelesaianBmn::STATUS_PENGGUNAAN_OPTIONS;
    }

    // Metode helper untuk membersihkan format angka dari input
    protected function prepareNilaiAsetForStorage(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        // 1. Hapus semua karakter kecuali digit, koma, dan titik (jika ada)
        //    Lebih aman, kita asumsikan format Indonesia: titik ribuan, koma desimal.
        //    Atau format standar: tidak ada ribuan, titik desimal.
        //    Kita akan konversi format Indonesia ke standar.
        
        // Hapus pemisah ribuan (titik)
        $cleanedValue = str_replace('.', '', $value);
        // Ganti pemisah desimal (koma) dengan titik
        $cleanedValue = str_replace(',', '.', $cleanedValue);
        
        // Jika setelah dibersihkan hasilnya valid secara numerik (misal tidak hanya "." atau ",")
        if (is_numeric($cleanedValue)) {
            return $cleanedValue;
        }
        // Jika input awal tidak bisa di-parse, kembalikan apa adanya agar validasi numeric gagal
        // atau kembalikan null jika fieldnya nullable dan memang kosong.
        // Untuk 'required|numeric', kita harus pastikan outputnya bisa divalidasi.
        // Jika inputnya misal "abc", setelah dibersihkan jadi "", numeric akan gagal.
        // Jika inputnya "1.2.3,4,5", setelah dibersihkan jadi "123.4.5", numeric akan gagal.
        // Jadi, biarkan validator Laravel yang menangani string yang "kotor".
        // Yang penting, string yang "difomat dengan benar" (misal "1.500.000,75") jadi "1500000.75"
        return $cleanedValue; // Kembalikan nilai yang sudah dibersihkan
    }


    public function index(Request $request)
    {
        $query = PenyelesaianBmn::with('satuanKerja'); 

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('kode_satuan_kerja_filter')) { 
            $query->where('kode_satuan_kerja', $request->kode_satuan_kerja_filter);
        }
        if ($request->filled('jenis_bmn_filter')) {
            $query->where('jenis_bmn', $request->jenis_bmn_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'kode_satuan_kerja', 'jenis_bmn', 'kuantitas', 'nilai_aset'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
             if ($sortBy == 'kode_satuan_kerja') {
                $query->orderBy('kode_satuan_kerja', $sortDirection);
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $penyelesaianBmns = $query->paginate(10)->appends($request->except('page'));
        $availableYears = PenyelesaianBmn::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        $jenisBmnOptions = $this->getJenisBmnOptions();
        $satuanKerjaOptions = SatuanKerja::orderBy('nama_satuan_kerja')->pluck('nama_satuan_kerja', 'kode_sk');

        return view('penyelesaian_bmn.index', compact(
            'penyelesaianBmns',
            'availableYears',
            'jenisBmnOptions',
            'satuanKerjaOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $penyelesaianBmn = new PenyelesaianBmn();
        $jenisBmnOptions = $this->getJenisBmnOptions();
        $hentiGunaOptions = $this->getHentiGunaOptions();
        $statusPenggunaanOptions = $this->getStatusPenggunaanOptions();
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get(); 

        return view('penyelesaian_bmn.create', compact(
            'penyelesaianBmn', 
            'jenisBmnOptions', 
            'hentiGunaOptions', 
            'statusPenggunaanOptions',
            'satuanKerjas'
        ));
    }

    public function store(Request $request)
    {
        // Persiapkan input nilai_aset sebelum validasi
        $request->merge([
            'nilai_aset' => $this->prepareNilaiAsetForStorage($request->input('nilai_aset')),
        ]);

        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk', 
            'jenis_bmn' => ['required', 'integer', Rule::in(array_keys($this->getJenisBmnOptions()))],
            'henti_guna' => ['required', Rule::in(array_map('strval', array_keys($this->getHentiGunaOptions())))],
            'status_penggunaan' => ['required', 'integer', Rule::in(array_keys($this->getStatusPenggunaanOptions()))],
            'penetapan_status_penggunaan' => 'nullable|string|max:255',
            'kuantitas' => 'required|integer|min:0',
            'nilai_aset' => 'required|numeric|min:0', // Validasi tetap numeric
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput(); // withInput akan mengirim kembali nilai_aset yang sudah di-merge (dibersihkan)
        }
        
        $validatedData = $validator->validated();
        $validatedData['henti_guna'] = ($validatedData['henti_guna'] === '1' || $validatedData['henti_guna'] === true);

        PenyelesaianBmn::create($validatedData);

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Penyelesaian BMN berhasil ditambahkan.');
    }

    public function show(PenyelesaianBmn $penyelesaianBmn)
    {
        $penyelesaianBmn->load('satuanKerja'); 
        return view('penyelesaian_bmn.show', compact('penyelesaianBmn'));
    }

    public function edit(PenyelesaianBmn $penyelesaianBmn)
    {
        $jenisBmnOptions = $this->getJenisBmnOptions();
        $hentiGunaOptions = $this->getHentiGunaOptions();
        $statusPenggunaanOptions = $this->getStatusPenggunaanOptions();
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();

        return view('penyelesaian_bmn.edit', compact(
            'penyelesaianBmn', 
            'jenisBmnOptions', 
            'hentiGunaOptions', 
            'statusPenggunaanOptions',
            'satuanKerjas'
        ));
    }

    public function update(Request $request, PenyelesaianBmn $penyelesaianBmn)
    {
        // Persiapkan input nilai_aset sebelum validasi
        $request->merge([
            'nilai_aset' => $this->prepareNilaiAsetForStorage($request->input('nilai_aset')),
        ]);

        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk',
            'jenis_bmn' => ['required', 'integer', Rule::in(array_keys($this->getJenisBmnOptions()))],
            'henti_guna' => ['required', Rule::in(array_map('strval', array_keys($this->getHentiGunaOptions())))],
            'status_penggunaan' => ['required', 'integer', Rule::in(array_keys($this->getStatusPenggunaanOptions()))],
            'penetapan_status_penggunaan' => 'nullable|string|max:255',
            'kuantitas' => 'required|integer|min:0',
            'nilai_aset' => 'required|numeric|min:0', // Validasi tetap numeric
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $penyelesaianBmn->id)
                        ->withErrors($validator)
                        ->withInput(); // withInput akan mengirim kembali nilai_aset yang sudah di-merge (dibersihkan)
        }

        $validatedData = $validator->validated();
        $validatedData['henti_guna'] = ($validatedData['henti_guna'] === '1' || $validatedData['henti_guna'] === true);

        $penyelesaianBmn->update($validatedData);

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Penyelesaian BMN berhasil diperbarui.');
    }

    public function destroy(PenyelesaianBmn $penyelesaianBmn)
    {
        try {
            $penyelesaianBmn->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Penyelesaian BMN berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting PenyelesaianBmn: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Gagal menghapus data.');
        }
    }

    public function importExcel(Request $request)
    {
        // ... (kode importExcel tetap sama, pastikan PenyelesaianBmnImport juga menangani format angka dengan benar) ...
        // PenyelesaianBmnImport yang saya berikan sebelumnya sudah mencoba membersihkan nilai_aset:
        // (float)str_replace([',', '.'], ['', '.'], $nilaiAset)
        // Ini mungkin perlu disesuaikan jika format Excel Anda berbeda, misalnya jika Excel mengirim angka murni.
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'index') 
                        ->withErrors($validator)
                        ->with('error', 'Gagal mengimpor data. Pastikan file valid.');
        }

        $file = $request->file('excel_file');

        try {
            Excel::import(new PenyelesaianBmnImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Penyelesaian BMN berhasil diimpor dari Excel.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $errorMessages = [];
             foreach ($failures as $failure) {
                 $errorMessages[] = "Baris {$failure->row()}: " . implode(", ", $failure->errors()) . " (Nilai yang diberikan: " . implode(", ", array_values($failure->values())) . ")";
             }
             return redirect()->route($this->routeNamePrefix . 'index')
                              ->with('error', 'Gagal mengimpor data karena validasi gagal.')
                              ->with('import_errors', $errorMessages);
        } catch (Exception $e) {
            Log::error("Error importing PenyelesaianBmn Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}