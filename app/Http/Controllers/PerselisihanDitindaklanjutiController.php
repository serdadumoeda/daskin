<?php

namespace App\Http\Controllers;

use App\Models\PerselisihanDitindaklanjuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\PerselisihanDitindaklanjutiImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PerselisihanDitindaklanjutiController extends Controller
{
    private $routeNamePrefix = 'phi.perselisihan-ditindaklanjuti.';

    public function index(Request $request)
    {
        $query = PerselisihanDitindaklanjuti::query();

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
        if ($request->filled('cara_penyelesaian_filter')) {
            $query->where('cara_penyelesaian', $request->cara_penyelesaian_filter);
        }


        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'provinsi', 'kbli', 'jenis_perselisihan', 'cara_penyelesaian', 'jumlah_perselisihan', 'jumlah_ditindaklanjuti'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $perselisihanDitindaklanjutis = $query->paginate(10)->appends($request->except('page'));
        $availableYears = PerselisihanDitindaklanjuti::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        $jenisPerselisihanOptions = PerselisihanDitindaklanjuti::getJenisPerselisihanOptions();
        $caraPenyelesaianOptions = PerselisihanDitindaklanjuti::getCaraPenyelesaianOptions();

        return view('perselisihan_ditindaklanjuti.index', compact(
            'perselisihanDitindaklanjutis',
            'availableYears',
            'jenisPerselisihanOptions',
            'caraPenyelesaianOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $perselisihanDitindaklanjuti = new PerselisihanDitindaklanjuti();
        $jenisPerselisihanOptions = PerselisihanDitindaklanjuti::getJenisPerselisihanOptions();
        $caraPenyelesaianOptions = PerselisihanDitindaklanjuti::getCaraPenyelesaianOptions();
        return view('perselisihan_ditindaklanjuti.create', compact('perselisihanDitindaklanjuti', 'jenisPerselisihanOptions', 'caraPenyelesaianOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'kbli' => 'required|string|max:50',
            'jenis_perselisihan' => ['required', 'string', Rule::in(array_keys(PerselisihanDitindaklanjuti::getJenisPerselisihanOptions()))],
            'cara_penyelesaian' => ['required', 'string', Rule::in(array_keys(PerselisihanDitindaklanjuti::getCaraPenyelesaianOptions()))],
            'jumlah_perselisihan' => 'required|integer|min:0',
            'jumlah_ditindaklanjuti' => 'required|integer|min:0|lte:jumlah_perselisihan',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        PerselisihanDitindaklanjuti::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Perselisihan Ditindaklanjuti berhasil ditambahkan.');
    }

    // public function show(PerselisihanDitindaklanjuti $perselisihanDitindaklanjuti)
    // {
    //     $jenisPerselisihanOptions = PerselisihanDitindaklanjuti::getJenisPerselisihanOptions();
    //     $caraPenyelesaianOptions = PerselisihanDitindaklanjuti::getCaraPenyelesaianOptions();
    //     return view('perselisihan_ditindaklanjuti.show', compact('perselisihanDitindaklanjuti', 'jenisPerselisihanOptions', 'caraPenyelesaianOptions'));
    // }

    public function edit(PerselisihanDitindaklanjuti $perselisihanDitindaklanjuti)
    {
        $jenisPerselisihanOptions = PerselisihanDitindaklanjuti::getJenisPerselisihanOptions();
        $caraPenyelesaianOptions = PerselisihanDitindaklanjuti::getCaraPenyelesaianOptions();
        return view('perselisihan_ditindaklanjuti.edit', compact('perselisihanDitindaklanjuti', 'jenisPerselisihanOptions', 'caraPenyelesaianOptions'));
    }

    public function update(Request $request, PerselisihanDitindaklanjuti $perselisihanDitindaklanjuti)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'kbli' => 'required|string|max:50',
            'jenis_perselisihan' => ['required', 'string', Rule::in(array_keys(PerselisihanDitindaklanjuti::getJenisPerselisihanOptions()))],
            'cara_penyelesaian' => ['required', 'string', Rule::in(array_keys(PerselisihanDitindaklanjuti::getCaraPenyelesaianOptions()))],
            'jumlah_perselisihan' => 'required|integer|min:0',
            'jumlah_ditindaklanjuti' => 'required|integer|min:0|lte:jumlah_perselisihan',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $perselisihanDitindaklanjuti->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $perselisihanDitindaklanjuti->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Perselisihan Ditindaklanjuti berhasil diperbarui.');
    }

    public function destroy(PerselisihanDitindaklanjuti $perselisihanDitindaklanjuti)
    {
        try {
            $perselisihanDitindaklanjuti->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Perselisihan Ditindaklanjuti berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting PerselisihanDitindaklanjuti: {$e->getMessage()}");
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
            Excel::import(new PerselisihanDitindaklanjutiImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Perselisihan Ditindaklanjuti berhasil diimpor dari Excel.');
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
            Log::error("Error importing PerselisihanDitindaklanjuti Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
