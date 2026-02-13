<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CompanyInfoKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyInfoRequest;
use App\Models\CompanyInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CompanyInfoController extends Controller
{
    public function data()
    {
        $data = CompanyInfo::query()->orderBy('group')->orderBy('key');

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('key', function ($row) {
                $enum = CompanyInfoKey::tryFrom($row->key);
                $label = $enum?->getLabel() ?? $row->key;

                return '<div><span class="font-medium text-gray-900">'
                    .e($label).'</span><br><code class="text-xs text-gray-500">'
                    .e($row->key).'</code></div>';
            })
            ->editColumn('group', function ($row) {
                $colors = [
                    'contact' => 'bg-blue-500',
                    'social' => 'bg-purple-500',
                    'about' => 'bg-green-500',
                    'operation' => 'bg-orange-500',
                ];
                $color = $colors[$row->group] ?? 'bg-gray-500';

                return $row->group
                    ? '<span class="badge '.$color.' text-white px-2 py-1 rounded">'.e(ucfirst($row->group)).'</span>'
                    : '<span class="badge bg-gray-400 text-white px-2 py-1 rounded">-</span>';
            })
            ->editColumn('value', function ($row) {
                return \Illuminate\Support\Str::limit($row->value, 80);
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="'.route('admin.company-info.edit', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"><i class="bx bx-edit mr-1"></i> Edit</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.company-info.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$edit.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'group', 'key'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        return view('admin.company-info.index');
    }

    public function create(): View
    {
        $availableKeys = $this->getAvailableKeys();

        return view('admin.company-info.create', compact('availableKeys'));
    }

    public function store(CompanyInfoRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $enum = CompanyInfoKey::from($validated['key']);

        CompanyInfo::create([
            'key' => $validated['key'],
            'value' => $validated['value'],
            'group' => $enum->getGroup(),
        ]);

        Cache::forget("company_info.{$validated['key']}");
        Cache::forget("company_info.group.{$enum->getGroup()}");

        alert()->success('Success', 'Info Perusahaan berhasil ditambahkan!');

        return redirect()->route('admin.company-info.index');
    }

    public function edit(string $id): View
    {
        $companyInfo = CompanyInfo::findOrFail($id);
        $availableKeys = $this->getAvailableKeys($companyInfo->key);

        return view('admin.company-info.create', compact('companyInfo', 'availableKeys'));
    }

    public function update(CompanyInfoRequest $request, string $id): RedirectResponse
    {
        $companyInfo = CompanyInfo::findOrFail($id);
        $validated = $request->validated();
        $enum = CompanyInfoKey::from($validated['key']);

        // Clear old cache
        Cache::forget("company_info.{$companyInfo->key}");
        if ($companyInfo->group) {
            Cache::forget("company_info.group.{$companyInfo->group}");
        }

        $companyInfo->update([
            'key' => $validated['key'],
            'value' => $validated['value'],
            'group' => $enum->getGroup(),
        ]);

        // Clear new cache
        Cache::forget("company_info.{$validated['key']}");
        Cache::forget("company_info.group.{$enum->getGroup()}");

        alert()->success('Success', 'Info Perusahaan berhasil diubah!');

        return redirect()->route('admin.company-info.index');
    }

    public function destroy(string $id): RedirectResponse
    {
        $companyInfo = CompanyInfo::findOrFail($id);

        Cache::forget("company_info.{$companyInfo->key}");
        if ($companyInfo->group) {
            Cache::forget("company_info.group.{$companyInfo->group}");
        }

        $companyInfo->delete();

        alert()->success('Success', 'Info Perusahaan berhasil dihapus!');

        return redirect()->route('admin.company-info.index');
    }

    /**
     * Get available keys (excluding already used ones).
     *
     * @return array<string, array<string, string>>
     */
    private function getAvailableKeys(?string $currentKey = null): array
    {
        $usedKeys = CompanyInfo::pluck('key')->toArray();

        $grouped = [];
        foreach (CompanyInfoKey::cases() as $case) {
            if ($case->value === $currentKey || ! in_array($case->value, $usedKeys)) {
                $group = $case->getGroup();
                $grouped[$group][$case->value] = $case->getLabel();
            }
        }

        return $grouped;
    }
}
