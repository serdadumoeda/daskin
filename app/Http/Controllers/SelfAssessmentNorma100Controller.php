<?php

namespace App\Http\Controllers;

use App\Models\SelfAssessmentNorma100;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\SelfAssessmentNorma100Import;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

class SelfAssessmentNorma100Controller extends Controller
{
    private $routeNamePrefix = 'binwasnaker.self-assessment-norma100.';

    public function index(Request $request)
    {
        $query = SelfAssessmentNorma100::query();

        if ($request->filled('tahun_filter')) {
            $query->where('tahun', $request->tahun_filter);
        }
        if ($request->filled('bulan_filter')) {
            $query->where('bulan', $request->bulan_filter);
        }

        $sortBy = $request->input('sort_by', 'tahun');
        $sortDirection = $request->input('sort_direction', 'desc');
        $sortableColumns = ['bulan', 'tahun', 'jumlah_perusahaan'];

        if (in_array($sortBy, $sortableColumns) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
        }

        $selfAssessmentNorma100s = $query->paginate(10)->appends($request->except('page'));
        $availableYears = SelfAssessmentNorma100::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('self_assessment_norma100.index', [
            'selfAssessmentNorma100s' => $selfAssessmentNorma100s,
            'availableYears' => $availableYears,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection
        ]);
    }

    public function create()
    {
        $selfAssessmentNorma100 = new SelfAssessmentNorma100();
        return view('self_assessment_norma100.create', [
            'selfAssessmentNorma100' => $selfAssessmentNorma100,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'jumlah_perusahaan' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'create')
                        ->withErrors($validator)
                        ->withInput();
        }

        SelfAssessmentNorma100::create($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Self Assessment Norma 100 berhasil ditambahkan.');
    }

    // public function show(SelfAssessmentNorma100 $selfAssessmentNorma100)
    // {
    //     return view('self_assessment_norma100.show', compact('selfAssessmentNorma100'));
    // }

    public function edit(SelfAssessmentNorma100 $selfAssessmentNorma100)
    {
        return view('self_assessment_norma100.edit', [
            'selfAssessmentNorma100' => $selfAssessmentNorma100,
        ]);
    }

    public function update(Request $request, SelfAssessmentNorma100 $selfAssessmentNorma100)
    {
        $validator = Validator::make($request->all(), [
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5),
            'jumlah_perusahaan' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route($this->routeNamePrefix . 'edit', $selfAssessmentNorma100->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $selfAssessmentNorma100->update($validator->validated());

        return redirect()->route($this->routeNamePrefix . 'index')
                         ->with('success', 'Data Self Assessment Norma 100 berhasil diperbarui.');
    }

    public function destroy(SelfAssessmentNorma100 $selfAssessmentNorma100)
    {
        try {
            $selfAssessmentNorma100->delete();
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Self Assessment Norma 100 berhasil dihapus.');
        } catch (Exception $e) {
            Log::error("Error deleting SelfAssessmentNorma100: {$e->getMessage()}");
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
            Excel::import(new SelfAssessmentNorma100Import, $file);
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('success', 'Data Self Assessment Norma 100 berhasil diimpor dari Excel.');
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
            Log::error("Error importing SelfAssessmentNorma100 Excel: {$e->getMessage()}");
            return redirect()->route($this->routeNamePrefix . 'index')
                             ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
