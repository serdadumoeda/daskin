<?php

namespace App\Http\Controllers;

use App\Models\JumlahPenangananKasus;
use App\Models\SatuanKerja;
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
        $query = JumlahPenangananKasus::with('satuanKerja');

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('satuan_kerja_filter')) {
            $query->where('kode_satuan_kerja', $request->satuan_kerja_filter);
        }
        if ($request->filled('jenis_perkara_filter')) {
            $query->where('jenis_perkara', 'like', '%' . $request->jenis_perkara_filter . '%');
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'kode_satuan_kerja', 'jenis_perkara', 'jumlah_perkara'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $jumlahPenangananKasuses = $query->paginate(10)->appends($request->except('page'));
        $availableYears = JumlahPenangananKasus::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();

        return view('jumlah_penanganan_kasus.index', compact(
            'jumlahPenangananKasuses',
            'availableYears',
            'satuanKerjas',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();
        $jumlahPenangananKasus = new JumlahPenangananKasus();
        return view('jumlah_penanganan_kasus.create', compact('jumlahPenangananKasus', 'satuanKerjas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk',
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

    // Show method is excluded by Route::resource except('show')
    // public function show(JumlahPenangananKasus $jumlahPenangananKasus)
    // {
    //     $jumlahPenangananKasus->load('satuanKerja');
    //     return view('jumlah_penanganan_kasus.show', compact('jumlahPenangananKasus'));
    // }

    public function edit(JumlahPenangananKasus $jumlahPenangananKasu) // Parameter name should match model
    {
        $satuanKerjas = SatuanKerja::orderBy('nama_satuan_kerja')->get();
        // Correct variable name for route model binding
        return view('jumlah_penanganan_kasus.edit', ['jumlahPenangananKasus' => $jumlahPenangananKasu, 'satuanKerjas' => $satuanKerjas]);
    }

    public function update(Request $request, JumlahPenangananKasus $jumlahPenangananKasu) // Parameter name should match model
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_satuan_kerja' => 'required|string|exists:satuan_kerja,kode_sk',
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

    public function destroy(JumlahPenangananKasus $jumlahPenangananKasu) // Parameter name should match model
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
