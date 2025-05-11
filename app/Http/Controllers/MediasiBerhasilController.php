<?php

namespace App\Http\Controllers;

use App\Models\MediasiBerhasil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\MediasiBerhasilImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MediasiBerhasilController extends Controller
{
    private $routeNamePrefix = 'phi.mediasi-berhasil.';

    public function index(Request $request)
    {
        $query = MediasiBerhasil::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('provinsi_filter')) {
            $query->where('provinsi', 'like', '%' . $request->provinsi_filter . '%');
        }
        if ($request->filled('kbli_filter')) {
            $query->where('kbli', 'like', '%' . $request->kbli_filter . '%');
        }
        if ($request->filled('jenis_perselisihan_filter')) {
            $query->where('jenis_perselisihan', $request->jenis_perselisihan_filter);
        }
        if ($request->filled('hasil_mediasi_filter')) {
            $query->where('hasil_mediasi', $request->hasil_mediasi_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'provinsi', 'kbli', 'jenis_perselisihan', 'hasil_mediasi', 'jumlah_mediasi', 'jumlah_mediasi_berhasil'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $mediasiBerhasils = $query->paginate(10)->appends($request->except('page'));
        $availableYears = MediasiBerhasil::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        $jenisPerselisihanOptions = MediasiBerhasil::getJenisPerselisihanOptions();
        $hasilMediasiOptions = MediasiBerhasil::getHasilMediasiOptions();

        return view('mediasi_berhasil.index', compact(
            'mediasiBerhasils',
            'availableYears',
            'jenisPerselisihanOptions',
            'hasilMediasiOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $mediasiBerhasil = new MediasiBerhasil();
        $jenisPerselisihanOptions = MediasiBerhasil::getJenisPerselisihanOptions();
        $hasilMediasiOptions = MediasiBerhasil::getHasilMediasiOptions();
        return view('mediasi_berhasil.create', compact('mediasiBerhasil', 'jenisPerselisihanOptions', 'hasilMediasiOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'kbli' => 'required|string|max:50',
            'jenis_perselisihan' => ['required', 'string', Rule::in(array_keys(MediasiBerhasil::getJenisPerselisihanOptions()))],
            'hasil_mediasi' => ['required', 'string', Rule::in(array_keys(MediasiBerhasil::getHasilMediasiOptions()))],
            'jumlah_mediasi' => 'required|integer|min:0',
            'jumlah_mediasi_berhasil' => 'required|integer|min:0|lte:jumlah_mediasi',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        MediasiBerhasil::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Mediasi Berhasil berhasil ditambahkan.');
    }

    // public function show(MediasiBerhasil $mediasiBerhasil)
    // {
    //     $jenisPerselisihanOptions = MediasiBerhasil::getJenisPerselisihanOptions();
    //     $hasilMediasiOptions = MediasiBerhasil::getHasilMediasiOptions();
    //     return view('mediasi_berhasil.show', compact('mediasiBerhasil', 'jenisPerselisihanOptions', 'hasilMediasiOptions'));
    // }

    public function edit(MediasiBerhasil $mediasiBerhasil)
    {
        $jenisPerselisihanOptions = MediasiBerhasil::getJenisPerselisihanOptions();
        $hasilMediasiOptions = MediasiBerhasil::getHasilMediasiOptions();
        return view('mediasi_berhasil.edit', compact('mediasiBerhasil', 'jenisPerselisihanOptions', 'hasilMediasiOptions'));
    }

    public function update(Request $request, MediasiBerhasil $mediasiBerhasil)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'kbli' => 'required|string|max:50',
            'jenis_perselisihan' => ['required', 'string', Rule::in(array_keys(MediasiBerhasil::getJenisPerselisihanOptions()))],
            'hasil_mediasi' => ['required', 'string', Rule::in(array_keys(MediasiBerhasil::getHasilMediasiOptions()))],
            'jumlah_mediasi' => 'required|integer|min:0',
            'jumlah_mediasi_berhasil' => 'required|integer|min:0|lte:jumlah_mediasi',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $mediasiBerhasil->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $mediasiBerhasil->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Mediasi Berhasil berhasil diperbarui.');
    }

    public function destroy(MediasiBerhasil $mediasiBerhasil)
    {
        try {
            $mediasiBerhasil->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Mediasi Berhasil berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting MediasiBerhasil: {$e->getMessage()}");
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
            Excel::import(new MediasiBerhasilImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Mediasi Berhasil berhasil diimpor dari Excel.');
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
            Log::error("Error importing MediasiBerhasil Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
