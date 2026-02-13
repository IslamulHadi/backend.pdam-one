@push('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/quill/editor.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/quill/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/quill/katex.css') }}">
@endpush()

@push('js')
    <script src="{{ asset('assets/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/js/quilljs_textarea.js') }}"></script>
    <script>

        const fullToolbar = [
            [
                {
                    font: ['monospace']
                },
                {
                    size: []
                }
            ],
            ['bold', 'italic', 'underline', 'strike'],
            [
                {
                    color: []
                },
                {
                    background: []
                }
            ],
            [
                {
                    script: 'super'
                },
                {
                    script: 'sub'
                }
            ],
            [
                {
                    header: '1'
                },
                {
                    header: '2'
                },
                'blockquote',
            ],
            [
                {
                    list: 'ordered'
                },
                {
                    list: 'bullet'
                },
                {
                    indent: '-1'
                },
                {
                    indent: '+1'
                }
            ],
            ['link'],
            ['clean']
        ];

        const options =  {
            placeholder: 'Type Something...',
            modules: {
            formula: true,
                toolbar: fullToolbar
            },
            theme: 'snow',

        };

        quilljs_textarea('.quillEditor',options);

    </script>
@endpush
