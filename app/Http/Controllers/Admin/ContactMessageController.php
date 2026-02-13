<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageResponseMail;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ContactMessageController extends Controller
{
    public function data()
    {
        $data = ContactMessage::query()->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('name', function ($row) {
                $readIcon = $row->is_read
                    ? ''
                    : '<span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-2" title="Belum dibaca"></span>';

                return $readIcon.'<span class="font-medium text-gray-900">'.e($row->name).'</span>';
            })
            ->editColumn('category', function ($row) {
                $colors = [
                    'umum' => 'bg-blue-500',
                    'layanan' => 'bg-green-500',
                    'tagihan' => 'bg-yellow-500',
                    'kerjasama' => 'bg-purple-500',
                    'lainnya' => 'bg-gray-500',
                ];
                $color = $colors[$row->category] ?? 'bg-gray-500';
                $label = ucfirst($row->category);

                return '<span class="badge '.$color.' text-white px-2 py-1 rounded">'.e($label).'</span>';
            })
            ->addColumn('status', function ($row) {
                if ($row->responded_at) {
                    return '<span class="badge bg-green-500 text-white px-2 py-1 rounded">Direspon</span>';
                }
                if ($row->is_read) {
                    return '<span class="badge bg-yellow-500 text-white px-2 py-1 rounded">Dibaca</span>';
                }

                return '<span class="badge bg-red-500 text-white px-2 py-1 rounded">Baru</span>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i');
            })
            ->editColumn('message', function ($row) {
                return Str::limit($row->message, 60);
            })
            ->addColumn('action', function ($row) {
                $show = '<a href="'.route('admin.pesan-kontak.show', $row->id).'" class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"><i class="bx bx-show mr-1"></i> Lihat</a>';
                $delete = '<a href="#" data-delete-url="'.route('admin.pesan-kontak.destroy', $row->id).'" class="btn-delete inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"><i class="bx bx-trash mr-1"></i> Hapus</a>';

                return '<div class="flex gap-2">'.$show.' '.$delete.'</div>';
            })
            ->rawColumns(['action', 'category', 'status', 'name'])
            ->make(true);
    }

    public function index(): mixed
    {
        if (request()->ajax()) {
            return $this->data();
        }

        $unreadCount = ContactMessage::unread()->count();

        return view('admin.pesan-kontak.index', compact('unreadCount'));
    }

    public function show(string $id): View
    {
        $contactMessage = ContactMessage::with('respondedByUser')->findOrFail($id);
        $contactMessage->markAsRead();

        return view('admin.pesan-kontak.show', compact('contactMessage'));
    }

    public function respond(Request $request, string $id): RedirectResponse
    {
        $contactMessage = ContactMessage::findOrFail($id);

        $validated = $request->validate([
            'response' => ['required', 'string', 'min:10'],
        ], [
            'response.required' => 'Balasan wajib diisi.',
            'response.min' => 'Balasan minimal 10 karakter.',
        ]);

        $contactMessage->respond($validated['response'], (string) auth()->id());

        // TODO: implement email sending next
        // Mail::to($contactMessage->email)->send(new ContactMessageResponseMail($contactMessage));

        alert()->success('Success', 'Balasan berhasil dikirim!');

        return redirect()->route('admin.pesan-kontak.show', $contactMessage->id);
    }

    public function destroy(string $id): RedirectResponse
    {
        $contactMessage = ContactMessage::findOrFail($id);
        $contactMessage->delete();

        alert()->success('Success', 'Pesan kontak berhasil dihapus!');

        return redirect()->route('admin.pesan-kontak.index');
    }
}
