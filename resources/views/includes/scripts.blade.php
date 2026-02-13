<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('assets/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script src="{{ asset('assets/libs/hammer/hammer.js') }}"></script>

<script src="{{ asset('assets/js/menu.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Page JS -->
@stack('js')
