<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DownloadCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\DownloadRequest;
use App\Models\Download;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DownloadController extends Controller
{
    public function data()
    {
        $data = Download::query()->ordered();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('category', function ($row) {
                $colors = [
                    'formulir' => 'bg-blue-500',
                    'peraturan' => 'bg-red-500',
                    'panduan' => 'bg-green-500',
                    'lainnya' => 'bg-gray-500',
                ];
                $color = $colors[$row->category->value] ?? 'bg-gray-500';

                return '<span class="badge '.$color.' text-white px-2 py-1 rounded">'.e($row->category->getLabel()).'</span>';
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-green-500 text-white px-2 py-1 rounded">Aktif</span>'
                    : '<span class="badge bg-gray-500 text-white px-2 py-1 rounded">Tidak Aktif</span>';
            })
            ->addColumn('file_info', function ($row) {
                $ext = $row->file_extension;
                $size = $row->file_size;
                if ($ext && $size) {
                    return '<span class="text-sm text-gray-600">'.$ext.' - '.$size.'</span>';
                }

                return '<span class="text-gray-400">-</span>';
            })
            ->addColumn('downloads', function ($row) {
                return '<span class="text-sm">'.$row->download_count.' unduhan</span>';
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.download.edit', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 transition-colors duration-200"><i class="bx bx-edit mr-1"></i> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.download.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$edit.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'is_active', 'category', 'file_info', 'downloads'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.download.index');
    }

    public function create(): View
    {
        $categories = DownloadCategory::toArray();

        return view('admin.download.create', compact('categories'));
    }

    public function store(DownloadRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request) {
                unset($validated['file']);

                $download = Download::create($validated);

                if ($request->hasFile('file')) {
                    $download->addMediaFromRequest('file')
                        ->toMediaCollection('files');
                }
            });

            alert()->success('Success', 'File download berhasil disimpan!');

            return redirect()->route('admin.download.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function edit(string $id): View
    {
        $download = Download::findOrFail($id);
        $categories = DownloadCategory::toArray();

        return view('admin.download.create', compact('download', 'categories'));
    }

    public function update(DownloadRequest $request, string $id)
    {
        $download = Download::findOrFail($id);
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $request, $download) {
                unset($validated['file']);

                $download->update($validated);

                if ($request->hasFile('file')) {
                    $download->clearMediaCollection('files');
                    $download->addMediaFromRequest('file')
                        ->toMediaCollection('files');
                }
            });

            alert()->success('Success', 'File download berhasil diubah!');

            return redirect()->route('admin.download.index');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $download = Download::findOrFail($id);
        $download->clearMediaCollection('files');
        $download->delete();

        alert()->success('Success', 'File download berhasil dihapus!');

        return redirect()->route('admin.download.index');
    }
}
