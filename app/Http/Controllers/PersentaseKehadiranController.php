<?php

namespace App\Http\Controllers;

use App\Models\PersentaseKehadiran;
use App\Models\UnitKerjaEselonI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\PersentaseKehadiranImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class PersentaseKehadiranController extends Controller
{
    private $routeNamePrefix = 'sekretariat-jenderal.persentase-kehadiran.';

    public function index(Request $request)
    {
        $query = PersentaseKehadiran::with(['unitKerjaEselonI']);

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('unit_kerja_filter')) {
            $query->where('kode_unit_kerja_eselon_i', $request->unit_kerja_filter);
        }
        if ($request->filled('status_asn_filter')) {
            $query->where('status_asn', $request->status_asn_filter);
        }
        if ($request->filled('status_kehadiran_filter')) {
            $query->where('status_kehadiran', $request->status_kehadiran_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'kode_unit_kerja_eselon_i', 'status_asn', 'status_kehadiran', 'jumlah_orang'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $persentaseKehadirans = $query->paginate(10)->appends($request->except('page'));
        $availableYears = PersentaseKehadiran::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();

        $statusAsnOptions = [1 => 'ASN', 2 => 'Non ASN'];
        $statusKehadiranOptions = [
            1 => 'WFO', 2 => 'Cuti', 3 => 'Dinas Luar',
            4 => 'Sakit', 5 => 'Tugas Belajar', 6 => 'Tanpa Keterangan'
        ];

        return view('persentase_kehadiran.index', compact(
            'persentaseKehadirans',
            'availableYears',
            'unitKerjaEselonIs',
            'statusAsnOptions',
            'statusKehadiranOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    public function create()
    {
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();
        $statusAsnOptions = [1 => 'ASN', 2 => 'Non ASN'];
        $statusKehadiranOptions = [
            1 => 'WFO', 2 => 'Cuti', 3 => 'Dinas Luar',
            4 => 'Sakit', 5 => 'Tugas Belajar', 6 => 'Tanpa Keterangan'
        ];
        $persentaseKehadiran = new PersentaseKehadiran();
        return view('persentase_kehadiran.create', compact('persentaseKehadiran', 'unitKerjaEselonIs', 'statusAsnOptions', 'statusKehadiranOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_unit_kerja_eselon_i' => 'required|string|exists:unit_kerja_eselon_i,kode_uke1',
            'status_asn' => 'required|integer|in:1,2',
            'status_kehadiran' => 'required|integer|in:1,2,3,4,5,6',
            'jumlah_orang' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        PersentaseKehadiran::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Persentase Kehadiran berhasil ditambahkan.');
    }

    // public function show(PersentaseKehadiran $persentaseKehadiran)
    // {
    //     $persentaseKehadiran->load(['unitKerjaEselonI', 'satuanKerja']);
    //     return view('persentase_kehadiran.show', compact('persentaseKehadiran'));
    // }

    public function edit(PersentaseKehadiran $persentaseKehadiran)
    {
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();
        $statusAsnOptions = [1 => 'ASN', 2 => 'Non ASN'];
        $statusKehadiranOptions = [
            1 => 'WFO', 2 => 'Cuti', 3 => 'Dinas Luar',
            4 => 'Sakit', 5 => 'Tugas Belajar', 6 => 'Tanpa Keterangan'
        ];
        return view('persentase_kehadiran.edit', compact('persentaseKehadiran', 'unitKerjaEselonIs', 'statusAsnOptions', 'statusKehadiranOptions'));
    }

    public function update(Request $request, PersentaseKehadiran $persentaseKehadiran)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_unit_kerja_eselon_i' => 'required|string|exists:unit_kerja_eselon_i,kode_uke1',
            'status_asn' => 'required|integer|in:1,2',
            'status_kehadiran' => 'required|integer|in:1,2,3,4,5,6',
            'jumlah_orang' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $persentaseKehadiran->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $persentaseKehadiran->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Persentase Kehadiran berhasil diperbarui.');
    }

    public function destroy(PersentaseKehadiran $persentaseKehadiran)
    {
        try {
            $persentaseKehadiran->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Persentase Kehadiran berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting PersentaseKehadiran: {$e->getMessage()}");
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
            Excel::import(new PersentaseKehadiranImport, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Persentase Kehadiran berhasil diimpor dari Excel.');
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
            Log::error("Error importing PersentaseKehadiran Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
