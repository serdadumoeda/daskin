<?php

namespace App\Http\Controllers;

use App\Models\PelaporanWlkpOnline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\PelaporanWlkpOnlineImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PelaporanWlkpOnlineController extends Controller
{
    private $routeNamePrefix = 'binwasnaker.pelaporan-wlkp-online.';

    public function index(Request $request)
    {
        $query = PelaporanWlkpOnline::query();

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
        $sortableColumns = ['tahun', 'bulan', 'provinsi', 'jumlah_perusahaan_melapor'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $pelaporanWlkpOnlines = $query->paginate(10)->appends($request->except('page'));
        $availableYears = PelaporanWlkpOnline::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('pelaporan_wlkp_online.index', compact(
            'pelaporanWlkpOnlines',
            'availableYears',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $pelaporanWlkpOnline = new PelaporanWlkpOnline();
        return view('pelaporan_wlkp_online.create', compact('pelaporanWlkpOnline'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'jumlah_perusahaan_melapor' => 'required|integer|min:0', // Pastikan ini divalidasi
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        PelaporanWlkpOnline::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Laporan WLKP Online berhasil ditambahkan.');
    }

    public function edit(PelaporanWlkpOnline $pelaporanWlkpOnline)
    {
        return view('pelaporan_wlkp_online.edit', compact('pelaporanWlkpOnline'));
    }

    public function update(Request $request, PelaporanWlkpOnline $pelaporanWlkpOnline)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'provinsi' => 'required|string|max:255',
            'jumlah_perusahaan_melapor' => 'required|integer|min:0', // Pastikan ini divalidasi
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $pelaporanWlkpOnline->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $pelaporanWlkpOnline->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Laporan WLKP Online berhasil diperbarui.');
    }

    public function destroy(PelaporanWlkpOnline $pelaporanWlkpOnline)
    {
        try {
            $pelaporanWlkpOnline->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Laporan WLKP Online berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting PelaporanWlkpOnline: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Gagal menghapus data. Kemungkinan data terkait dengan entitas lain.');
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
            Excel::import(new PelaporanWlkpOnlineImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Laporan WLKP Online berhasil diimpor dari Excel.');
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
            Log::error("Error importing PelaporanWlkpOnline Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate(Request $request) {
        $filePath = 'template_input_wlkp.xlsx';

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath);
        }
        abort(404, 'File not found.');
    }
}
