<?php

namespace App\Http\Controllers;

use App\Models\IKPA;
use App\Models\UnitKerjaEselonI;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class IKPAController extends Controller
{
    private $routeNamePrefix = 'sekretariat-jenderal.ikpa.';
    private $aspekPelaksanaanAnggaranOptions = [
        'Kualitas Perencanaan Anggaran',
        'Kualitas Pelaksanaan Anggaran',
        'Kualitas Hasil Pelaksanaan Anggaran',
        'Total',
    ];

    private function validationRules()
    {
        $opts = implode(',', $this->aspekPelaksanaanAnggaranOptions);
        return [
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'bulan' => 'required|integer|min:1|max:12',
            'kode_unit_kerja_eselon_i' => 'required|string|exists:unit_kerja_eselon_i,kode_uke1',
            'aspek_pelaksanaan_anggaran' => "required|string|in:$opts",
            'nilai_aspek' => 'required|integer|min:0|max:100',
            'konversi_bobot' => 'required|integer|min:0|max:100',
            'dispensasi_spm' => 'required|integer|min:0|max:100',
            'nilai_akhir' => 'required|integer|min:0|max:100'
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = IKPA::with(['unitKerjaEselonI']);

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }
        if ($request->filled('unit_kerja_filter')) {
            $query->where('kode_unit_kerja_eselon_i', $request->unit_kerja_filter);
        }
        if ($request->filled('aspek_pelaksanaan_anggaran_filter')) {
            $query->where('aspek_pelaksanaan_anggaran', $request->aspek_pelaksanaan_anggaran_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['tahun', 'bulan', 'kode_unit_kerja_eselon_i', 'aspek_pelaksanaan_anggaran', 'nilai_aspek', 'konversi_bobot', 'dispensasi_spm', 'nilai_akhir'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $indikatorKinerjaPelaksanaanAnggarans = $query->paginate(10)->appends($request->except('page'));
        $availableYears = IKPA::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();

        $aspekPelaksanaanAnggaranOptions = $this->aspekPelaksanaanAnggaranOptions;

        return view('ikpa.index', compact(
            'indikatorKinerjaPelaksanaanAnggarans',
            'availableYears',
            'unitKerjaEselonIs',
            'aspekPelaksanaanAnggaranOptions',
            'sortBy',
            'sortDirection'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();
        $aspekPelaksanaanAnggaranOptions = $this->aspekPelaksanaanAnggaranOptions;
        $indikatorKinerjaPelaksanaanAnggaran = new IKPA();
        return view('ikpa.create', compact(
            'indikatorKinerjaPelaksanaanAnggaran',
            'unitKerjaEselonIs',
            'aspekPelaksanaanAnggaranOptions'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $opts = implode(',', $this->aspekPelaksanaanAnggaranOptions);
        $validator = Validator::make($request->all(), $this->validationRules());

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                ->withErrors($validator)
                ->withInput();
        }

        IKPA::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
            ->with('success', 'Data Indikator Kinerja Pelaksanaan Anggaran berhasil diperbarui.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $indikatorKinerjaPelaksanaanAnggaran = IKPA::find($id);
        $unitKerjaEselonIs = UnitKerjaEselonI::orderBy('nama_unit_kerja_eselon_i')->get();
        $aspekPelaksanaanAnggaranOptions = $this->aspekPelaksanaanAnggaranOptions;
        return view('ikpa.edit', compact(
            'indikatorKinerjaPelaksanaanAnggaran',
            'unitKerjaEselonIs',
            'aspekPelaksanaanAnggaranOptions'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $indikatorKinerjaPelaksanaanAnggaran = IKPA::find($id);
        $opts = implode(',', $this->aspekPelaksanaanAnggaranOptions);
        $validator = Validator::make($request->all(), $this->validationRules());

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $indikatorKinerjaPelaksanaanAnggaran->id)
                ->withErrors($validator)
                ->withInput();
        }

        $indikatorKinerjaPelaksanaanAnggaran->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
            ->with('success', 'Data Indikator Kinerja Pelaksanaan Anggaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            IKPA::find($id)->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                ->with('success', 'Data Indikator Kinerja Pelaksanaan Anggaran berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting Indikator Kinerja Pelaksanaan Anggaran: {$e->getMessage()}");
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
            Excel::import(new IKPA, $file); // Pastikan IKPA sudah benar
            return redirect()->route($this->routeNamePrefix . 'index')
                ->with('success', 'Data Progres Temuan BPK berhasil diimpor dari Excel.');
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
            Log::error("Error importing ProgressTemuanBpk Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
