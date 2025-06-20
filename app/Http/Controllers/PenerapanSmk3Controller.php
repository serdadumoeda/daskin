<?php

namespace App\Http\Controllers;

use App\Models\PenerapanSmk3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\PenerapanSmk3Import;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PenerapanSmk3Controller extends Controller
{
    private $routeNamePrefix = 'binwasnaker.penerapan-smk3.';

    public function index(Request $request)
    {
        $query = PenerapanSmk3::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('provinsi_filter')) {
            $query->where('provinsi', 'like', '%' . $request->provinsi_filter . '%');
        }


        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'provinsi', 'jumlah_perusahaan'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $penerapanSmk3s = $query->paginate(10)->appends($request->except('page'));
        $availableYears = PenerapanSmk3::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('penerapan_smk3.index', [
            'penerapanSmk3s' => $penerapanSmk3s,
            'availableYears' => $availableYears,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection
        ]);
    }

    public function create()
    {
        $penerapanSmk3 = new PenerapanSmk3();
        return view('penerapan_smk3.create', [
            'penerapanSmk3' => $penerapanSmk3,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'jumlah_perusahaan' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        PenerapanSmk3::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Penerapan SMK3 berhasil ditambahkan.');
    }

    // public function show(PenerapanSmk3 $penerapanSmk3)
    // {
    //     return view('penerapan_smk3.show', compact('penerapanSmk3'));
    // }

    public function edit(PenerapanSmk3 $penerapanSmk3)
    {
        return view('penerapan_smk3.edit', [
            'penerapanSmk3' => $penerapanSmk3,
        ]);
    }

    public function update(Request $request, PenerapanSmk3 $penerapanSmk3)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'jumlah_perusahaan' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $penerapanSmk3->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $penerapanSmk3->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Penerapan SMK3 berhasil diperbarui.');
    }

    public function destroy(PenerapanSmk3 $penerapanSmk3)
    {
        try {
            $penerapanSmk3->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Penerapan SMK3 berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting PenerapanSmk3: {$e->getMessage()}");
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
            Excel::import(new PenerapanSmk3Import, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Penerapan SMK3 berhasil diimpor dari Excel.');
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
            Log::error("Error importing PenerapanSmk3 Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
