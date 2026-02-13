@push('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/plugins/monthSelect/style.css">
@endpush

@push('js')
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="https://npmcdn.com/flatpickr@4.6.13/dist/l10n/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/plugins/monthSelect/index.js"></script>

    <script>
        $('.datepicker').flatpickr({
            altInput: true,
            altFormat: 'F j, Y',
            dateFormat: 'Y-m-d',
            locale: 'id'
        })

        $('.monthpicker').flatpickr({
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "m-Y",
                    locale: "id"
                })
            ]
        })

        $('.datetimepicker').flatpickr({
            altInput: true,
            altFormat: 'F j, Y H:i',
            dateFormat: 'Y-m-d H:i',
            enableTime: true,
            time_24hr: true
        })

        $('.datepicker-past').flatpickr({
            altInput: true,
            altFormat: 'F j, Y H:i',
            dateFormat: 'Y-m-d H:i',
            maxDate: 'today',
            enableTime: true,
            time_24hr: true
        })

        $('.datepicker-current').flatpickr({
            altInput: true,
            altFormat: 'F j, Y',
            dateFormat: 'Y-m-d H:i',
            minDate: 'today',
            enableTime: true,
            time_24hr: true
        })

        $('.timepicker').flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        })
    </script>
@endpush
