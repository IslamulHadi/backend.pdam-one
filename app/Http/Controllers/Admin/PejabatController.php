<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PejabatLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\PejabatRequest;
use App\Models\Pejabat;
use App\Models\Sigap\Pegawai as SigapPegawai;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PejabatController extends Controller
{
    public function data()
    {
        $data = Pejabat::query()->withSigap()->ordered();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('level', function ($row) {
                $level = $row->display_level;

                if (! $level) {
                    return '<span class="badge bg-gray-500 text-white px-2 py-1 rounded">-</span>';
                }

                $color = match ($level) {
                    PejabatLevel::Direksi => 'bg-blue-500',
                    PejabatLevel::Kabid => 'bg-green-500',
                    PejabatLevel::Kasubid => 'bg-purple-500',
                };

                return '<span class="badge '.$color.' text-white px-2 py-1 rounded">'.e($level->getLabel()).'</span>';
            })
            ->editColumn('nama', function ($row) {
                return e($row->display_nama);
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-green-500 text-white px-2 py-1 rounded">Aktif</span>'
                    : '<span class="badge bg-gray-500 text-white px-2 py-1 rounded">Tidak Aktif</span>';
            })
            ->addColumn('sumber', function ($row) {
                return $row->isLinkedToSigap()
                    ? '<span class="badge bg-indigo-500 text-white px-2 py-1 rounded">Sigap</span>'
                    : '<span class="badge bg-gray-300 text-gray-700 px-2 py-1 rounded">Manual</span>';
            })
            ->addColumn('foto', function ($row) {
                $url = $row->getFirstMediaUrl('foto');
                if ($url) {
                    return '<img src="'.$url.'" alt="Foto" class="h-12 w-12 object-cover rounded-full">';
                }

                return '<span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-gray-200 text-gray-500"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>';
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.pejabat.edit', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 transition-colors duration-200"><i class="bx bx-edit mr-1"></i> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.pejabat.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$edit.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'is_active', 'level', 'foto', 'sumber'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.pejabat.index');
    }

    public function create(): View
    {
        $levels = PejabatLevel::toArray();
        $pegawaiList = $this->getPegawaiList();

        return view('admin.pejabat.create', compact('levels', 'pegawaiList'));
    }

    public function store(PejabatRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request) {
                unset($validated['foto']);

                // If linked to sigap, fill nama/jabatan/level/bidang from sigap data
                if (! empty($validated['pegawai_id'])) {
                    $validated = $this->fillFromSigap($validated);
                }

                $pejabat = Pejabat::create($validated);

                if ($request->hasFile('foto')) {
                    $pejabat->addMediaFromRequest('foto')
                        ->toMediaCollection('foto');
                }
            });

            alert()->success('Success', 'Data pejabat berhasil disimpan!');

            return redirect()->route('admin.pejabat.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function edit(string $id): View
    {
        $pejabat = Pejabat::findOrFail($id);
        $levels = PejabatLevel::toArray();
        $pegawaiList = $this->getPegawaiList();

        return view('admin.pejabat.create', compact('pejabat', 'levels', 'pegawaiList'));
    }

    public function update(PejabatRequest $request, string $id)
    {
        $pejabat = Pejabat::findOrFail($id);
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request, $pejabat) {
                unset($validated['foto']);

                // If linked to sigap, fill nama/jabatan/level/bidang from sigap data
                if (! empty($validated['pegawai_id'])) {
                    $validated = $this->fillFromSigap($validated);
                }

                $pejabat->update($validated);

                if ($request->hasFile('foto')) {
                    $pejabat->clearMediaCollection('foto');
                    $pejabat->addMediaFromRequest('foto')
                        ->toMediaCollection('foto');
                }
            });

            alert()->success('Success', 'Data pejabat berhasil diubah!');

            return redirect()->route('admin.pejabat.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $pejabat = Pejabat::findOrFail($id);
        $pejabat->clearMediaCollection('foto');
        $pejabat->delete();

        alert()->success('Success', 'Data pejabat berhasil dihapus!');

        return redirect()->route('admin.pejabat.index');
    }

    /**
     * Get pegawai list from sigap for dropdown (Direksi, Kabid, Kasubid only).
     *
     * @return array<int, array<string, mixed>>
     */
    private function getPegawaiList(): array
    {
        try {
            return SigapPegawai::query()
                ->active()
                ->pejabat()
                ->with(['jabatan', 'bagian'])
                ->orderBy('nama')
                ->get()
                ->map(fn (SigapPegawai $p) => [
                    'id' => $p->id,
                    'nama' => $p->nama,
                    'jabatan' => $p->jabatan?->nama ?? '-',
                    'level_jabatan' => $p->jabatan?->level_jabatan,
                    'level' => $p->pejabat_level?->value,
                    'bidang' => $p->bagian?->nama ?? '-',
                ])
                ->toArray();
        } catch (\Exception) {
            // If sigap database is unavailable, return empty
            return [];
        }
    }

    /**
     * Fill pejabat data from sigap pegawai.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function fillFromSigap(array $validated): array
    {
        try {
            $pegawai = SigapPegawai::with(['jabatan', 'bagian'])->find($validated['pegawai_id']);

            if ($pegawai) {
                $validated['nama'] = $pegawai->nama;
                $validated['jabatan'] = $pegawai->jabatan?->nama ?? $validated['jabatan'] ?? '';
                $validated['level'] = $pegawai->pejabat_level?->value ?? $validated['level'] ?? null;
                $validated['bidang'] = $pegawai->bagian?->nama ?? $validated['bidang'] ?? null;
            }
        } catch (\Exception) {
            // Sigap unavailable, keep manual values
        }

        return $validated;
    }
}
