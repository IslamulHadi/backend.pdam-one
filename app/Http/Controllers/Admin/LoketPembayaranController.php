<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\LoketType;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoketPembayaranRequest;
use App\Models\LoketPembayaran;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class LoketPembayaranController extends Controller
{
    public function data()
    {
        $data = LoketPembayaran::query()->ordered();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('type', function ($row) {
                $colors = [
                    'bank' => 'bg-blue-500',
                    'minimarket' => 'bg-green-500',
                    'kantor_pdam' => 'bg-cyan-500',
                    'online' => 'bg-purple-500',
                    'ppob' => 'bg-orange-500',
                ];
                $color = $colors[$row->type->value] ?? 'bg-gray-500';

                return '<span class="badge '.$color.' text-white px-2 py-1 rounded">'.e($row->type->getLabel()).'</span>';
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-green-500 text-white px-2 py-1 rounded">Aktif</span>'
                    : '<span class="badge bg-gray-500 text-white px-2 py-1 rounded">Tidak Aktif</span>';
            })
            ->addColumn('location', function ($row) {
                if ($row->hasCoordinates()) {
                    return '<a href="'.$row->google_maps_url.'" target="_blank" class="text-blue-600 hover:underline">Lihat Peta</a>';
                }

                return '<span class="text-gray-400">-</span>';
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.loket-pembayaran.edit', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 transition-colors duration-200"><i class="bx bx-edit mr-1"></i> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.loket-pembayaran.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$edit.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'is_active', 'type', 'location'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.loket-pembayaran.index');
    }

    public function create(): View
    {
        $types = LoketType::toArray();

        return view('admin.loket-pembayaran.create', compact('types'));
    }

    public function store(LoketPembayaranRequest $request)
    {
        $validated = $request->validated();

        try {
            LoketPembayaran::create($validated);

            alert()->success('Success', 'Data loket pembayaran berhasil disimpan!');

            return redirect()->route('admin.loket-pembayaran.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function edit(string $id): View
    {
        $loketPembayaran = LoketPembayaran::findOrFail($id);
        $types = LoketType::toArray();

        return view('admin.loket-pembayaran.create', compact('loketPembayaran', 'types'));
    }

    public function update(LoketPembayaranRequest $request, string $id)
    {
        $loketPembayaran = LoketPembayaran::findOrFail($id);
        $validated = $request->validated();

        try {
            $loketPembayaran->update($validated);

            alert()->success('Success', 'Data loket pembayaran berhasil diubah!');

            return redirect()->route('admin.loket-pembayaran.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $loketPembayaran = LoketPembayaran::findOrFail($id);
        $loketPembayaran->delete();

        alert()->success('Success', 'Data loket pembayaran berhasil dihapus!');

        return redirect()->route('admin.loket-pembayaran.index');
    }
}
