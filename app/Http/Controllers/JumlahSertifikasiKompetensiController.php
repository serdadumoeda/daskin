<?php

namespace App\Http\Controllers;

use App\Models\JumlahSertifikasiKompetensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\JumlahSertifikasiKompetensiImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class JumlahSertifikasiKompetensiController extends Controller
{
    private $routeNamePrefix = 'binalavotas.jumlah-sertifikasi-kompetensi.';

    public function index(Request $request)
    {
        $query = JumlahSertifikasiKompetensi::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('jenis_lsp_filter')) {
            $query->where('jenis_lsp', $request->jenis_lsp_filter);
        }
        if ($request->filled('jenis_kelamin_filter')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin_filter);
        }
        if ($request->filled('provinsi_filter')) {
            $query->where('provinsi', 'like', '%' . $request->provinsi_filter . '%');
        }
        if ($request->filled('kbli_filter')) {
            $query->where('lapangan_usaha_kbli', 'like', '%' . $request->kbli_filter . '%');
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = [
            'tahun', 'bulan', 'jenis_lsp', 'jenis_kelamin',
            'provinsi', 'lapangan_usaha_kbli', 'jumlah_sertifikasi'
        ];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $jumlahSertifikasiKompetensis = $query->paginate(10)->appends($request->except('page'));
        $availableYears = JumlahSertifikasiKompetensi::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $jenisLspOptions = JumlahSertifikasiKompetensi::getJenisLspOptions();
        $jenisKelaminOptions = JumlahSertifikasiKompetensi::getJenisKelaminOptions();

        return view('jumlah_sertifikasi_kompetensi.index', compact(
            'jumlahSertifikasiKompetensis',
            'availableYears',
            'jenisLspOptions',
            'jenisKelaminOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $jumlahSertifikasiKompetensi = new JumlahSertifikasiKompetensi();
        $jenisLspOptions = JumlahSertifikasiKompetensi::getJenisLspOptions();
        $jenisKelaminOptions = JumlahSertifikasiKompetensi::getJenisKelaminOptions();
        return view('jumlah_sertifikasi_kompetensi.create', compact('jumlahSertifikasiKompetensi', 'jenisLspOptions', 'jenisKelaminOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_lsp' => ['required', 'integer', Rule::in(array_keys(JumlahSertifikasiKompetensi::getJenisLspOptions()))],
            'jenis_kelamin' => ['required', 'integer', Rule::in(array_keys(JumlahSertifikasiKompetensi::getJenisKelaminOptions()))],
            'provinsi' => 'required|string|max:255',
            'lapangan_usaha_kbli' => 'required|string|max:255',
            'jumlah_sertifikasi' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        JumlahSertifikasiKompetensi::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Sertifikasi Kompetensi berhasil ditambahkan.');
    }

    // public function show(JumlahSertifikasiKompetensi $jumlahSertifikasiKompetensi)
    // {
    //     return view('jumlah_sertifikasi_kompetensi.show', compact('jumlahSertifikasiKompetensi'));
    // }

    public function edit(JumlahSertifikasiKompetensi $jumlahSertifikasiKompetensi)
    {
        $jenisLspOptions = JumlahSertifikasiKompetensi::getJenisLspOptions();
        $jenisKelaminOptions = JumlahSertifikasiKompetensi::getJenisKelaminOptions();
        return view('jumlah_sertifikasi_kompetensi.edit', compact('jumlahSertifikasiKompetensi', 'jenisLspOptions', 'jenisKelaminOptions'));
    }

    public function update(Request $request, JumlahSertifikasiKompetensi $jumlahSertifikasiKompetensi)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_lsp' => ['required', 'integer', Rule::in(array_keys(JumlahSertifikasiKompetensi::getJenisLspOptions()))],
            'jenis_kelamin' => ['required', 'integer', Rule::in(array_keys(JumlahSertifikasiKompetensi::getJenisKelaminOptions()))],
            'provinsi' => 'required|string|max:255',
            'lapangan_usaha_kbli' => 'required|string|max:255',
            'jumlah_sertifikasi' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $jumlahSertifikasiKompetensi->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $jumlahSertifikasiKompetensi->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Sertifikasi Kompetensi berhasil diperbarui.');
    }

    public function destroy(JumlahSertifikasiKompetensi $jumlahSertifikasiKompetensi)
    {
        try {
            $jumlahSertifikasiKompetensi->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Sertifikasi Kompetensi berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting JumlahSertifikasiKompetensi: {$e->getMessage()}");
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
            Excel::import(new JumlahSertifikasiKompetensiImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Sertifikasi Kompetensi berhasil diimpor dari Excel.');
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
            Log::error("Error importing JumlahSertifikasiKompetensi Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate(Request $request) {
        $filePath = 'template_input_jml_sertifikasi_kompetensi.xlsx';

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath);
        }
        abort(404, 'File not found.');
    }
}
