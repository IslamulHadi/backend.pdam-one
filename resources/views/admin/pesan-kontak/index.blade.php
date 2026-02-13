<x-layouts.auth title="Pesan Kontak">
    <div class="container-xxl flex-grow-1 container-p-y">

        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Pesan Kontak</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Data Pesan Masuk</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        @if ($unreadCount > 0)
            <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bx bx-envelope text-blue-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">
                            Anda memiliki <strong>{{ $unreadCount }}</strong> pesan yang belum dibaca.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Pesan Kontak</h5>
                    </div>
                    <div class="card-body">
                        <div class="overflow-x-auto">
                            <table class="datatable w-full">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>Pengirim</th>
                                        <th>Email</th>
                                        <th>Kategori</th>
                                        <th>Pesan</th>
                                        <th style="width: 10%">Status</th>
                                        <th style="width: 10%">Tanggal</th>
                                        <th style="width: 15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('css')
        <link rel="stylesheet" href="{{ asset('assets/css/dataTables.tailwindcss.css') }}">
    @endpush

    @push('js')
        <script src="{{ asset('assets/js/dataTables.js') }}"></script>
        <script src="{{ asset('assets/js/dataTables.tailwind.js') }}"></script>
        <script>
            let datatable = $(".datatable").DataTable({
                renderer: "tailwindcss",
                language: {
                    "info": "Data _START_ sampai _END_ dari _TOTAL_ data.",
                },
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('admin.pesan-kontak.index') }}",
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX Error:', error, thrown);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'message',
                        name: 'message'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [6, 'desc']
                ]
            });
        </script>
    @endpush

</x-layouts.auth>
