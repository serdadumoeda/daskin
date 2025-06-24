<?php

namespace App\Http\Controllers;

use App\Models\DataKetenagakerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\DataKetenagakerjaanImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class DataKetenagakerjaanController extends Controller
{
    private $routeNamePrefix = 'barenbang.data-ketenagakerjaan.';

    /**
     * Helper function untuk membersihkan input numerik yang diformat.
     */
    private function cleanNumericInput(array $data): array
    {
        $numericFields = [
            'penduduk_15_atas', 'angkatan_kerja', 'bukan_angkatan_kerja', 'sekolah',
            'mengurus_rumah_tangga', 'lainnya_bak', 'bekerja', 'pengangguran_terbuka',
            'tpak', 'tpt', 'tingkat_kesempatan_kerja',
        ];

        foreach ($numericFields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                // 1. Hapus pemisah ribuan (titik)
                $value = str_replace('.', '', $data[$field]);
                // 2. Ganti pemisah desimal (koma) dengan titik
                $value = str_replace(',', '.', $value);
                $data[$field] = $value;
            }
        }
        return $data;
    }

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
        
        $sortableColumns = [
            'tahun', 'bulan', 'penduduk_15_atas', 'angkatan_kerja',
            'bukan_angkatan_kerja', 'sekolah', 'mengurus_rumah_tangga',
            'lainnya_bak', 'tpak', 'bekerja', 'pengangguran_terbuka',
            'tpt', 'tingkat_kesempatan_kerja'
        ];

        if (in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $dataKetenagakerjaans = $query->paginate(10);
        
        $availableYears = DataKetenagakerjaan::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        $bulanOptions = range(1, 12);

        return view('data_ketenagakerjaan.index', compact('dataKetenagakerjaans', 'availableYears', 'bulanOptions'));
    }

    public function create()
    {
        $dataKetenagakerjaan = new DataKetenagakerjaan();
        return view('data_ketenagakerjaan.create', compact('dataKetenagakerjaan'));
    }

    public function store(Request $request)
    {
        // DIPERBAIKI: Bersihkan data input sebelum validasi
        $cleanedData = $this->cleanNumericInput($request->all());

        $validator = Validator::make($cleanedData, [
            'tahun' => 'required|integer|digits:4',
            'bulan' => 'required|integer|between:1,12',
            'penduduk_15_atas' => "nullable|numeric",
            'angkatan_kerja' => "nullable|numeric",
            'bukan_angkatan_kerja' => "nullable|numeric",
            'sekolah' => "nullable|numeric",
            'mengurus_rumah_tangga' => "nullable|numeric",
            'lainnya_bak' => "nullable|numeric",
            'bekerja' => "nullable|numeric",
            'pengangguran_terbuka' => "nullable|numeric",
            'tpak' => "nullable|numeric",
            'tpt' => "nullable|numeric",
            'tingkat_kesempatan_kerja' => "nullable|numeric",
        ]);

        if ($validator->fails()) {
            return redirect()->route('barenbang.data-ketenagakerjaan.create')
                        ->withErrors($validator)
                        ->withInput()
                        ->with('error', 'Data yang dimasukkan tidak valid. Silakan periksa kembali nilai yang Anda masukkan.');
        }

        DataKetenagakerjaan::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Ketenagakerjaan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $dataKetenagakerjaan = DataKetenagakerjaan::findOrFail($id);
        return view('data_ketenagakerjaan.show', compact('dataKetenagakerjaan'));
    }

    public function edit($id)
    {
        $dataKetenagakerjaan = DataKetenagakerjaan::findOrFail($id);
        return view('data_ketenagakerjaan.edit', compact('dataKetenagakerjaan'));
    }
    
    public function update(Request $request, $id)
    {
        // DIPERBAIKI: Bersihkan data input sebelum validasi
        $cleanedData = $this->cleanNumericInput($request->all());

        $validator = Validator::make($cleanedData, [
            'tahun' => 'required|integer|digits:4',
            'bulan' => 'required|integer|between:1,12',
            'penduduk_15_atas' => "nullable|numeric",
            'angkatan_kerja' => "nullable|numeric",
            'bukan_angkatan_kerja' => "nullable|numeric",
            'sekolah' => "nullable|numeric",
            'mengurus_rumah_tangga' => "nullable|numeric",
            'lainnya_bak' => "nullable|numeric",
            'bekerja' => "nullable|numeric",
            'pengangguran_terbuka' => "nullable|numeric",
            'tpak' => "nullable|numeric",
            'tpt' => "nullable|numeric",
            'tingkat_kesempatan_kerja' => "nullable|numeric",
        ]);

        if ($validator->fails()) {
            return redirect()->route('barenbang.data-ketenagakerjaan.edit', $id)
                        ->withErrors($validator)
                        ->withInput()
                        ->with('error', 'Data yang dimasukkan tidak valid.');
        }

        $data = DataKetenagakerjaan::findOrFail($id);
        $data->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $data = DataKetenagakerjaan::findOrFail($id);
            $data->delete();
            return redirect()->route($this->routeNamePrefix . 'index')->with('success', 'Data berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->route($this->routeNamePrefix . 'index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
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