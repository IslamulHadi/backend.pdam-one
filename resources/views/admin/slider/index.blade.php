<x-layouts.auth title="Slider">
    <div class="container-xxl flex-grow-1 container-p-y">

        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Slider</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Data Slider Homepage</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Slider Homepage</h5>
                        <small class="text-muted float-end">
                            <a href="{{ route('admin.slider.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-custom text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <i class="bx bx-plus mr-2"></i> Tambah Data
                            </a>
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="overflow-x-auto">
                            <table class="datatable w-full">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 15%">Gambar</th>
                                        <th>Judul</th>
                                        <th style="width: 8%">Urutan</th>
                                        <th style="width: 15%">Periode</th>
                                        <th style="width: 10%">Status</th>
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
                    url: "{{ route('admin.slider.index') }}",
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
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'display_order',
                        name: 'display_order'
                    },
                    {
                        data: 'period',
                        name: 'period',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'is_active',
                        name: 'is_active'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        </script>
    @endpush

</x-layouts.auth>
