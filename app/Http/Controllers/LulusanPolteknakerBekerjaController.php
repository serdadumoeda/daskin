<?php

namespace App\Http\Controllers;

use App\Models\LulusanPolteknakerBekerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\LulusanPolteknakerBekerjaImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class LulusanPolteknakerBekerjaController extends Controller
{
    private $routeNamePrefix = 'sekretariat-jenderal.lulusan-polteknaker-bekerja.';

    public function index(Request $request)
    {
        $query = LulusanPolteknakerBekerja::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('program_studi_filter')) {
            $query->where('program_studi', $request->program_studi_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'program_studi', 'jumlah_lulusan', 'jumlah_lulusan_bekerja'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $lulusanPolteknakerBekerjas = $query->paginate(10)->appends($request->except('page'));
        $availableYears = LulusanPolteknakerBekerja::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        $programStudiOptions = [
            1 => 'Relasi Industri',
            2 => 'Keselamatan dan Kesehatan Kerja',
            3 => 'Manajemen Sumber Daya Manusia',
        ];

        return view('lulusan_polteknaker_bekerja.index', compact(
            'lulusanPolteknakerBekerjas',
            'availableYears',
            'programStudiOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $programStudiOptions = [
            1 => 'Relasi Industri',
            2 => 'Keselamatan dan Kesehatan Kerja',
            3 => 'Manajemen Sumber Daya Manusia',
        ];
        $lulusanPolteknakerBekerja = new LulusanPolteknakerBekerja();
        return view('lulusan_polteknaker_bekerja.create', compact('lulusanPolteknakerBekerja', 'programStudiOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'program_studi' => 'required|integer|in:1,2,3',
            'jumlah_lulusan' => 'required|integer|min:0',
            'jumlah_lulusan_bekerja' => 'required|integer|min:0|lte:jumlah_lulusan',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        LulusanPolteknakerBekerja::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Lulusan Polteknaker Bekerja berhasil ditambahkan.');
    }

    // public function show(LulusanPolteknakerBekerja $lulusanPolteknakerBekerja)
    // {
    //     // Jika Anda membuat view show
    //     $programStudiOptions = [1 => 'Relasi Industri', 2 => 'K3', 3 => 'MSDM'];
    //     return view('lulusan_polteknaker_bekerja.show', compact('lulusanPolteknakerBekerja', 'programStudiOptions'));
    // }

    public function edit(LulusanPolteknakerBekerja $lulusanPolteknakerBekerja)
    {
        $programStudiOptions = [
            1 => 'Relasi Industri',
            2 => 'Keselamatan dan Kesehatan Kerja',
            3 => 'Manajemen Sumber Daya Manusia',
        ];
        return view('lulusan_polteknaker_bekerja.edit', compact('lulusanPolteknakerBekerja', 'programStudiOptions'));
    }

    public function update(Request $request, LulusanPolteknakerBekerja $lulusanPolteknakerBekerja)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'program_studi' => 'required|integer|in:1,2,3',
            'jumlah_lulusan' => 'required|integer|min:0',
            'jumlah_lulusan_bekerja' => 'required|integer|min:0|lte:jumlah_lulusan',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $lulusanPolteknakerBekerja->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $lulusanPolteknakerBekerja->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Lulusan Polteknaker Bekerja berhasil diperbarui.');
    }

    public function destroy(LulusanPolteknakerBekerja $lulusanPolteknakerBekerja)
    {
        try {
            $lulusanPolteknakerBekerja->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Lulusan Polteknaker Bekerja berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting LulusanPolteknakerBekerja: {$e->getMessage()}");
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
            Excel::import(new LulusanPolteknakerBekerjaImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Lulusan Polteknaker Bekerja berhasil diimpor dari Excel.');
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
            Log::error("Error importing LulusanPolteknakerBekerja Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
