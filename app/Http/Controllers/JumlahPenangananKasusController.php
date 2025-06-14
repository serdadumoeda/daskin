<?php

namespace App\Http\Controllers;

use App\Models\JumlahPenangananKasus;
// SatuanKerja tidak lagi digunakan secara langsung untuk form/filter substansi (kecuali jika substansi punya tabel sendiri)
// use App\Models\SatuanKerja; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\JumlahPenangananKasusImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class JumlahPenangananKasusController extends Controller
{
    private $routeNamePrefix = 'sekretariat-jenderal.jumlah-penanganan-kasus.';

    public function index(Request $request)
    {
        // Menghapus with('satuanKerja') karena relasi diubah
        $query = JumlahPenangananKasus::query(); 

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        // Mengganti filter satuan_kerja_filter menjadi substansi_filter
        if ($request->filled('substansi_filter')) { 
            $query->where('substansi', 'like', '%' . $request->substansi_filter . '%');
        }
        if ($request->filled('jenis_perkara_filter')) {
            $query->where('jenis_perkara', 'like', '%' . $request->jenis_perkara_filter . '%');
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        // Mengganti kode_satuan_kerja dengan substansi di sortable columns
        $sortableColumns = ['tahun', 'bulan', 'substansi', 'jenis_perkara', 'jumlah_perkara']; 

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $jumlahPenangananKasuses = $query->paginate(10)->appends($request->except('page'));
        $availableYears = JumlahPenangananKasus::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        // $satuanKerjas tidak lagi diperlukan di sini
        // $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();

        return view('jumlah_penanganan_kasus.index', compact(
            'jumlahPenangananKasuses',
            'availableYears',
            // 'satuanKerjas', // Dihapus
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        // $satuanKerjas tidak lagi diperlukan
        // $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();
        $jumlahPenangananKasus = new JumlahPenangananKasus();
        // return view('jumlah_penanganan_kasus.create', compact('jumlahPenangananKasus', 'satuanKerjas'));
        return view('jumlah_penanganan_kasus.create', compact('jumlahPenangananKasus'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            // Validasi untuk substansi (menggantikan kode_satuan_kerja)
            'substansi' => 'required|string|max:255', 
            'jenis_perkara' => 'required|string|max:255',
            'jumlah_perkara' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        JumlahPenangananKasus::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Penanganan Kasus berhasil ditambahkan.');
    }

    // Mengaktifkan dan mengimplementasikan metode show()
    public function show(JumlahPenangananKasus $jumlahPenangananKasu) // Nama parameter disesuaikan dengan route model binding
    {
        // Tidak perlu load relasi satuanKerja lagi
        // $jumlahPenangananKasu->load('satuanKerja'); 
        return view('jumlah_penanganan_kasus.show', ['jumlahPenangananKasus' => $jumlahPenangananKasu]);
    }

    public function edit(JumlahPenangananKasus $jumlahPenangananKasu)
    {
        // $satuanKerjas tidak lagi diperlukan
        // $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();
        // return view('jumlah_penanganan_kasus.edit', ['jumlahPenangananKasus' => $jumlahPenangananKasu, 'satuanKerjas' => $satuanKerjas]);
        return view('jumlah_penanganan_kasus.edit', ['jumlahPenangananKasus' => $jumlahPenangananKasu]);
    }

    public function update(Request $request, JumlahPenangananKasus $jumlahPenangananKasu)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            // Validasi untuk substansi (menggantikan kode_satuan_kerja)
            'substansi' => 'required|string|max:255',
            'jenis_perkara' => 'required|string|max:255',
            'jumlah_perkara' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $jumlahPenangananKasu->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $jumlahPenangananKasu->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Penanganan Kasus berhasil diperbarui.');
    }

    public function destroy(JumlahPenangananKasus $jumlahPenangananKasu)
    {
        try {
            $jumlahPenangananKasu->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Penanganan Kasus berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting JumlahPenangananKasus: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Gagal menghapus data.');
        }
    }

    public function importExcel(Request $request)
    {
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
            Excel::import(new JumlahPenangananKasusImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Penanganan Kasus berhasil diimpor dari Excel.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $errorMessages = [];
             foreach ($failures as $failure) {
                 $errorMessages[] = "Baris {$failure->row()}: " . implode(", ", $failure->errors()) . " (Nilai: " . implode(", ", $failure->values()) . ")";
             }
             return redirect()->route($this->routeNamePrefix . 'index')
                              ->with('error', 'Gagal mengimpor data karena validasi gagal.')
                              ->with('import_errors', $errorMessages);
        } catch (Exception $e) {
            Log::error("Error importing JumlahPenangananKasus Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}