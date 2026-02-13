<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PriorityLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\PengumumanRequest;
use App\Models\Pengumuman;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PengumumanController extends Controller
{
    public function data()
    {
        $data = Pengumuman::query()->ordered();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('priority', function ($row) {
                $colors = [
                    'tinggi' => 'bg-red-500',
                    'sedang' => 'bg-yellow-500',
                    'rendah' => 'bg-blue-500',
                ];
                $color = $colors[$row->priority->value] ?? 'bg-gray-500';

                return '<span class="badge '.$color.' text-white px-2 py-1 rounded">'.e($row->priority->getLabel()).'</span>';
            })
            ->editColumn('is_active', function ($row) {
                if ($row->isExpired()) {
                    return '<span class="badge bg-gray-500 text-white px-2 py-1 rounded">Kadaluarsa</span>';
                }
                if ($row->isScheduled()) {
                    return '<span class="badge bg-blue-500 text-white px-2 py-1 rounded">Terjadwal</span>';
                }

                return $row->is_active
                    ? '<span class="badge bg-green-500 text-white px-2 py-1 rounded">Aktif</span>'
                    : '<span class="badge bg-gray-500 text-white px-2 py-1 rounded">Tidak Aktif</span>';
            })
            ->editColumn('title', function ($row) {
                return \Illuminate\Support\Str::limit($row->title, 50);
            })
            ->addColumn('period', function ($row) {
                $start = $row->start_date ? $row->start_date->format('d/m/Y') : '-';
                $end = $row->end_date ? $row->end_date->format('d/m/Y') : '-';

                return $start.' s/d '.$end;
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.pengumuman.edit', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"><i class="bx bx-edit mr-1"></i> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.pengumuman.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$edit.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'is_active', 'priority'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.pengumuman.index');
    }

    public function create(): View
    {
        $priorities = PriorityLevel::toArray();

        return view('admin.pengumuman.create', compact('priorities'));
    }

    public function store(PengumumanRequest $request)
    {
        $validated = $request->validated();

        try {
            Pengumuman::create($validated);

            alert()->success('Success', 'Pengumuman berhasil disimpan!');

            return redirect()->route('admin.pengumuman.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function edit(string $id): View
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $priorities = PriorityLevel::toArray();

        return view('admin.pengumuman.create', compact('pengumuman', 'priorities'));
    }

    public function update(PengumumanRequest $request, string $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $validated = $request->validated();

        try {
            $pengumuman->update($validated);

            alert()->success('Success', 'Pengumuman berhasil diubah!');

            return redirect()->route('admin.pengumuman.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->delete();

        alert()->success('Success', 'Pengumuman berhasil dihapus!');

        return redirect()->route('admin.pengumuman.index');
    }
}
