@push('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-select/bootstrap-select.css') }}">
@endpush

@push('js')
    <script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script>
        $('.selectpicker').selectpicker()
    </script>
@endpush
