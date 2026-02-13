<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CompanyInfoKey;
use App\Enums\DownloadCategory;
use App\Enums\EmploymentType;
use App\Enums\FaqCategory;
use App\Enums\GalleryType;
use App\Enums\GangguanStatus;
use App\Enums\LoketType;
use App\Enums\LowonganStatus;
use App\Enums\PriorityLevel;
use App\Enums\SeverityLevel;
use App\Models\Berita;
use App\Models\CompanyInfo;
use App\Models\ContactMessage;
use App\Models\Download;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\GangguanAir;
use App\Models\Kategori;
use App\Models\LoketPembayaran;
use App\Models\Lowongan;
use App\Models\Page;
use App\Models\Pengumuman;
use App\Models\Slider;
use Illuminate\Database\Seeder;

class PdamSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCompanyInfo();
        $this->seedSliders();
        $this->seedBerita();
        $this->seedPengumuman();
        $this->seedGangguanAir();
        $this->seedGallery();
        $this->seedLowongan();
        $this->seedFaqs();
        $this->seedLoketPembayaran();
        $this->seedDownloads();
        $this->seedPages();
        $this->seedContactMessages();
    }

    private function seedCompanyInfo(): void
    {
        $info = [
            // Contact
            CompanyInfoKey::Phone->value => '(0423) 23344',
            CompanyInfoKey::Email->value => 'layanan@pdamamertatoraya.id',
            CompanyInfoKey::Whatsapp->value => '0812-3456-7890',
            CompanyInfoKey::Address->value => 'Jalan Tedong Bonga, Kompleks Pasar Bolu, Kelurahan Tallunglipu, Kecamatan Tallunglipu, Kabupaten Toraja Utara, Provinsi Sulawesi Selatan',
            CompanyInfoKey::OfficeAddress->value => 'Jalan Tedong Bonga, Kompleks Pasar Bolu, Kelurahan Tallunglipu, Kecamatan Tallunglipu, Kabupaten Toraja Utara',

            // Social Media
            CompanyInfoKey::Facebook->value => 'https://facebook.com/pdamamertatoraya',
            CompanyInfoKey::Instagram->value => 'https://instagram.com/pdamamertatoraya',
            CompanyInfoKey::Twitter->value => 'https://twitter.com/pdamamertatoraya',

            // About - General
            CompanyInfoKey::CompanyName->value => 'PDAM Amerta Toraya',
            CompanyInfoKey::Tagline->value => 'Air Bersih untuk Kehidupan',
            CompanyInfoKey::FoundedYear->value => '1982',
            CompanyInfoKey::Vision->value => 'Menjadi perusahaan air minum modern yang memberikan pelayanan prima dan berkontribusi pada kelestarian lingkungan.',
            CompanyInfoKey::Mission1->value => 'Menyediakan air bersih yang memenuhi standar kesehatan secara berkesinambungan.',
            CompanyInfoKey::Mission2->value => 'Menerapkan teknologi terkini dalam operasional dan pelayanan pelanggan.',
            CompanyInfoKey::Mission3->value => 'Meningkatkan kompetensi sumber daya manusia yang profesional dan berintegritas.',

            // About - Hero Section
            CompanyInfoKey::AboutHeroBadge->value => 'Tentang PDAM Amerta Toraya',
            CompanyInfoKey::AboutHeroTitle->value => 'Mengalirkan Kehidupan, Membangun Masa Depan',
            CompanyInfoKey::AboutHeroSubtitle->value => 'Berkomitmen menyediakan air bersih berkualitas tinggi dengan teknologi modern dan pelayanan prima untuk kesejahteraan masyarakat dan lingkungan yang berkelanjutan.',
            CompanyInfoKey::AboutHeroImage->value => 'https://images.unsplash.com/photo-1581092160607-ee67df51c730?q=80&w=1200&auto=format&fit=crop',

            // About - History Section
            CompanyInfoKey::AboutHistoryTitle->value => 'Dedikasi Untuk Negeri',
            CompanyInfoKey::AboutHistoryParagraph1->value => 'PDAM Amerta Toraya didirikan dengan satu tujuan mulia: memastikan setiap tetes air yang sampai ke rumah pelanggan adalah air yang bersih, aman, dan menyehatkan. Sebagai Badan Usaha Milik Daerah (BUMD), kami memegang teguh mandat untuk melayani kebutuhan dasar masyarakat.',
            CompanyInfoKey::AboutHistoryParagraph2->value => 'Kami terus bertransformasi. Dari sistem manual hingga kini kami tidak pernah berhenti berinovasi untuk efisiensi dan kepuasan pelanggan.',
            CompanyInfoKey::CoveragePercentage->value => '98%',
            CompanyInfoKey::ServiceHoursValue->value => '24/7',

            // About - Visi Misi Section
            CompanyInfoKey::AboutVisiMisiSubtitle->value => 'Arah langkah kami dalam mewujudkan pelayanan air bersih yang unggul dan berkelanjutan.',

            // About - Values Section
            CompanyInfoKey::AboutValuesTitle->value => 'Nilai Utama Perusahaan',
            CompanyInfoKey::AboutValuesSubtitle->value => 'Dalam setiap tetes air yang kami alirkan, terkandung nilai-nilai yang menjadi pedoman kerja seluruh insan PDAM.',
            CompanyInfoKey::Value1Title->value => 'Integritas',
            CompanyInfoKey::Value1Description->value => 'Bekerja dengan jujur, transparan, dan bertanggung jawab terhadap amanah publik.',
            CompanyInfoKey::Value2Title->value => 'Orientasi Pelanggan',
            CompanyInfoKey::Value2Description->value => 'Menempatkan kepuasan pelanggan sebagai prioritas utama dalam setiap keputusan.',
            CompanyInfoKey::Value3Title->value => 'Inovasi',
            CompanyInfoKey::Value3Description->value => 'Terus melakukan perbaikan dan pembaruan untuk hasil yang lebih baik dan efisien.',

            // About - Coverage Section
            CompanyInfoKey::AboutCoverageTitle->value => 'Wilayah Layanan Luas',
            CompanyInfoKey::AboutCoverageDescription->value => 'Terus memperluas jaringan pipa distribusi hingga ke pelosok daerah untuk pemerataan akses air bersih.',

            // Operation
            CompanyInfoKey::OfficeHours->value => 'Senin - Jumat: 08.00 - 16.00 WITA',
            CompanyInfoKey::WeekendHours->value => 'Sabtu - Minggu: Tutup',
            CompanyInfoKey::CallCenterHours->value => '24 Jam',
        ];

        foreach ($info as $key => $value) {
            $enum = CompanyInfoKey::from($key);
            CompanyInfo::create([
                'key' => $key,
                'value' => $value,
                'group' => $enum->getGroup(),
            ]);
        }
    }

    private function seedSliders(): void
    {
        $sliders = [
            [
                'title' => 'Air Bersih untuk Kehidupan Lebih Baik',
                'subtitle' => 'Nikmati kemudahan akses layanan air bersih dengan sistem digital terintegrasi',
                'button_text' => 'Cek Tagihan',
                'button_url' => '/cek-tagihan',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Layanan 24 Jam Tanpa Henti',
                'subtitle' => 'Tim teknis kami siap melayani kebutuhan Anda kapan saja',
                'button_text' => 'Hubungi Kami',
                'button_url' => '/kontak',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Pasang Baru Sambungan Air',
                'subtitle' => 'Proses mudah dan cepat untuk sambungan air baru di rumah Anda',
                'button_text' => 'Daftar Sekarang',
                'button_url' => '/pasang-baru',
                'display_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }

    private function seedBerita(): void
    {
        $kategoriBerita = Kategori::firstOrCreate(['nama' => 'Berita']);
        $kategoriPengumuman = Kategori::firstOrCreate(['nama' => 'Pengumuman']);
        $kategoriLayanan = Kategori::firstOrCreate(['nama' => 'Layanan']);
        $kategoriEdukasi = Kategori::firstOrCreate(['nama' => 'Edukasi']);

        $beritaData = [
            [
                'title' => 'PDAM Raih Penghargaan Pelayanan Terbaik Tahun 2025',
                'content' => '<p>PDAM berhasil meraih penghargaan sebagai perusahaan daerah dengan pelayanan terbaik di Indonesia. Penghargaan ini diberikan atas dedikasi dan komitmen dalam memberikan layanan air bersih berkualitas kepada masyarakat.</p><p>Penghargaan ini merupakan hasil kerja keras seluruh jajaran PDAM dalam meningkatkan kualitas pelayanan dan infrastruktur distribusi air bersih.</p>',
                'is_featured' => true,
                'published_at' => now()->subDays(1),
                'kategori' => [$kategoriBerita->id],
            ],
            [
                'title' => 'Program Pemeliharaan Jaringan Pipa Distribusi',
                'content' => '<p>PDAM melaksanakan program pemeliharaan rutin jaringan pipa distribusi untuk memastikan kualitas dan kontinuitas pasokan air bersih ke pelanggan.</p><p>Program ini meliputi penggantian pipa yang sudah tua, perbaikan kebocoran, dan peningkatan kapasitas jaringan di beberapa wilayah.</p>',
                'is_featured' => false,
                'published_at' => now()->subDays(3),
                'kategori' => [$kategoriPengumuman->id],
            ],
            [
                'title' => 'Sosialisasi Hemat Air di Musim Kemarau',
                'content' => '<p>PDAM mengadakan sosialisasi tentang pentingnya menghemat air bersih di musim kemarau. Kegiatan ini diharapkan dapat meningkatkan kesadaran masyarakat dalam menggunakan air secara bijak.</p><p>Beberapa tips yang disampaikan antara lain: menutup keran saat tidak digunakan, menggunakan air bekas cucian untuk menyiram tanaman, dan memperbaiki kebocoran di rumah.</p>',
                'is_featured' => false,
                'published_at' => now()->subDays(5),
                'kategori' => [$kategoriEdukasi->id],
            ],
            [
                'title' => 'Peningkatan Kapasitas Produksi Air Bersih',
                'content' => '<p>PDAM berhasil meningkatkan kapasitas produksi air bersih sebesar 20% melalui penambahan unit pengolahan baru. Hal ini dilakukan untuk memenuhi kebutuhan air bersih masyarakat yang terus meningkat.</p>',
                'is_featured' => false,
                'published_at' => now()->subDays(7),
                'kategori' => [$kategoriBerita->id],
            ],
            [
                'title' => 'Mudahnya Bayar Tagihan Air Lewat Aplikasi Mobile',
                'content' => '<p>Peluncuran fitur pembayaran digital terbaru yang memungkinkan pelanggan membayar tagihan air melalui e-wallet dan QRIS.</p><p>PDAM terus berinovasi dalam memberikan kemudahan kepada pelanggan. Kini, pembayaran tagihan bulanan dapat dilakukan melalui aplikasi yang mendukung berbagai metode pembayaran digital.</p>',
                'is_featured' => false,
                'published_at' => now()->subDays(10),
                'kategori' => [$kategoriLayanan->id],
            ],
            [
                'title' => '5 Tips Menghemat Penggunaan Air di Rumah Tangga',
                'content' => '<p>Panduan praktis bagi keluarga untuk mengurangi konsumsi air sehari-hari tanpa mengurangi kenyamanan.</p><p>1. Periksa keran dan pipa secara berkala untuk mendeteksi kebocoran.</p><p>2. Gunakan shower dengan flow rate rendah daripada bak mandi.</p><p>3. Kumpulkan air hujan untuk menyiram tanaman.</p><p>4. Jangan biarkan keran mengalir saat menyikat gigi atau mencuci piring.</p><p>5. Pilih mesin cuci dengan mode hemat air.</p>',
                'is_featured' => false,
                'published_at' => now()->subDays(14),
                'kategori' => [$kategoriEdukasi->id],
            ],
            [
                'title' => 'Program Penghijauan Hulu Sungai untuk Menjaga Kualitas Air Baku',
                'content' => '<p>PDAM bekerjasama dengan komunitas pecinta alam melakukan penanaman 10.000 pohon di area hulu sungai sebagai upaya konservasi sumber air.</p><p>Program ini merupakan bagian dari komitmen jangka panjang dalam menjaga keberlanjutan sumber air baku. Pohon-pohon yang ditanam meliputi varietas endemik seperti beringin, trembesi, dan mahoni.</p>',
                'is_featured' => false,
                'published_at' => now()->subDays(21),
                'kategori' => [$kategoriBerita->id],
            ],
        ];

        foreach ($beritaData as $data) {
            $kategoriIds = $data['kategori'];
            unset($data['kategori']);

            $berita = Berita::create([
                ...$data,
                'author_id' => null,
                'view_count' => fake()->numberBetween(50, 500),
            ]);

            $berita->kategori()->attach($kategoriIds);
        }
    }

    private function seedPengumuman(): void
    {
        $pengumumanData = [
            [
                'title' => 'Jadwal Pemeliharaan Rutin Bulan Ini',
                'content' => '<p>Diberitahukan kepada seluruh pelanggan bahwa akan dilaksanakan pemeliharaan rutin jaringan distribusi air pada:</p><ul><li>Tanggal: 15-17 Februari 2026</li><li>Wilayah: Kecamatan Tallunglipu dan sekitarnya</li><li>Dampak: Aliran air mungkin terganggu atau tekanan berkurang</li></ul><p>Mohon maaf atas ketidaknyamanan ini. Kami mengimbau pelanggan untuk menyiapkan cadangan air.</p>',
                'priority' => PriorityLevel::Tinggi,
                'is_active' => true,
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(30),
            ],
            [
                'title' => 'Pembayaran Tagihan Melalui Aplikasi Mobile',
                'content' => '<p>Kini pembayaran tagihan air dapat dilakukan dengan mudah melalui aplikasi PDAM Mobile. Download aplikasi di Play Store atau App Store dan nikmati kemudahan pembayaran tanpa antre.</p><p>Fitur unggulan:</p><ul><li>Cek tagihan real-time</li><li>Pembayaran online 24 jam</li><li>Riwayat pemakaian</li><li>Notifikasi tagihan</li></ul>',
                'priority' => PriorityLevel::Sedang,
                'is_active' => true,
                'start_date' => now()->subDays(7),
                'end_date' => now()->addMonths(3),
            ],
            [
                'title' => 'Perubahan Jam Operasional Loket Pelayanan',
                'content' => '<p>Mulai tanggal 1 Maret 2026, jam operasional loket pelayanan PDAM berubah menjadi:</p><ul><li>Senin - Kamis: 08.00 - 15.00 WITA</li><li>Jumat: 08.00 - 11.30 WITA</li><li>Sabtu - Minggu: Tutup</li></ul><p>Untuk pelayanan darurat, silakan hubungi Call Center 24 jam.</p>',
                'priority' => PriorityLevel::Rendah,
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
            ],
        ];

        foreach ($pengumumanData as $data) {
            Pengumuman::create($data);
        }
    }

    private function seedGangguanAir(): void
    {
        $gangguanData = [
            [
                'title' => 'Perbaikan Pipa Utama Jl. Pongtiku',
                'description' => 'Sedang dilakukan perbaikan pipa distribusi utama yang mengalami kebocoran. Tim teknis sedang bekerja untuk menyelesaikan perbaikan.',
                'affected_areas' => ['Kelurahan Tallunglipu', 'Kelurahan Rantepao', 'Perumahan Griya Asri'],
                'severity' => SeverityLevel::Sedang,
                'status' => GangguanStatus::Active,
                'start_datetime' => now()->subHours(4),
                'estimated_end_datetime' => now()->addHours(8),
            ],
            [
                'title' => 'Pemeliharaan Pompa Distribusi',
                'description' => 'Dilakukan pemeliharaan rutin pada pompa distribusi untuk memastikan tekanan air tetap optimal.',
                'affected_areas' => ['Kecamatan Tallunglipu', 'Kecamatan Rantepao'],
                'severity' => SeverityLevel::Ringan,
                'status' => GangguanStatus::Active,
                'start_datetime' => now()->subHours(2),
                'estimated_end_datetime' => now()->addHours(4),
            ],
            [
                'title' => 'Gangguan Listrik PLN di IPA',
                'description' => 'Terjadi gangguan pasokan listrik dari PLN yang mempengaruhi operasional Instalasi Pengolahan Air. Kami berkoordinasi dengan PLN untuk pemulihan.',
                'affected_areas' => ['Kecamatan Sesean', 'Kecamatan Sa\'dan', 'Kecamatan Balusu'],
                'severity' => SeverityLevel::Berat,
                'status' => GangguanStatus::Active,
                'start_datetime' => now()->subHours(1),
                'estimated_end_datetime' => now()->addHours(6),
            ],
        ];

        foreach ($gangguanData as $data) {
            GangguanAir::create($data);
        }
    }

    private function seedGallery(): void
    {
        $galleryData = [
            [
                'title' => 'Peresmian Instalasi Pengolahan Air Baru',
                'description' => 'Dokumentasi peresmian IPA baru yang akan meningkatkan kapasitas produksi air bersih.',
                'type' => GalleryType::Foto,
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Sosialisasi Hemat Air di Sekolah',
                'description' => 'Kegiatan edukasi tentang pentingnya menghemat air bersih kepada siswa sekolah dasar.',
                'type' => GalleryType::Foto,
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Tutorial Cara Membaca Meter Air',
                'description' => 'Video tutorial cara membaca meter air dengan benar untuk pelanggan.',
                'type' => GalleryType::Video,
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Pelatihan Teknisi PDAM',
                'description' => 'Dokumentasi kegiatan pelatihan untuk meningkatkan kompetensi teknisi lapangan.',
                'type' => GalleryType::Foto,
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Kerja Bakti Bersama Masyarakat',
                'description' => 'PDAM bersama masyarakat melakukan kerja bakti membersihkan saluran air.',
                'type' => GalleryType::Foto,
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Pengecekan Kualitas Air',
                'description' => 'Tim laboratorium melakukan pengecekan kualitas air secara berkala.',
                'type' => GalleryType::Foto,
                'display_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($galleryData as $data) {
            Gallery::create($data);
        }
    }

    private function seedLowongan(): void
    {
        $lowonganData = [
            [
                'title' => 'Staff IT - Programmer',
                'description' => '<p>PDAM membuka kesempatan bagi profesional IT untuk bergabung sebagai Programmer yang akan bertanggung jawab dalam pengembangan dan pemeliharaan sistem informasi perusahaan.</p>',
                'requirements' => '<ul><li>S1 Teknik Informatika/Sistem Informasi</li><li>Pengalaman minimal 2 tahun</li><li>Menguasai PHP, Laravel, MySQL</li><li>Familiar dengan Git</li></ul>',
                'responsibilities' => '<ul><li>Mengembangkan fitur baru aplikasi</li><li>Melakukan maintenance sistem</li><li>Membuat dokumentasi teknis</li></ul>',
                'department' => 'Divisi Teknologi Informasi',
                'location' => 'Kantor Pusat',
                'employment_type' => EmploymentType::FullTime,
                'status' => LowonganStatus::Open,
                'deadline' => now()->addDays(30),
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'title' => 'Teknisi Jaringan Distribusi',
                'description' => '<p>Dibutuhkan teknisi yang berpengalaman dalam pengelolaan dan pemeliharaan jaringan distribusi air bersih.</p>',
                'requirements' => '<ul><li>D3/S1 Teknik Sipil/Lingkungan</li><li>Pengalaman minimal 1 tahun</li><li>Memiliki SIM A/C</li><li>Bersedia bekerja shift</li></ul>',
                'responsibilities' => '<ul><li>Melakukan inspeksi rutin</li><li>Memperbaiki kebocoran pipa</li><li>Melaporkan kondisi jaringan</li></ul>',
                'department' => 'Divisi Teknik',
                'location' => 'Seluruh Wilayah Layanan',
                'employment_type' => EmploymentType::FullTime,
                'status' => LowonganStatus::Open,
                'deadline' => now()->addDays(21),
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'title' => 'Magang - Customer Service',
                'description' => '<p>Program magang untuk mahasiswa yang ingin belajar tentang pelayanan pelanggan di perusahaan utilitas publik.</p>',
                'requirements' => '<ul><li>Mahasiswa aktif semester 5-7</li><li>Jurusan Komunikasi/Manajemen</li><li>Komunikatif dan ramah</li></ul>',
                'responsibilities' => '<ul><li>Membantu pelayanan pelanggan</li><li>Mencatat keluhan pelanggan</li><li>Belajar sistem pelayanan</li></ul>',
                'department' => 'Divisi Pelayanan',
                'location' => 'Kantor Pusat',
                'employment_type' => EmploymentType::Internship,
                'status' => LowonganStatus::Open,
                'deadline' => now()->addDays(14),
                'is_active' => true,
                'display_order' => 3,
            ],
        ];

        foreach ($lowonganData as $data) {
            Lowongan::create($data);
        }
    }

    private function seedFaqs(): void
    {
        $faqData = [
            [
                'question' => 'Bagaimana cara mengecek tagihan air saya?',
                'answer' => "Anda dapat mengecek tagihan air melalui:\n1. Website resmi PDAM di menu \"Cek Tagihan\"\n2. Aplikasi PDAM Mobile\n3. Datang langsung ke loket pelayanan",
                'category' => FaqCategory::Umum,
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara melaporkan kebocoran pipa?',
                'answer' => "Untuk melaporkan kebocoran pipa, Anda dapat:\n1. Menghubungi Call Center 24 jam\n2. Mengisi form pengaduan di website\n3. Menggunakan fitur \"Lapor\" di aplikasi PDAM Mobile\n4. Datang ke kantor pelayanan terdekat",
                'category' => FaqCategory::Pengaduan,
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Berapa lama proses pemasangan sambungan baru?',
                'answer' => 'Proses pemasangan sambungan baru membutuhkan waktu 7-14 hari kerja setelah semua persyaratan lengkap dan pembayaran biaya pasang baru dilakukan. Waktu dapat bervariasi tergantung kondisi lapangan.',
                'category' => FaqCategory::Umum,
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Apa saja persyaratan untuk pasang baru?',
                'answer' => "Persyaratan pasang baru sambungan air:\n1. Fotokopi KTP pemilik\n2. Fotokopi bukti kepemilikan/sewa (sertifikat/PBB/kontrak)\n3. Denah lokasi\n4. Mengisi formulir permohonan\n5. Membayar biaya pendaftaran",
                'category' => FaqCategory::Umum,
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara pindah nama pelanggan?',
                'answer' => "Untuk pindah nama pelanggan, silakan datang ke kantor PDAM dengan membawa:\n1. KTP pemilik baru\n2. Bukti pembelian/sewa properti\n3. Surat keterangan dari pemilik lama (jika ada)\n4. Melunasi tunggakan (jika ada)",
                'category' => FaqCategory::Umum,
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'question' => 'Berapa lama proses perbaikan kebocoran?',
                'answer' => 'Waktu perbaikan tergantung pada jenis kerusakan. Untuk masalah ringan seperti pipa bocor kecil, biasanya dapat diselesaikan dalam 1-2 hari kerja. Untuk masalah yang lebih kompleks, tim kami akan memberikan estimasi waktu setelah melakukan survei lokasi.',
                'category' => FaqCategory::Pengaduan,
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'question' => 'Kenapa tagihan saya naik drastis?',
                'answer' => 'Kenaikan tagihan dapat disebabkan oleh peningkatan pemakaian air, kebocoran pipa internal, atau perubahan tarif. Jika Anda mencurigai ada masalah pada meteran atau kebocoran, silakan laporkan untuk pemeriksaan oleh petugas kami.',
                'category' => FaqCategory::Umum,
                'display_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($faqData as $data) {
            Faq::create($data);
        }
    }

    private function seedLoketPembayaran(): void
    {
        $loketData = [
            [
                'name' => 'Kantor Pusat PDAM',
                'type' => LoketType::KantorPdam,
                'address' => 'Jl. Tedong Bonga, Kompleks Pasar Bolu, Tallunglipu',
                'phone' => '(0423) 23344',
                'latitude' => -2.9667,
                'longitude' => 119.9000,
                'operational_hours' => 'Senin - Jumat: 08.00 - 15.00',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Loket Cabang Rantepao',
                'type' => LoketType::KantorPdam,
                'address' => 'Jl. Ahmad Yani, Rantepao',
                'phone' => '(0423) 21234',
                'latitude' => -2.9711,
                'longitude' => 119.8978,
                'operational_hours' => 'Senin - Jumat: 08.00 - 15.00',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Bank BRI Cabang Rantepao',
                'type' => LoketType::Bank,
                'address' => 'Jl. Mappanyuki No. 10, Rantepao',
                'phone' => '(0423) 21456',
                'latitude' => -2.9700,
                'longitude' => 119.8950,
                'operational_hours' => 'Senin - Jumat: 08.00 - 15.00',
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'name' => 'Indomaret Jl. Pongtiku',
                'type' => LoketType::Minimarket,
                'address' => 'Jl. Pongtiku No. 56, Rantepao',
                'phone' => null,
                'latitude' => -2.9680,
                'longitude' => 119.8960,
                'operational_hours' => '24 Jam',
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'name' => 'Alfamart Jl. Diponegoro',
                'type' => LoketType::Minimarket,
                'address' => 'Jl. Diponegoro No. 78, Rantepao',
                'phone' => null,
                'latitude' => -2.9720,
                'longitude' => 119.9010,
                'operational_hours' => '24 Jam',
                'is_active' => true,
                'display_order' => 5,
            ],
            [
                'name' => 'PPOB Mitra Jaya',
                'type' => LoketType::Ppob,
                'address' => 'Jl. Pemuda No. 12, Tallunglipu',
                'phone' => '0812-3456-7890',
                'latitude' => -2.9650,
                'longitude' => 119.8980,
                'operational_hours' => 'Senin - Sabtu: 08.00 - 20.00',
                'is_active' => true,
                'display_order' => 6,
            ],
        ];

        foreach ($loketData as $data) {
            LoketPembayaran::create($data);
        }
    }

    private function seedDownloads(): void
    {
        $downloadData = [
            [
                'title' => 'Formulir Permohonan Pasang Baru',
                'description' => 'Formulir resmi untuk mengajukan permohonan pemasangan sambungan air baru.',
                'category' => DownloadCategory::Formulir,
                'download_count' => 152,
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'title' => 'Formulir Pengaduan Pelanggan',
                'description' => 'Formulir untuk menyampaikan keluhan atau pengaduan terkait layanan PDAM.',
                'category' => DownloadCategory::Formulir,
                'download_count' => 89,
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'title' => 'Peraturan Daerah tentang Pengelolaan Air Minum',
                'description' => 'Peraturan Daerah yang mengatur tentang pengelolaan dan distribusi air minum di wilayah layanan.',
                'category' => DownloadCategory::Peraturan,
                'download_count' => 64,
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'title' => 'Panduan Membaca Meter Air',
                'description' => 'Panduan lengkap cara membaca meter air untuk pelanggan PDAM.',
                'category' => DownloadCategory::Panduan,
                'download_count' => 210,
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'title' => 'SK Tarif Air Minum Terbaru',
                'description' => 'Surat Keputusan Bupati tentang penetapan tarif air minum PDAM yang berlaku.',
                'category' => DownloadCategory::Peraturan,
                'download_count' => 178,
                'is_active' => true,
                'display_order' => 5,
            ],
            [
                'title' => 'Panduan Penghematan Air',
                'description' => 'Tips dan panduan praktis menghemat penggunaan air di rumah tangga.',
                'category' => DownloadCategory::Panduan,
                'download_count' => 95,
                'is_active' => true,
                'display_order' => 6,
            ],
        ];

        foreach ($downloadData as $data) {
            Download::create($data);
        }
    }

    private function seedPages(): void
    {
        $pages = [
            [
                'title' => 'Visi & Misi',
                'slug' => 'visi-misi',
                'content' => '<h2>Visi</h2><p>Menjadi perusahaan air minum modern yang memberikan pelayanan prima dan berkontribusi pada kelestarian lingkungan di Kabupaten Toraja Utara.</p><h2>Misi</h2><ol><li>Menyediakan air bersih yang memenuhi standar kesehatan secara berkesinambungan.</li><li>Menerapkan teknologi terkini dalam operasional dan pelayanan pelanggan.</li><li>Meningkatkan kompetensi sumber daya manusia yang profesional dan berintegritas.</li><li>Memperluas cakupan layanan air bersih ke seluruh wilayah.</li></ol>',
                'meta_title' => 'Visi & Misi - PDAM Amerta Toraya',
                'meta_description' => 'Visi dan misi PDAM Amerta Toraya dalam melayani kebutuhan air bersih masyarakat.',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'title' => 'Syarat & Ketentuan',
                'slug' => 'syarat-ketentuan',
                'content' => '<h2>Syarat & Ketentuan Layanan</h2><p>Dengan menggunakan layanan PDAM Amerta Toraya, pelanggan dianggap telah menyetujui syarat dan ketentuan berikut:</p><ol><li>Pelanggan wajib membayar tagihan air tepat waktu sebelum tanggal jatuh tempo.</li><li>Keterlambatan pembayaran akan dikenakan denda sesuai ketentuan yang berlaku.</li><li>Pelanggan bertanggung jawab atas instalasi pipa di dalam properti miliknya.</li><li>PDAM berhak memutus aliran air apabila terjadi tunggakan lebih dari 3 bulan.</li></ol>',
                'meta_title' => 'Syarat & Ketentuan - PDAM Amerta Toraya',
                'meta_description' => 'Syarat dan ketentuan layanan PDAM Amerta Toraya.',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'title' => 'Kebijakan Privasi',
                'slug' => 'kebijakan-privasi',
                'content' => '<h2>Kebijakan Privasi</h2><p>PDAM Amerta Toraya berkomitmen menjaga privasi data pelanggan. Data pribadi yang dikumpulkan hanya digunakan untuk keperluan pelayanan dan tidak dibagikan kepada pihak ketiga tanpa persetujuan.</p><h3>Data yang Dikumpulkan</h3><ul><li>Nama dan alamat pelanggan</li><li>Nomor identitas (KTP)</li><li>Nomor telepon dan email</li><li>Data pemakaian air</li></ul>',
                'meta_title' => 'Kebijakan Privasi - PDAM Amerta Toraya',
                'meta_description' => 'Kebijakan privasi dan perlindungan data pelanggan PDAM Amerta Toraya.',
                'is_active' => true,
                'display_order' => 3,
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }

    private function seedContactMessages(): void
    {
        ContactMessage::factory()->count(5)->unread()->create();
        ContactMessage::factory()->count(3)->read()->create();
    }
}
