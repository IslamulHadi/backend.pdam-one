<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqRequest;
use App\Models\Faq;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class FaqController extends Controller
{
    public function data()
    {
        $data = Faq::query()->ordered();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-green-500 text-white px-2 py-1 rounded">Aktif</span>'
                    : '<span class="badge bg-gray-500 text-white px-2 py-1 rounded">Tidak Aktif</span>';
            })
            ->editColumn('category', function ($row) {
                return $row->category
                    ? '<span class="badge bg-blue-500 text-white px-2 py-1 rounded">'.e($row->category->getLabel()).'</span>'
                    : '-';
            })
            ->editColumn('question', function ($row) {
                return \Illuminate\Support\Str::limit($row->question, 80);
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.faq.edit', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"><i class="bx bx-edit mr-1"></i> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.faq.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$edit.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'is_active', 'category'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.faq.index');
    }

    public function create(): View
    {
        return view('admin.faq.create');
    }

    public function store(FaqRequest $request)
    {
        $validated = $request->validated();

        Faq::create($validated);

        alert()->success('Success', 'FAQ berhasil ditambahkan!');

        return redirect()->route('admin.faq.index');
    }

    public function edit(string $id): View
    {
        $faq = Faq::findOrFail($id);

        return view('admin.faq.create', compact('faq'));
    }

    public function update(FaqRequest $request, string $id)
    {
        $faq = Faq::findOrFail($id);
        $validated = $request->validated();

        $faq->update($validated);

        alert()->success('Success', 'FAQ berhasil diubah!');

        return redirect()->route('admin.faq.index');
    }

    public function destroy(string $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        alert()->success('Success', 'FAQ berhasil dihapus!');

        return redirect()->route('admin.faq.index');
    }
}
