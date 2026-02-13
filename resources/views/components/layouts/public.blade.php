<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'PDAM Smart' }} - Air Bersih, Layanan Modern</title>

    <link rel="icon" type="image/png" href="{{ asset('pdam.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|inter:400,500,600,700"
        rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- Select2 CSS (already in app.js, but ensure it loads) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body class="pdam-page antialiased">
    <!-- Header -->
    <header class="pdam-header">
        <div class="pdam-container pdam-header-inner">
            <a href="{{ route('home') }}" class="pdam-logo">
                <div class="pdam-logo-image">
                    <img src="{{ asset('pdam.png') }}" alt="PDAM Amerta Toraya">
                </div>
                <span>PDAM Amerta Toraya</span>
            </a>

            <nav class="pdam-nav">
                <a href="{{ route('home') }}"
                    class="pdam-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('layanan') }}"
                    class="pdam-nav-link {{ request()->routeIs('layanan') ? 'active' : '' }}">Layanan</a>
                <a href="{{ route('tarif') }}"
                    class="pdam-nav-link {{ request()->routeIs('tarif') ? 'active' : '' }}">Tarif</a>
                <a href="{{ route('pengaduan') }}"
                    class="pdam-nav-link {{ request()->routeIs('pengaduan*') ? 'active' : '' }}">Pengaduan</a>
                <a href="{{ route('berita') }}"
                    class="pdam-nav-link {{ request()->routeIs('berita*') ? 'active' : '' }}">Berita</a>
            </nav>

            {{-- <div class="pdam-nav-actions">
                <a href="{{ route('kontak') }}" class="pdam-nav-link {{ request()->routeIs('kontak') ? 'active' : '' }}">Kontak</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="pdam-btn pdam-btn-primary pdam-btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="pdam-btn pdam-btn-primary pdam-btn-sm">Login Pelanggan</a>
                @endauth
            </div> --}}

            <button class="pdam-mobile-toggle" type="button" aria-label="Toggle Menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="pdam-footer">
        <div class="pdam-container">
            <div class="pdam-footer-grid">
                <div class="pdam-footer-brand">
                    <a href="{{ route('home') }}" class="pdam-logo">
                        <div class="pdam-logo-image">
                            <img src="{{ asset('pdam.png') }}" alt="PDAM Amerta Toraya"
                                class="pdam-footer-contact-item-image">
                        </div>
                        <span>PDAM Amerta Toraya</span>
                    </a>
                    <p>Penyedia layanan air bersih terpercaya, melayani masyarakat dengan integritas dan teknologi
                        modern.</p>
                    <div class="pdam-footer-social">
                        <a href="#" aria-label="Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </a>
                        <a href="#" aria-label="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </a>
                        <a href="#" aria-label="Twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="pdam-footer-title">Layanan</h4>
                    <ul class="pdam-footer-links">
                        <li><a href="{{ route('pasang-baru') }}">Pasang Baru</a></li>
                        <li><a href="{{ route('cek-tagihan') }}">Cek Tagihan</a></li>
                        <li><a href="{{ route('pengaduan') }}">Lapor Gangguan</a></li>
                        <li><a href="{{ route('download') }}">Download Center</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="pdam-footer-title">Perusahaan</h4>
                    <ul class="pdam-footer-links">
                        <li><a href="{{ route('tentang') }}">Tentang Kami</a></li>
                        <li><a href="{{ route('struktur-organisasi') }}">Struktur Organisasi</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="pdam-footer-title">Kontak</h4>
                    <div class="pdam-footer-contact">
                        <div class="pdam-footer-contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                </path>
                            </svg>
                            <span>(0423) 23344</span>
                        </div>
                        {{-- <div class="pdam-footer-contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <span>layanan@pdamsmart.id</span>
                        </div> --}}
                        <div class="pdam-footer-contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>

                            <span>
                                Jalan Tedong Bonga, Kompleks Pasar Bolu, Kelurahan Tallunglipu, Kecamatan
                                Tallunglipu,<br>
                                Kabupaten Toraja Utara, Provinsi Sulawesi Selatan
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pdam-footer-bottom">
                <p>&copy; {{ date('Y') }} PDAM Amerta Toraya. All rights reserved.</p>
                <div class="pdam-footer-legal">
                    <a href="#">Kebijakan Privasi</a>
                    <a href="#">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    
    <!-- jQuery and Select2 from CDN as fallback -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    
    @stack('scripts')
</body>

</html>
