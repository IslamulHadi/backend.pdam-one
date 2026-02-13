<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SliderRequest;
use App\Models\Slider;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SliderController extends Controller
{
    public function data()
    {
        $data = Slider::query()->ordered();

        return DataTables::of($data)
            ->addIndexColumn()
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
            ->addColumn('image', function ($row) {
                $url = $row->getFirstMediaUrl('image');
                if ($url) {
                    return '<img src="'.$url.'" alt="Slider" class="h-16 w-28 object-cover rounded">';
                }

                return '<span class="text-gray-400">No Image</span>';
            })
            ->addColumn('period', function ($row) {
                $start = $row->start_date ? $row->start_date->format('d/m/Y') : '-';
                $end = $row->end_date ? $row->end_date->format('d/m/Y') : '-';

                return $start.' s/d '.$end;
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.slider.edit', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 transition-colors duration-200"><i class="bx bx-edit mr-1"></i> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.slider.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$edit.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'is_active', 'image'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.slider.index');
    }

    public function create(): View
    {
        return view('admin.slider.create');
    }

    public function store(SliderRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request) {
                unset($validated['image']);

                $slider = Slider::create($validated);

                if ($request->hasFile('image')) {
                    $slider->addMediaFromRequest('image')
                        ->toMediaCollection('image');
                }
            });

            alert()->success('Success', 'Slider berhasil disimpan!');

            return redirect()->route('admin.slider.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function edit(string $id): View
    {
        $slider = Slider::findOrFail($id);

        return view('admin.slider.create', compact('slider'));
    }

    public function update(SliderRequest $request, string $id)
    {
        $slider = Slider::findOrFail($id);
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request, $slider) {
                unset($validated['image']);

                $slider->update($validated);

                if ($request->hasFile('image')) {
                    $slider->clearMediaCollection('image');
                    $slider->addMediaFromRequest('image')
                        ->toMediaCollection('image');
                }
            });

            alert()->success('Success', 'Slider berhasil diubah!');

            return redirect()->route('admin.slider.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $slider = Slider::findOrFail($id);
        $slider->clearMediaCollection('image');
        $slider->delete();

        alert()->success('Success', 'Slider berhasil dihapus!');

        return redirect()->route('admin.slider.index');
    }
}
