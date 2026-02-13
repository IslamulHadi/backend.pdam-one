<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\EmploymentType;
use App\Enums\LowonganStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LowonganRequest;
use App\Models\Lowongan;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class LowonganController extends Controller
{
    public function data()
    {
        $data = Lowongan::query()->ordered();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('employment_type', function ($row) {
                $colors = [
                    'full_time' => 'bg-blue-500',
                    'contract' => 'bg-orange-500',
                    'internship' => 'bg-green-500',
                ];
                $color = $colors[$row->employment_type->value] ?? 'bg-gray-500';

                return '<span class="badge '.$color.' text-white px-2 py-1 rounded">'.e($row->employment_type->getLabel()).'</span>';
            })
            ->editColumn('status', function ($row) {
                $expired = $row->isExpired() ? ' (Expired)' : '';
                $color = $row->status === LowonganStatus::Open ? 'bg-green-500' : 'bg-red-500';

                return '<span class="badge '.$color.' text-white px-2 py-1 rounded">'.e($row->status->getLabel()).$expired.'</span>';
            })
            ->editColumn('deadline', function ($row) {
                if (! $row->deadline) {
                    return '<span class="text-gray-400">Tidak ada</span>';
                }

                return $row->deadline->format('d M Y');
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-green-500 text-white px-2 py-1 rounded">Aktif</span>'
                    : '<span class="badge bg-gray-500 text-white px-2 py-1 rounded">Tidak Aktif</span>';
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.lowongan.edit', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 transition-colors duration-200"><i class="bx bx-edit mr-1"></i> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.lowongan.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$edit.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'is_active', 'employment_type', 'status', 'deadline'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.lowongan.index');
    }

    public function create(): View
    {
        $employmentTypes = EmploymentType::toArray();
        $statuses = LowonganStatus::toArray();

        return view('admin.lowongan.create', compact('employmentTypes', 'statuses'));
    }

    public function store(LowonganRequest $request)
    {
        Lowongan::create($request->validated());

        alert()->success('Success', 'Lowongan berhasil disimpan!');

        return redirect()->route('admin.lowongan.index');
    }

    public function edit(string $id): View
    {
        $lowongan = Lowongan::findOrFail($id);
        $employmentTypes = EmploymentType::toArray();
        $statuses = LowonganStatus::toArray();

        return view('admin.lowongan.create', compact('lowongan', 'employmentTypes', 'statuses'));
    }

    public function update(LowonganRequest $request, string $id)
    {
        $lowongan = Lowongan::findOrFail($id);
        $lowongan->update($request->validated());

        alert()->success('Success', 'Lowongan berhasil diubah!');

        return redirect()->route('admin.lowongan.index');
    }

    public function destroy(string $id)
    {
        $lowongan = Lowongan::findOrFail($id);
        $lowongan->delete();

        alert()->success('Success', 'Lowongan berhasil dihapus!');

        return redirect()->route('admin.lowongan.index');
    }
}
