@push('js')
<script src="{{ asset('assets/libs/parsley/dist/parsley.min.js') }}"></script>
<script>
    const form = document.getElementById('needs-validation');
    $(form).parsley();
    window.Parsley.on('field:validated', function() {
        $(":submit").removeAttr("disabled").text("Simpan Data");
    });

    $(form).on('submit', function(e) {
        $(":submit").attr("disabled", true).text("Submitting..");
    });
</script>
@endpush
