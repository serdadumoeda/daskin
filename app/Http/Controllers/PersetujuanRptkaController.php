<?php

namespace App\Http\Controllers;

use App\Models\PersetujuanRptka;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\PersetujuanRptkaImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PersetujuanRptkaController extends Controller
{
    private string $routeNamePrefix = 'binapenta.persetujuan-rptka.'; 

    public function getRouteNamePrefix(): string
    {
        return $this->routeNamePrefix;
    }

    private function getOptions(): array
    {
        return [
            'jenisKelaminOptions' => PersetujuanRptka::JENIS_KELAMIN_OPTIONS,
            'jabatanOptions' => PersetujuanRptka::JABATAN_OPTIONS,
            // Hapus lapanganUsahaKbliOptions
            // 'lapanganUsahaKbliOptions' => PersetujuanRptka::LAPANGAN_USAHA_KBLI_OPTIONS,
            'statusPengajuanOptions' => PersetujuanRptka::STATUS_PENGAJUAN_OPTIONS,
        ];
    }

    public function index(Request $request)
    {
        $query = PersetujuanRptka::query();

        // ... (filter tahun, bulan, jenis kelamin, negara asal tetap) ...
        if ($request->filled('jabatan_filter')) {
            $query->where('jabatan', $request->jabatan_filter);
        }
        // Filter untuk lapangan_usaha_kbli sekarang menggunakan like
        if ($request->filled('lapangan_usaha_kbli_filter')) {
            $query->where('lapangan_usaha_kbli', 'like', '%' . $request->lapangan_usaha_kbli_filter . '%');
        }
        if ($request->filled('provinsi_penempatan_filter')) {
            $query->where('provinsi_penempatan', 'like', '%' . $request->provinsi_penempatan_filter . '%');
        }
        if ($request->filled('status_pengajuan_filter')) {
            $query->where('status_pengajuan', $request->status_pengajuan_filter);
        }

        // ... (sorting tetap sama) ...
        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'jenis_kelamin', 'negara_asal', 'jabatan', 'lapangan_usaha_kbli', 'provinsi_penempatan', 'status_pengajuan', 'jumlah'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }


        $persetujuanRptkas = $query->paginate(10)->appends($request->except('page'));
        
        $availableYears = PersetujuanRptka::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYears->isEmpty() && !$availableYears->contains(date('Y'))) {
            $availableYears->push(date('Y'));
            $availableYears = $availableYears->sortDesc();
        }
        
        $options = $this->getOptions(); // lapanganUsahaKbliOptions tidak ada lagi
        $routeNamePrefix = $this->routeNamePrefix;

        return view('persetujuan_rptka.index', compact(
            'persetujuanRptkas',
            'availableYears',
            'options',
            'sortBy',
            'sortDirection',
            'routeNamePrefix'
        ));
    }

    public function create()
    {
        $persetujuanRptka = new PersetujuanRptka();
        $options = $this->getOptions(); // lapanganUsahaKbliOptions tidak ada lagi
        $routeNamePrefix = $this->routeNamePrefix;
        return view('persetujuan_rptka.create', compact('persetujuanRptka', 'options', 'routeNamePrefix'));
    }

    public function store(Request $request)
    {
        $options = $this->getOptions();
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_kelamin' => ['required', 'integer', Rule::in(array_keys($options['jenisKelaminOptions']))],
            'negara_asal' => 'required|string|max:100',
            'jabatan' => ['required', 'integer', Rule::in(array_keys($options['jabatanOptions']))],
            // Validasi lapangan_usaha_kbli sebagai string
            'lapangan_usaha_kbli' => 'required|string|max:255', 
            'provinsi_penempatan' => 'required|string|max:100',
            'status_pengajuan' => ['required', 'integer', Rule::in(array_keys($options['statusPengajuanOptions']))],
            'jumlah' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }
        
        PersetujuanRptka::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Persetujuan RPTKA berhasil ditambahkan.');
    }

    public function show(PersetujuanRptka $persetujuanRptka)
    {
        $routeNamePrefix = $this->routeNamePrefix;
        return view('persetujuan_rptka.show', compact('persetujuanRptka', 'routeNamePrefix'));
    }

    public function edit(PersetujuanRptka $persetujuanRptka)
    {
        $options = $this->getOptions(); // lapanganUsahaKbliOptions tidak ada lagi
        $routeNamePrefix = $this->routeNamePrefix;
        return view('persetujuan_rptka.edit', compact('persetujuanRptka', 'options', 'routeNamePrefix'));
    }

    public function update(Request $request, PersetujuanRptka $persetujuanRptka)
    {
        $options = $this->getOptions();
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'jenis_kelamin' => ['required', 'integer', Rule::in(array_keys($options['jenisKelaminOptions']))],
            'negara_asal' => 'required|string|max:100',
            'jabatan' => ['required', 'integer', Rule::in(array_keys($options['jabatanOptions']))],
            // Validasi lapangan_usaha_kbli sebagai string
            'lapangan_usaha_kbli' => 'required|string|max:255',
            'provinsi_penempatan' => 'required|string|max:100',
            'status_pengajuan' => ['required', 'integer', Rule::in(array_keys($options['statusPengajuanOptions']))],
            'jumlah' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $persetujuanRptka->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $persetujuanRptka->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Persetujuan RPTKA berhasil diperbarui.');
    }

    // ... (destroy dan importExcel tetap sama, pastikan import class disesuaikan) ...
    public function destroy(PersetujuanRptka $persetujuanRptka)
    {
        try {
            $persetujuanRptka->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Persetujuan RPTKA berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting PersetujuanRptka: {$e->getMessage()}");
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
            Excel::import(new PersetujuanRptkaImport, $file); // Pastikan PersetujuanRptkaImport disesuaikan
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Persetujuan RPTKA berhasil diimpor dari Excel.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $errorMessages = [];
             foreach ($failures as $failure) {
                 $errorMessages[] = "Baris {$failure->row()}: " . implode(", ", $failure->errors()) . " (Nilai yang diberikan: " . implode(", ", array_values($failure->values())) . ")";
             }
             return redirect()->route($this->routeNamePrefix . 'index')
                              ->with('error', 'Gagal mengimpor data karena validasi gagal.')
                              ->with('import_errors', $errorMessages);
        } catch (Exception $e) {
            Log::error("Error importing Persetujuan RPTKA Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}