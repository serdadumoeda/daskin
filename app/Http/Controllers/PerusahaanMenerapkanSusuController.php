<?php

namespace App\Http\Controllers;

use App\Models\PerusahaanMenerapkanSusu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\PerusahaanMenerapkanSusuImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class PerusahaanMenerapkanSusuController extends Controller
{
    private $routeNamePrefix = 'phi.perusahaan-menerapkan-susu.';

    public function index(Request $request)
    {
        $query = PerusahaanMenerapkanSusu::query();

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

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'provinsi', 'kbli', 'jumlah_perusahaan_susu'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $perusahaanMenerapkanSusus = $query->paginate(10)->appends($request->except('page'));
        $availableYears = PerusahaanMenerapkanSusu::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        
        return view('perusahaan_menerapkan_susu.index', compact(
            'perusahaanMenerapkanSusus',
            'availableYears',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $perusahaanMenerapkanSusu = new PerusahaanMenerapkanSusu();
        return view('perusahaan_menerapkan_susu.create', compact('perusahaanMenerapkanSusu'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'kbli' => 'required|string|max:50',
            'jumlah_perusahaan_susu' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        PerusahaanMenerapkanSusu::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Perusahaan Menerapkan SUSU berhasil ditambahkan.');
    }

    // public function show(PerusahaanMenerapkanSusu $perusahaanMenerapkanSusu)
    // {
    //     return view('perusahaan_menerapkan_susu.show', compact('perusahaanMenerapkanSusu'));
    // }

    public function edit(PerusahaanMenerapkanSusu $perusahaanMenerapkanSusu)
    {
        return view('perusahaan_menerapkan_susu.edit', compact('perusahaanMenerapkanSusu'));
    }

    public function update(Request $request, PerusahaanMenerapkanSusu $perusahaanMenerapkanSusu)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'kbli' => 'required|string|max:50',
            'jumlah_perusahaan_susu' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $perusahaanMenerapkanSusu->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $perusahaanMenerapkanSusu->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Perusahaan Menerapkan SUSU berhasil diperbarui.');
    }

    public function destroy(PerusahaanMenerapkanSusu $perusahaanMenerapkanSusu)
    {
        try {
            $perusahaanMenerapkanSusu->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Perusahaan Menerapkan SUSU berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting PerusahaanMenerapkanSusu: {$e->getMessage()}");
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
            Excel::import(new PerusahaanMenerapkanSusuImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Perusahaan Menerapkan SUSU berhasil diimpor dari Excel.');
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
            Log::error("Error importing PerusahaanMenerapkanSusu Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
