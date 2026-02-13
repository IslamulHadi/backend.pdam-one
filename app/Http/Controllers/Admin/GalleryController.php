<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\GalleryType;
use App\Http\Controllers\Controller;
use App\Http\Requests\GalleryRequest;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class GalleryController extends Controller
{
    public function data()
    {
        $data = Gallery::query()->ordered();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('type', function ($row) {
                $color = $row->type === GalleryType::Foto ? 'bg-blue-500' : 'bg-purple-500';

                return '<span class="badge '.$color.' text-white px-2 py-1 rounded">'.e($row->type->getLabel()).'</span>';
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-green-500 text-white px-2 py-1 rounded">Aktif</span>'
                    : '<span class="badge bg-gray-500 text-white px-2 py-1 rounded">Tidak Aktif</span>';
            })
            ->addColumn('thumbnail', function ($row) {
                $url = $row->getFirstMediaUrl('thumbnail') ?: $row->getFirstMediaUrl('images');
                if ($url) {
                    return '<img src="'.$url.'" alt="Thumbnail" class="h-12 w-12 object-cover rounded">';
                }

                return '<span class="text-gray-400">No Image</span>';
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.galeri.edit', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"><i class="bx bx-edit mr-1"></i> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.galeri.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$edit.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'is_active', 'type', 'thumbnail'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.gallery.index');
    }

    public function create(): View
    {
        $types = GalleryType::toArray();

        return view('admin.gallery.create', compact('types'));
    }

    public function store(GalleryRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request) {
                unset($validated['images'], $validated['thumbnail']);

                $gallery = Gallery::create($validated);

                if ($request->hasFile('thumbnail')) {
                    $gallery->addMediaFromRequest('thumbnail')
                        ->toMediaCollection('thumbnail');
                }

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $gallery->addMedia($image)
                            ->toMediaCollection('images');
                    }
                }
            });

            alert()->success('Success', 'Data galeri berhasil disimpan!');

            return redirect()->route('admin.galeri.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function edit(string $id): View
    {
        $gallery = Gallery::findOrFail($id);
        $types = GalleryType::toArray();

        return view('admin.gallery.create', compact('gallery', 'types'));
    }

    public function update(GalleryRequest $request, string $id)
    {
        $gallery = Gallery::findOrFail($id);
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request, $gallery) {
                unset($validated['images'], $validated['thumbnail']);

                $gallery->update($validated);

                if ($request->hasFile('thumbnail')) {
                    $gallery->clearMediaCollection('thumbnail');
                    $gallery->addMediaFromRequest('thumbnail')
                        ->toMediaCollection('thumbnail');
                }

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $gallery->addMedia($image)
                            ->toMediaCollection('images');
                    }
                }
            });

            alert()->success('Success', 'Data galeri berhasil diubah!');

            return redirect()->route('admin.galeri.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $gallery = Gallery::findOrFail($id);
        $gallery->clearMediaCollection('images');
        $gallery->clearMediaCollection('thumbnail');
        $gallery->delete();

        alert()->success('Success', 'Data galeri berhasil dihapus!');

        return redirect()->route('admin.galeri.index');
    }

    public function deleteImage(string $id, string $mediaId): JsonResponse
    {
        $gallery = Gallery::findOrFail($id);
        $media = $gallery->getMedia('images')->where('id', $mediaId)->first();

        if ($media) {
            $media->delete();

            return response()->json(['success' => true, 'message' => 'Gambar berhasil dihapus']);
        }

        return response()->json(['success' => false, 'message' => 'Gambar tidak ditemukan'], 404);
    }
}
