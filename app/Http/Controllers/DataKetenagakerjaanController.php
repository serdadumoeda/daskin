<?php

namespace App\Http\Controllers;

use App\Models\DataKetenagakerjaan; // Pastikan model di-import dengan benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\DataKetenagakerjaanImport; // Pastikan import class juga ada
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class DataKetenagakerjaanController extends Controller
{
    private $routeNamePrefix = 'barenbang.data-ketenagakerjaan.';

    public function index(Request $request)
    {
        $query = DataKetenagakerjaan::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        
        // Sesuaikan nama kolom di sortableColumns dengan yang ada di Model & Migrasi
        $sortableColumns = [
            'tahun', 'bulan', 
            'penduduk_15_tahun_ke_atas', 
            'angkatan_kerja', 
            'bukan_angkatan_kerja',
            'sekolah', 
            'mengurus_rumah_tangga', 
            'lainnya_bukan_angkatan_kerja', 
            'tingkat_partisipasi_angkatan_kerja', 
            'bekerja', 
            'pengangguran_terbuka', 
            'tingkat_pengangguran_terbuka', 
            'tingkat_kesempatan_kerja'
        ];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $dataKetenagakerjaans = $query->paginate(10)->appends($request->except('page'));
        $availableYears = DataKetenagakerjaan::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        return view('data_ketenagakerjaan.index', compact(
            'dataKetenagakerjaans',
            'availableYears',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $dataKetenagakerjaan = new DataKetenagakerjaan();
        return view('data_ketenagakerjaan.create', compact('dataKetenagakerjaan'));
    }

    private function cleanNumericRequest(Request $request, array $fields) {
        $cleanedData = $request->except($fields); 
        foreach ($fields as $field) {
            $value = $request->input($field);
            if (is_string($value)) {
                $cleanedValue = str_replace('.', '', $value); 
                $cleanedValue = str_replace(',', '.', $cleanedValue); 
                $cleanedData[$field] = is_numeric($cleanedValue) ? (float)$cleanedValue : null;
            } elseif (is_numeric($value)) {
                $cleanedData[$field] = (float)$value;
            } else {
                 $cleanedData[$field] = null; 
            }
        }
        return array_merge($request->except($fields), $cleanedData);
    }


    public function store(Request $request)
    {
        $numericFields = [
            'penduduk_15_tahun_ke_atas', 'angkatan_kerja', 'bukan_angkatan_kerja', 'sekolah', 
            'mengurus_rumah_tangga', 'lainnya_bukan_angkatan_kerja', 'tingkat_partisipasi_angkatan_kerja', 
            'bekerja', 'pengangguran_terbuka', 'tingkat_pengangguran_terbuka', 'tingkat_kesempatan_kerja'
        ];
        
        $dataToValidate = $request->all(); // Ambil semua data request awal
        foreach ($numericFields as $field) { // Iterasi dan clean hanya field numerik
            $value = $request->input($field);
            if (is_string($value)) {
                $cleanedValue = str_replace('.', '', $value); 
                $cleanedValue = str_replace(',', '.', $cleanedValue); 
                $dataToValidate[$field] = is_numeric($cleanedValue) ? (float)$cleanedValue : null;
            } elseif (is_numeric($value)) {
                 $dataToValidate[$field] = (float)$value;
            } else {
                 $dataToValidate[$field] = null;
            }
        }

        $validator = Validator::make($dataToValidate, [
            'tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'penduduk_15_tahun_ke_atas' => 'nullable|numeric|min:0',
            'angkatan_kerja' => 'nullable|numeric|min:0',
            'bukan_angkatan_kerja' => 'nullable|numeric|min:0',
            'sekolah' => 'nullable|numeric|min:0',
            'mengurus_rumah_tangga' => 'nullable|numeric|min:0',
            'lainnya_bukan_angkatan_kerja' => 'nullable|numeric|min:0',
            'tingkat_partisipasi_angkatan_kerja' => 'nullable|numeric|min:0|max:100', 
            'bekerja' => 'nullable|numeric|min:0',
            'pengangguran_terbuka' => 'nullable|numeric|min:0',
            'tingkat_pengangguran_terbuka' => 'nullable|numeric|min:0|max:100', 
            'tingkat_kesempatan_kerja' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput($request->all()); 
        }

        DataKetenagakerjaan::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Ketenagakerjaan berhasil ditambahkan.');
    }
    
    public function edit(DataKetenagakerjaan $dataKetenagakerjaan)
    {
        return view('data_ketenagakerjaan.edit', compact('dataKetenagakerjaan'));
    }

    public function update(Request $request, DataKetenagakerjaan $dataKetenagakerjaan)
    {
        $numericFields = [
            'penduduk_15_tahun_ke_atas', 'angkatan_kerja', 'bukan_angkatan_kerja', 'sekolah', 
            'mengurus_rumah_tangga', 'lainnya_bukan_angkatan_kerja', 'tingkat_partisipasi_angkatan_kerja', 
            'bekerja', 'pengangguran_terbuka', 'tingkat_pengangguran_terbuka', 'tingkat_kesempatan_kerja'
        ];
        $dataToValidate = $request->all();
        foreach ($numericFields as $field) {
            $value = $request->input($field);
            if (is_string($value)) {
                $cleanedValue = str_replace('.', '', $value); 
                $cleanedValue = str_replace(',', '.', $cleanedValue); 
                $dataToValidate[$field] = is_numeric($cleanedValue) ? (float)$cleanedValue : null;
            } elseif (is_numeric($value)) {
                 $dataToValidate[$field] = (float)$value;
            } else {
                 $dataToValidate[$field] = null;
            }
        }

        $validator = Validator::make($dataToValidate, [
            'tahun' => 'required|integer|digits:4|min:1900|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'penduduk_15_tahun_ke_atas' => 'nullable|numeric|min:0',
            'angkatan_kerja' => 'nullable|numeric|min:0',
            'bukan_angkatan_kerja' => 'nullable|numeric|min:0',
            'sekolah' => 'nullable|numeric|min:0',
            'mengurus_rumah_tangga' => 'nullable|numeric|min:0',
            'lainnya_bukan_angkatan_kerja' => 'nullable|numeric|min:0',
            'tingkat_partisipasi_angkatan_kerja' => 'nullable|numeric|min:0|max:100',
            'bekerja' => 'nullable|numeric|min:0',
            'pengangguran_terbuka' => 'nullable|numeric|min:0',
            'tingkat_pengangguran_terbuka' => 'nullable|numeric|min:0|max:100',
            'tingkat_kesempatan_kerja' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $dataKetenagakerjaan->id)
                        ->withErrors($validator)
                        ->withInput($request->all());
        }

        $dataKetenagakerjaan->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Ketenagakerjaan berhasil diperbarui.');
    }

    public function destroy(DataKetenagakerjaan $dataKetenagakerjaan)
    {
        try {
            $dataKetenagakerjaan->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Ketenagakerjaan berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting DataKetenagakerjaan: {$e->getMessage()}");
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
            Excel::import(new DataKetenagakerjaanImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Ketenagakerjaan berhasil diimpor dari Excel.');
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
            Log::error("Error importing DataKetenagakerjaan Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
