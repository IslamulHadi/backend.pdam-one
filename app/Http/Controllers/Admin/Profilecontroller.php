<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Profilecontroller extends Controller
{
    public function index($tab)
    {
        return view('profile', compact('tab'));
    }

    public function update(ProfileRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                auth()->user()->update([
                    'nama' => $request->nama,
                    'no_telp' => $request->no_telp,
                ]);

                if ($request->hasFile('avatar')) {
                    auth()->user()->getFirstMedia('avatars')?->delete();
                    auth()->user()
                        ->addMediaFromRequest('avatar')
                        ->usingFileName(Str::slug(auth()->user()->nama.'-id-'.auth()->id()).'.'.$request->file('avatar')->extension())
                        ->toMediaCollection('avatars');
                }
            });
            alert()->success('Success', 'Data berhasil disimpan!');

            return redirect()->route('admin.profile', 'data');
        } catch (\Exception $e) {
            alert()->error('Ooppss!', 'Proses simpan data gagal!');

            return redirect()->route('admin.profile', 'data')->withErrors($e->getMessage())->withInput();
        }
    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        try {
            auth()->user()->update([
                'password' => Hash::make($request->password),
            ]);
            alert()->success('Success', 'Password berhasil diubah!');

            return redirect()->route('admin.profile', 'settings');
        } catch (\Exception $e) {
            alert()->error('Ooppss!', 'Proses simpan data gagal!');

            return redirect()->route('admin.profile', 'settings')->withErrors($e->getMessage())->withInput();
        }
    }
}
