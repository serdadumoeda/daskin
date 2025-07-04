<?php

namespace App\Http\Controllers;

use App\Models\JumlahPhk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\JumlahPhkImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class JumlahPhkController extends Controller
{
    private $routeNamePrefix = 'phi.jumlah-phk.';

    public function index(Request $request)
    {
        $query = JumlahPhk::query();

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
        $sortableColumns = ['tahun', 'bulan', 'provinsi', 'jumlah_tk_phk'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $jumlahPhks = $query->paginate(10)->appends($request->except('page'));
        $availableYears = JumlahPhk::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('jumlah_phk.index', compact(
            'jumlahPhks',
            'availableYears',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $jumlahPhk = new JumlahPhk();
        return view('jumlah_phk.create', compact('jumlahPhk'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            // 'jumlah_perusahaan_phk' => 'required|integer|min:0',
            'jumlah_tk_phk' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        JumlahPhk::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah PHK berhasil ditambahkan.');
    }

    // public function show(JumlahPhk $jumlahPhk)
    // {
    //     return view('jumlah_phk.show', compact('jumlahPhk'));
    // }

    public function edit(JumlahPhk $jumlahPhk)
    {
        return view('jumlah_phk.edit', compact('jumlahPhk'));
    }

    public function update(Request $request, JumlahPhk $jumlahPhk)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            // 'jumlah_perusahaan_phk' => 'required|integer|min:0',
            'jumlah_tk_phk' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $jumlahPhk->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $jumlahPhk->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Jumlah PHK berhasil diperbarui.');
    }

    public function destroy(JumlahPhk $jumlahPhk)
    {
        try {
            $jumlahPhk->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah PHK berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting JumlahPhk: {$e->getMessage()}");
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
            Excel::import(new JumlahPhkImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Jumlah PHK berhasil diimpor dari Excel.');
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
            Log::error("Error importing JumlahPhk Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
