<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PageRequest;
use App\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PageController extends Controller
{
    public function data()
    {
        $data = Page::query()->ordered();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-green-500 text-white px-2 py-1 rounded">Aktif</span>'
                    : '<span class="badge bg-gray-500 text-white px-2 py-1 rounded">Tidak Aktif</span>';
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.page.edit', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 transition-colors duration-200"><i class="bx bx-edit mr-1"></i> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.page.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$edit.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'is_active'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.page.index');
    }

    public function create(): View
    {
        return view('admin.page.create');
    }

    public function store(PageRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request) {
                unset($validated['featured_image'], $validated['images']);

                $page = Page::create($validated);

                if ($request->hasFile('featured_image')) {
                    $page->addMediaFromRequest('featured_image')
                        ->toMediaCollection('featured');
                }

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $page->addMedia($image)->toMediaCollection('images');
                    }
                }
            });

            alert()->success('Success', 'Halaman berhasil disimpan!');

            return redirect()->route('admin.page.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function edit(string $id): View
    {
        $page = Page::findOrFail($id);

        return view('admin.page.create', compact('page'));
    }

    public function update(PageRequest $request, string $id)
    {
        $page = Page::findOrFail($id);
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request, $page) {
                unset($validated['featured_image'], $validated['images']);

                $page->update($validated);

                if ($request->hasFile('featured_image')) {
                    $page->clearMediaCollection('featured');
                    $page->addMediaFromRequest('featured_image')
                        ->toMediaCollection('featured');
                }

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $page->addMedia($image)->toMediaCollection('images');
                    }
                }
            });

            alert()->success('Success', 'Halaman berhasil diubah!');

            return redirect()->route('admin.page.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $page = Page::findOrFail($id);
        $page->clearMediaCollection('featured');
        $page->clearMediaCollection('images');
        $page->delete();

        alert()->success('Success', 'Halaman berhasil dihapus!');

        return redirect()->route('admin.page.index');
    }

    public function deleteImage(string $id, string $mediaId)
    {
        $page = Page::findOrFail($id);
        $media = $page->getMedia('images')->where('id', $mediaId)->first();

        if ($media) {
            $media->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
