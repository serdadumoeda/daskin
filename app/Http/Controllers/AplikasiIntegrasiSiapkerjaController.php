<?php

namespace App\Http\Controllers;

use App\Models\AplikasiIntegrasiSiapkerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\AplikasiIntegrasiSiapkerjaImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AplikasiIntegrasiSiapkerjaController extends Controller
{
    private $routeNamePrefix = 'barenbang.aplikasi-integrasi-siapkerja.';

    public function index(Request $request)
    {
        $query = AplikasiIntegrasiSiapkerja::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('jenis_instansi_filter')) {
            $query->where('jenis_instansi', $request->jenis_instansi_filter);
        }
        if ($request->filled('nama_instansi_filter')) {
            $query->where('nama_instansi', 'like', '%' . $request->nama_instansi_filter . '%');
        }
        if ($request->filled('status_integrasi_filter')) {
            $query->where('status_integrasi', $request->status_integrasi_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = [
            'tahun', 'bulan', 'jenis_instansi', 'nama_instansi', 
            'nama_aplikasi_website', 'status_integrasi'
        ];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $aplikasiIntegrasiSiapkerjas = $query->paginate(10)->appends($request->except('page'));
        $availableYears = AplikasiIntegrasiSiapkerja::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        $jenisInstansiOptions = AplikasiIntegrasiSiapkerja::getJenisInstansiOptions();
        $statusIntegrasiOptions = AplikasiIntegrasiSiapkerja::getStatusIntegrasiOptions();

        return view('jumlah_aplikasi_integrasi_siapkerja.index', compact(
            'aplikasiIntegrasiSiapkerjas',
            'availableYears',
            'jenisInstansiOptions',
            'statusIntegrasiOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $aplikasiIntegrasiSiapkerja = new AplikasiIntegrasiSiapkerja();
        $jenisInstansiOptions = AplikasiIntegrasiSiapkerja::getJenisInstansiOptions();
        $statusIntegrasiOptions = AplikasiIntegrasiSiapkerja::getStatusIntegrasiOptions();
        return view('jumlah_aplikasi_integrasi_siapkerja.create', compact('aplikasiIntegrasiSiapkerja', 'jenisInstansiOptions', 'statusIntegrasiOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_instansi' => ['required', 'integer', Rule::in(array_keys(AplikasiIntegrasiSiapkerja::getJenisInstansiOptions()))],
            'nama_instansi' => 'required|string|max:255',
            'nama_aplikasi_website' => 'required|string|max:255',
            'status_integrasi' => ['required', 'integer', Rule::in(array_keys(AplikasiIntegrasiSiapkerja::getStatusIntegrasiOptions()))],
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        AplikasiIntegrasiSiapkerja::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Aplikasi Terintegrasi berhasil ditambahkan.');
    }

    // public function show(AplikasiIntegrasiSiapkerja $aplikasiIntegrasiSiapkerja)
    // {
    //     return view('jumlah_aplikasi_integrasi_siapkerja.show', compact('aplikasiIntegrasiSiapkerja'));
    // }

    public function edit(AplikasiIntegrasiSiapkerja $aplikasiIntegrasiSiapkerja)
    {
        $jenisInstansiOptions = AplikasiIntegrasiSiapkerja::getJenisInstansiOptions();
        $statusIntegrasiOptions = AplikasiIntegrasiSiapkerja::getStatusIntegrasiOptions();
        return view('jumlah_aplikasi_integrasi_siapkerja.edit', compact('aplikasiIntegrasiSiapkerja', 'jenisInstansiOptions', 'statusIntegrasiOptions'));
    }

    public function update(Request $request, AplikasiIntegrasiSiapkerja $aplikasiIntegrasiSiapkerja)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_instansi' => ['required', 'integer', Rule::in(array_keys(AplikasiIntegrasiSiapkerja::getJenisInstansiOptions()))],
            'nama_instansi' => 'required|string|max:255',
            'nama_aplikasi_website' => 'required|string|max:255',
            'status_integrasi' => ['required', 'integer', Rule::in(array_keys(AplikasiIntegrasiSiapkerja::getStatusIntegrasiOptions()))],
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $aplikasiIntegrasiSiapkerja->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $aplikasiIntegrasiSiapkerja->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Aplikasi Terintegrasi berhasil diperbarui.');
    }

    public function destroy(AplikasiIntegrasiSiapkerja $aplikasiIntegrasiSiapkerja)
    {
        try {
            $aplikasiIntegrasiSiapkerja->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Aplikasi Terintegrasi berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting AplikasiIntegrasiSiapkerja: {$e->getMessage()}");
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
            Excel::import(new AplikasiIntegrasiSiapkerjaImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Aplikasi Terintegrasi berhasil diimpor dari Excel.');
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
            Log::error("Error importing AplikasiIntegrasiSiapkerja Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
