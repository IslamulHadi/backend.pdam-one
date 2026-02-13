<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BeritaRequest;
use App\Models\Berita;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BeritaController extends Controller
{
    public function data()
    {
        $data = Berita::query()->orderByDesc('published_at');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('kategori', function ($row) {
                return $row->kategori->map(function ($kategori) {
                    return '<span class="badge bg-primary">'.$kategori->nama.'</span>';
                })->implode(' ');
            })
            ->editColumn('is_featured', function ($row) {
                return $row->is_featured ? '<span class="badge bg-primary">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>';
            })
            ->editColumn('view_count', function ($row) {
                return number_format($row->view_count ?? 0);
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.input-berita.edit', $row->id).'" class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.input-berita.destroy', $row->id).'" class="btn-delete inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"> Hapus</a>';

                return $edit.' '.$delete;
            })
            ->rawColumns(['action', 'kategori', 'is_featured'])
            ->make(true);
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.berita.index');
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();

        return view('admin.berita.create', compact('kategoris'));
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('image');
        $filename = Str::random(40).'.'.$image->getClientOriginalExtension();
        $path = $image->storeAs('berita/content', $filename, 'public');

        $url = Storage::url($path);

        return response()->json([
            'success' => true,
            'url' => asset($url),
        ]);
    }

    public function store(BeritaRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request) {
                $kategoriIds = $validated['kategori_ids'] ?? [];
                unset($validated['kategori_ids']);

                unset($validated['thumbnail']);

                $berita = Berita::create($validated);
                $berita->kategori()->sync($kategoriIds);

                if ($request->hasFile('thumbnail')) {
                    $berita->clearMediaCollection('thumbnails');
                    $berita->addMediaFromRequest('thumbnail')
                        ->toMediaCollection('thumbnails');
                }
            });

            alert()->success('Success', 'Data berhasil disimpan!');

            return redirect()->route('admin.input-berita.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function edit(Berita $berita)
    {
        $berita->load('kategori');
        $kategoris = Kategori::orderBy('nama')->get();

        return view('admin.berita.create', compact('berita', 'kategoris'));
    }

    public function update(BeritaRequest $request, Berita $berita)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request, $berita) {
                $kategoriIds = $validated['kategori_ids'] ?? [];
                unset($validated['kategori_ids']);

                unset($validated['thumbnail']);

                $berita->update($validated);

                $berita->kategori()->sync($kategoriIds);

                if ($request->hasFile('thumbnail')) {
                    $berita->clearMediaCollection('thumbnails');
                    $berita->addMediaFromRequest('thumbnail')
                        ->toMediaCollection('thumbnails');
                }
            });

            alert()->success('Success', 'Data berhasil diubah!');

            return redirect()->route('admin.input-berita.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }
}
