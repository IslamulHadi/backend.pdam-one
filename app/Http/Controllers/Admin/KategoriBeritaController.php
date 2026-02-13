<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriBeritaRequest;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KategoriBeritaController extends Controller
{
    public function data()
    {
        $data = Kategori::query()->orderBy('nama');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $edit = '<a href="' . route('admin.kategori-berita.edit', $row->id) . '" class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"> Edit</a>';
                $delete = '<a href="#" data-delete-url="' . route('admin.kategori-berita.destroy', $row->id) . '" class="btn-delete inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"> Hapus</a>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.kategori-berita.index');
    }

    public function edit(Kategori $kategori)
    {
        return view('admin.kategori-berita.create', compact('kategori'));
    }

    public function create()
    {
        return view('admin.kategori-berita.create');
    }

    public function store(KategoriBeritaRequest $request)
    {
        $validated = $request->validated();
        try {
            DB::transaction(function () use ($validated) {
                Kategori::create($validated);
            });

            // Cara 1: Simple - Title dan Text
            alert()->success('Berhasil!', 'Data kategori berita berhasil disimpan.');

            // Cara 2: Dengan timer (auto close setelah beberapa detik)
            // alert()->success('Berhasil!', 'Data kategori berita berhasil disimpan.')
            //     ->timer(3000); // Auto close setelah 3 detik

            // Cara 3: Custom button text
            // alert()->success('Berhasil!', 'Data kategori berita berhasil disimpan.')
            //     ->confirmButtonText('OK, Mengerti!');

            // Cara 4: Dengan options lengkap
            // alert()->success('Berhasil!', 'Data kategori berita berhasil disimpan.')
            //     ->timer(5000)
            //     ->confirmButtonText('Baik')
            //     ->showConfirmButton(true);

            return redirect()->route('admin.kategori-berita.index');
        } catch (\Exception $e) {
            // Custom error message dengan detail
            alert()->error('Gagal!', 'Proses simpan data gagal: ' . $e->getMessage());

            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function update(KategoriBeritaRequest $request, Kategori $kategori)
    {
        $validated = $request->validated();
        try {
            DB::transaction(function () use ($validated, $kategori) {
                $kategori->update($validated);
            });

            alert()->success('Berhasil!', 'Data kategori berita berhasil diubah.');

            return redirect()->route('admin.kategori-berita.index');
        } catch (\Exception $e) {
            alert()->error('Gagal!', 'Proses ubah data gagal: ' . $e->getMessage());

            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy(Kategori $kategori)
    {
        try {
            $kategori->delete();
            alert()->success('Berhasil!', 'Data kategori berita berhasil dihapus.');

            return redirect()->route('admin.kategori-berita.index');
        } catch (\Exception $e) {
            alert()->error('Gagal!', 'Proses hapus data gagal: ' . $e->getMessage());

            return back()->withInput()->withErrors($e->getMessage());
        }
    }
}
