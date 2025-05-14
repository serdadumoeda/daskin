<?php

namespace App\Http\Controllers;

use App\Models\JumlahPenempatanKemnaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\JumlahPenempatanKemnakerImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JumlahPenempatanKemnakerController extends Controller
{
    private $routeNamePrefix = 'binapenta.jumlah-penempatan-kemnaker.';

    public function index(Request $request)
    {
        $query = JumlahPenempatanKemnaker::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('provinsi_filter')) {
            $query->where('provinsi_domisili', 'like', '%' . $request->provinsi_filter . '%');
        }
        if ($request->filled('kbli_filter')) {
            $query->where('lapangan_usaha_kbli', 'like', '%' . $request->kbli_filter . '%');
        }
        if ($request->filled('jenis_kelamin_filter')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin_filter);
        }
        if ($request->filled('status_disabilitas_filter')) {
            $query->where('status_disabilitas', $request->status_disabilitas_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'provinsi_domisili', 'lapangan_usaha_kbli', 'jenis_kelamin', 'status_disabilitas', 'ragam_disabilitas', 'jumlah'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $jumlahPenempatanKemnakers = $query->paginate(10)->appends($request->except('page'));
        $availableYears = JumlahPenempatanKemnaker::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        $jenisKelaminOptions = JumlahPenempatanKemnaker::getJenisKelaminOptions();
        $statusDisabilitasOptions = JumlahPenempatanKemnaker::getStatusDisabilitasOptions();
        // Ragam disabilitas tidak umum untuk filter utama, lebih ke detail atau form

        return view('jumlah_penempatan_kemnaker.index', compact(
            'jumlahPenempatanKemnakers',
            'availableYears',
            'jenisKelaminOptions',
            'statusDisabilitasOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $jumlahPenempatanKemnaker = new JumlahPenempatanKemnaker();
        $jenisKelaminOptions = JumlahPenempatanKemnaker::getJenisKelaminOptions();
        $statusDisabilitasOptions = JumlahPenempatanKemnaker::getStatusDisabilitasOptions();
        $ragamDisabilitasOptions = JumlahPenempatanKemnaker::getRagamDisabilitasOptions();
        return view('jumlah_penempatan_kemnaker.create', compact('jumlahPenempatanKemnaker', 'jenisKelaminOptions', 'statusDisabilitasOptions', 'ragamDisabilitasOptions'));
    }

    public function store(Request $request)
    {
        $rules = [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_kelamin' => ['required', 'integer', Rule::in(array_keys(JumlahPenempatanKemnaker::getJenisKelaminOptions()))],
            'provinsi_domisili' => 'required|string|max:255',
            'lapangan_usaha_kbli' => 'required|string|max:255',
            'status_disabilitas' => ['required', 'integer', Rule::in(array_keys(JumlahPenempatanKemnaker::getStatusDisabilitasOptions()))],
            'ragam_disabilitas' => ['nullable', Rule::requiredIf($request->status_disabilitas == 1), 'string', Rule::in(array_keys(JumlahPenempatanKemnaker::getRagamDisabilitasOptions()))],
            'jumlah' => 'required|integer|min:0',
        ];
        
        $validator = Validator::make($request->all(), $rules, [
            'ragam_disabilitas.required_if' => 'Ragam disabilitas wajib diisi jika status disabilitas adalah Ya.',
            'ragam_disabilitas.in' => 'Pilihan ragam disabilitas tidak valid.'
        ]);


        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $validatedData = $validator->validated();
        if ($validatedData['status_disabilitas'] == 2) { // Jika Tidak Disabilitas
            $validatedData['ragam_disabilitas'] = null;
        }

        JumlahPenempatanKemnaker::create($validatedData);

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Penempatan berhasil ditambahkan.');
    }

    // public function show(JumlahPenempatanKemnaker $jumlahPenempatanKemnaker)
    // {
    //     return view('jumlah_penempatan_kemnaker.show', compact('jumlahPenempatanKemnaker'));
    // }

    public function edit(JumlahPenempatanKemnaker $jumlahPenempatanKemnaker)
    {
        $jenisKelaminOptions = JumlahPenempatanKemnaker::getJenisKelaminOptions();
        $statusDisabilitasOptions = JumlahPenempatanKemnaker::getStatusDisabilitasOptions();
        $ragamDisabilitasOptions = JumlahPenempatanKemnaker::getRagamDisabilitasOptions();
        return view('jumlah_penempatan_kemnaker.edit', compact('jumlahPenempatanKemnaker', 'jenisKelaminOptions', 'statusDisabilitasOptions', 'ragamDisabilitasOptions'));
    }

    public function update(Request $request, JumlahPenempatanKemnaker $jumlahPenempatanKemnaker)
    {
         $rules = [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_kelamin' => ['required', 'integer', Rule::in(array_keys(JumlahPenempatanKemnaker::getJenisKelaminOptions()))],
            'provinsi_domisili' => 'required|string|max:255',
            'lapangan_usaha_kbli' => 'required|string|max:255',
            'status_disabilitas' => ['required', 'integer', Rule::in(array_keys(JumlahPenempatanKemnaker::getStatusDisabilitasOptions()))],
            'ragam_disabilitas' => ['nullable', Rule::requiredIf($request->status_disabilitas == 1), 'string', Rule::in(array_keys(JumlahPenempatanKemnaker::getRagamDisabilitasOptions()))],
            'jumlah' => 'required|integer|min:0',
        ];
        
        $validator = Validator::make($request->all(), $rules, [
            'ragam_disabilitas.required_if' => 'Ragam disabilitas wajib diisi jika status disabilitas adalah Ya.',
            'ragam_disabilitas.in' => 'Pilihan ragam disabilitas tidak valid.'
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $jumlahPenempatanKemnaker->id)
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $validatedData = $validator->validated();
        if ($validatedData['status_disabilitas'] == 2) { // Jika Tidak Disabilitas
            $validatedData['ragam_disabilitas'] = null;
        }

        $jumlahPenempatanKemnaker->update($validatedData);

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Penempatan berhasil diperbarui.');
    }

    public function destroy(JumlahPenempatanKemnaker $jumlahPenempatanKemnaker)
    {
        try {
            $jumlahPenempatanKemnaker->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Penempatan berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting JumlahPenempatanKemnaker: {$e->getMessage()}");
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
            Excel::import(new JumlahPenempatanKemnakerImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Penempatan berhasil diimpor dari Excel.');
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
            Log::error("Error importing JumlahPenempatanKemnaker Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
