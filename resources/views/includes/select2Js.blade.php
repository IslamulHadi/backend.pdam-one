
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/select2.css') }}">
@endpush

@push('js')
    <script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>
    <script>
        function escapeHtml(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        $('.select2').select2();
        $(document).on("select2:open",function (){
            $(".select2-search__field")[0].focus();
        })
        $(".select2-ajax").select2({
            ajax: {
                url: "{!! $url??null !!}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    let query = {
                        q: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text : "[ "+item.subtext+" ] "+item.text,
                                id : item.id,
                                html : "<div><strong>"+escapeHtml(item.subtext)+"</strong></div>"+
                                    "<div>"+escapeHtml(item.text)+"</div>"
                            }
                        })
                    };
                }
            },

            templateResult  : function (data){
                if (data.loading) return data.text;
                return data.html;
            },
            escapeMarkup : function (markup) {
                return markup;
            },
            minimumInputLength: 3,
        });
    </script>
@endpush
