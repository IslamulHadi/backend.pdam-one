{{-- <script src="{{asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script> --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.css') }}">
@endpush
@push('js')
    <script>
        let statusDelete = null;

        window.Livewire.on('showDialog', (icon = "warning", title, text) => {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                customClass: {
                    confirmButton: 'btn btn-primary me-3 w-300',
                    title: 'mb-0 p-0',
                    icon: 'w-20 h-20'
                },
                buttonsStyling: false
            })
        })

        window.addEventListener('showDialog', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: event.detail.icon,
                customClass: {
                    confirmButton: 'btn btn-primary me-3 w-300',
                    title: 'mb-0 p-0',
                    icon: 'w-20 h-20'
                },
                buttonsStyling: false
            })
        })

        function deleteData(id) {
            Swal.fire({
                title: "Yakin akan dihapus ?",
                text: "Data akan dihapus secara permanent!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary me-3 w-300',
                    cancelButton: 'btn btn-danger',
                    title: 'mb-0 p-0',
                    icon: 'w-20 h-20'
                },
                buttonsStyling: false
            }).then(function(res) {
                if (res.value) {
                    Livewire.emit('delete', id);
                }

            });
        }

        function fn_deleteData(url) {
            Swal.fire({
                title: "Yakin akan dihapus ?",
                text: "Data akan dihapus secara permanent!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary me-3 w-300',
                    cancelButton: 'btn btn-danger',
                    title: 'mb-0 p-0',
                    icon: 'w-20 h-20'
                },
                buttonsStyling: false
            }).then(function(res) {
                if (res.value) {
                    token = '{{ csrf_token() }}';
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        dataType: "JSON",
                        data: {
                            "_method": 'DELETE',
                            "_token": token,
                        },
                        success: function(respon) {
                            console.log(respon);
                            statusDelete = ("error" in respon) ? 'error' : 'success'

                        },
                        error: function(xhr) {
                            console.log(xhr);
                            statusDelete = 'network_error'
                        }
                    });

                    setTimeout(function() {

                        if (statusDelete === 'error')
                            errorDelete();

                        if (statusDelete === 'network_error')
                            networkError();

                        if (statusDelete === 'success')
                            successDelete();

                    }, 1000);

                }

            });
        }


        function verifikasiData(url) {
            Swal.fire({
                title: "Yakin akan diverifikasi ?",
                text: "Data akan diverifikasi secara permanent!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Yes, verify it!',
                customClass: {
                    confirmButton: 'btn btn-primary me-3 w-300',
                    cancelButton: 'btn btn-danger',
                    title: 'mb-0 p-0',
                    icon: 'w-20 h-20'
                },
                buttonsStyling: false
            }).then(function(res) {
                if (res.value) {
                    token = '{{ csrf_token() }}';
                    $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: "JSON",
                        data: {
                            "_token": token,
                        },
                        success: function(respon) {
                            console.log(respon);
                            statusDelete = ("error" in respon) ? 'error' : 'success'

                        },
                        error: function(xhr) {
                            console.log(xhr);
                            statusDelete = 'network_error'
                        }
                    });

                    setTimeout(function() {

                        if (statusDelete === 'error')
                            errorVerifikasi();

                        if (statusDelete === 'network_error')
                            networkError();

                        if (statusDelete === 'success')
                            successVerifikasi();

                    }, 1000);

                }

            });
        }

        function reloadTable() {

            console.log(statusDelete);
            if (typeof datatable === 'undefined') {
                window.location.reload();
            } else {
                datatable.draw();
            }
        }

        function errorDelete() {
            Swal.fire({
                title: "Proses Hapus Gagal !",
                text: "Pastikan data tidak digunakan oleh data lain.!",
                icon: "warning",
            }).then(() => reloadTable());
        }

        function networkError() {
            Swal.fire({
                title: "Ooops ! Koneksi Terputus",
                text: "Proses hapus gagal, silahkan ulangi proses",
                icon: "error",
            }).then(() => reloadTable());
        }

        function successDelete() {
            Swal.fire({
                title: "Hapus Data Berhasil !",
                text: "Pastikan data tidak digunakan oleh data lain.!",
                icon: "success",
            }).then(() => reloadTable());
        }

        function errorVerifikasi() {
            Swal.fire({
                title: "Proses Verifikasi Gagal !",
                text: "Pastikan data dapat diverifikasi.!",
                icon: "warning",
            }).then(() => reloadTable());
        }

        function successVerifikasi() {
            Swal.fire({
                title: "Verifikasi Data Berhasil !",
                text: "Data telah berhasil diverifikasi.!",
                icon: "success",
            }).then(() => reloadTable());
        }
    </script>
@endpush
