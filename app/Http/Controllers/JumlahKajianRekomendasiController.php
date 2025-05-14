<?php

namespace App\Http\Controllers;

use App\Models\JumlahKajianRekomendasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\JumlahKajianRekomendasiImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JumlahKajianRekomendasiController extends Controller
{
    private $routeNamePrefix = 'barenbang.jumlah-kajian-rekomendasi.';

    public function index(Request $request)
    {
        $query = JumlahKajianRekomendasi::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('substansi_filter')) {
            $query->where('substansi', $request->substansi_filter);
        }
        if ($request->filled('jenis_output_filter')) {
            $query->where('jenis_output', $request->jenis_output_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'substansi', 'jenis_output', 'jumlah'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $jumlahKajianRekomendasis = $query->paginate(10)->appends($request->except('page'));
        $availableYears = JumlahKajianRekomendasi::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        $substansiOptions = JumlahKajianRekomendasi::getSubstansiOptions();
        $jenisOutputOptions = JumlahKajianRekomendasi::getJenisOutputOptions();

        return view('jumlah_kajian_rekomendasi.index', compact(
            'jumlahKajianRekomendasis',
            'availableYears',
            'substansiOptions',
            'jenisOutputOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $jumlahKajianRekomendasi = new JumlahKajianRekomendasi();
        $substansiOptions = JumlahKajianRekomendasi::getSubstansiOptions();
        $jenisOutputOptions = JumlahKajianRekomendasi::getJenisOutputOptions();
        return view('jumlah_kajian_rekomendasi.create', compact('jumlahKajianRekomendasi', 'substansiOptions', 'jenisOutputOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'substansi' => ['required', 'integer', Rule::in(array_keys(JumlahKajianRekomendasi::getSubstansiOptions()))],
            'jenis_output' => ['required', 'integer', Rule::in(array_keys(JumlahKajianRekomendasi::getJenisOutputOptions()))],
            'jumlah' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        JumlahKajianRekomendasi::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Kajian dan Rekomendasi berhasil ditambahkan.');
    }

    // public function show(JumlahKajianRekomendasi $jumlahKajianRekomendasi)
    // {
    //     return view('jumlah_kajian_rekomendasi.show', compact('jumlahKajianRekomendasi'));
    // }

    public function edit(JumlahKajianRekomendasi $jumlahKajianRekomendasi)
    {
        $substansiOptions = JumlahKajianRekomendasi::getSubstansiOptions();
        $jenisOutputOptions = JumlahKajianRekomendasi::getJenisOutputOptions();
        return view('jumlah_kajian_rekomendasi.edit', compact('jumlahKajianRekomendasi', 'substansiOptions', 'jenisOutputOptions'));
    }

    public function update(Request $request, JumlahKajianRekomendasi $jumlahKajianRekomendasi)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'substansi' => ['required', 'integer', Rule::in(array_keys(JumlahKajianRekomendasi::getSubstansiOptions()))],
            'jenis_output' => ['required', 'integer', Rule::in(array_keys(JumlahKajianRekomendasi::getJenisOutputOptions()))],
            'jumlah' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $jumlahKajianRekomendasi->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $jumlahKajianRekomendasi->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah Kajian dan Rekomendasi berhasil diperbarui.');
    }

    public function destroy(JumlahKajianRekomendasi $jumlahKajianRekomendasi)
    {
        try {
            $jumlahKajianRekomendasi->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Kajian dan Rekomendasi berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting JumlahKajianRekomendasi: {$e->getMessage()}");
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
            Excel::import(new JumlahKajianRekomendasiImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah Kajian dan Rekomendasi berhasil diimpor dari Excel.');
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
            Log::error("Error importing JumlahKajianRekomendasi Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
