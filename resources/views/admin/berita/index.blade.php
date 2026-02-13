<x-layouts.auth title="Berita">
    <div class="container-xxl flex-grow-1 container-p-y">

        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Berita</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Data Berita</flux:breadcrumbs.item>
        </flux:breadcrumbs>


        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Berita</h5>
                        <small class="text-muted float-end"><a href="{{ route('admin.input-berita.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-custom text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"><i
                                    class="bx bx-plus mr-2"></i> Tambah Data</a></small>
                    </div>
                    <div class="card-body">
                        <div class="overflow-x-auto">
                            <table class="datatable w-full">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>Judul</th>
                                        <th>Tanggal</th>
                                        <th>Kategori</th>
                                        <th>Berita Utama</th>
                                        <th>Dilihat</th>
                                        <th style="width: 10%">Aksi</th>
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
                    url: "{{ route('admin.input-berita.index') }}",
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX Error:', error, thrown);
                        console.error('Response:', xhr.responseText);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'published_at',
                        name: 'published_at'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'is_featured',
                        name: 'is_featured'
                    },
                    {
                        data: 'view_count',
                        name: 'view_count',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'w-10'
                    }
                ],
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            })
        </script>
    @endpush

</x-layouts.auth>
