<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\GangguanStatus;
use App\Enums\SeverityLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\GangguanAirRequest;
use App\Models\GangguanAir;
use App\Models\Kecamatan;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class GangguanAirController extends Controller
{
    public function data()
    {
        $data = GangguanAir::query()->ordered();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('severity', function ($row) {
                $colors = [
                    'ringan' => 'bg-green-500',
                    'sedang' => 'bg-yellow-500',
                    'berat' => 'bg-red-500',
                ];
                $color = $colors[$row->severity->value] ?? 'bg-gray-500';

                return '<span class="badge '.$color.' text-white px-2 py-1 rounded">'.e($row->severity->getLabel()).'</span>';
            })
            ->editColumn('status', function ($row) {
                return $row->isActive()
                    ? '<span class="badge bg-red-500 text-white px-2 py-1 rounded">Aktif</span>'
                    : '<span class="badge bg-green-500 text-white px-2 py-1 rounded">Selesai</span>';
            })
            ->editColumn('title', function ($row) {
                return \Illuminate\Support\Str::limit($row->title, 40);
            })
            ->addColumn('period', function ($row) {
                $start = $row->start_datetime->format('d/m/Y H:i');
                $end = $row->actual_end_datetime
                    ? $row->actual_end_datetime->format('d/m/Y H:i')
                    : ($row->estimated_end_datetime ? 'Est: '.$row->estimated_end_datetime->format('d/m/Y H:i') : '-');

                return $start.' - '.$end;
            })
            ->addColumn('areas', function ($row) {
                $areas = $row->affected_areas ?? [];
                if (count($areas) > 2) {
                    return implode(', ', array_slice($areas, 0, 2)).' +'.count($areas) - 2;
                }

                return implode(', ', $areas) ?: '-';
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.gangguan-air.edit', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 transition-colors duration-200"><i class="bx bx-edit mr-1"></i> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.gangguan-air.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$edit.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'status', 'severity'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.gangguan-air.index');
    }

    public function create(): View
    {
        $severities = SeverityLevel::toArray();
        $statuses = GangguanStatus::toArray();
        $kecamatanList = Kecamatan::orderBy('nama')->pluck('nama', 'nama')->toArray();

        return view('admin.gangguan-air.create', compact('severities', 'statuses', 'kecamatanList'));
    }

    public function store(GangguanAirRequest $request)
    {
        $validated = $request->validated();

        try {
            GangguanAir::create($validated);

            alert()->success('Success', 'Data gangguan air berhasil disimpan!');

            return redirect()->route('admin.gangguan-air.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function edit(string $id): View
    {
        $gangguanAir = GangguanAir::findOrFail($id);
        $severities = SeverityLevel::toArray();
        $statuses = GangguanStatus::toArray();
        $kecamatanList = Kecamatan::orderBy('nama')->pluck('nama', 'nama')->toArray();

        return view('admin.gangguan-air.create', compact('gangguanAir', 'severities', 'statuses', 'kecamatanList'));
    }

    public function update(GangguanAirRequest $request, string $id)
    {
        $gangguanAir = GangguanAir::findOrFail($id);
        $validated = $request->validated();

        try {
            $gangguanAir->update($validated);

            alert()->success('Success', 'Data gangguan air berhasil diubah!');

            return redirect()->route('admin.gangguan-air.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $gangguanAir = GangguanAir::findOrFail($id);
        $gangguanAir->delete();

        alert()->success('Success', 'Data gangguan air berhasil dihapus!');

        return redirect()->route('admin.gangguan-air.index');
    }
}
