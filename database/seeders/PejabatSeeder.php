<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PejabatLevel;
use App\Models\Pejabat;
use Illuminate\Database\Seeder;

class PejabatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pejabats = [
            // Direksi
            [
                'nama' => 'H. Ahmad Surya, SE., MM.',
                'jabatan' => 'Direktur Utama',
                'level' => PejabatLevel::Direksi->value,
                'bidang' => 'Pimpinan Tertinggi',
                'deskripsi' => 'Memimpin dan mengarahkan seluruh kegiatan perusahaan.',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'nama' => 'Ir. Budi Hartono, MT.',
                'jabatan' => 'Direktur Teknik',
                'level' => PejabatLevel::Direksi->value,
                'bidang' => 'Teknik & Operasional',
                'deskripsi' => 'Mengelola bidang teknik, produksi, dan distribusi air.',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'nama' => 'Dra. Siti Rahayu, M.Ak.',
                'jabatan' => 'Direktur Umum',
                'level' => PejabatLevel::Direksi->value,
                'bidang' => 'Keuangan & Administrasi',
                'deskripsi' => 'Mengelola bidang keuangan, SDM, dan administrasi umum.',
                'display_order' => 3,
                'is_active' => true,
            ],

            // Kepala Bidang
            [
                'nama' => 'Ir. Dedi Setiawan',
                'jabatan' => 'Kabid Produksi',
                'level' => PejabatLevel::Kabid->value,
                'bidang' => 'Produksi',
                'deskripsi' => 'Pengelolaan produksi air bersih.',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'nama' => 'Eko Prasetyo, ST.',
                'jabatan' => 'Kabid Distribusi',
                'level' => PejabatLevel::Kabid->value,
                'bidang' => 'Distribusi',
                'deskripsi' => 'Pendistribusian air ke pelanggan.',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'nama' => 'Hj. Fatimah, SE.',
                'jabatan' => 'Kabid Langganan',
                'level' => PejabatLevel::Kabid->value,
                'bidang' => 'Langganan',
                'deskripsi' => 'Pelayanan pelanggan dan penagihan.',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'nama' => 'Gunawan, SE., M.Ak.',
                'jabatan' => 'Kabid Keuangan',
                'level' => PejabatLevel::Kabid->value,
                'bidang' => 'Keuangan',
                'deskripsi' => 'Pengelolaan keuangan perusahaan.',
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'nama' => 'Heru Susanto, SH.',
                'jabatan' => 'Kabid Umum',
                'level' => PejabatLevel::Kabid->value,
                'bidang' => 'Umum',
                'deskripsi' => 'Administrasi dan kepegawaian.',
                'display_order' => 8,
                'is_active' => true,
            ],
            [
                'nama' => 'Ir. Indra Wijaya, MT.',
                'jabatan' => 'Kabid Perencanaan',
                'level' => PejabatLevel::Kabid->value,
                'bidang' => 'Perencanaan',
                'deskripsi' => 'Perencanaan dan pengembangan.',
                'display_order' => 9,
                'is_active' => true,
            ],

            // Kepala Sub Bidang
            [
                'nama' => 'Joko Widodo, ST.',
                'jabatan' => 'Kasubid Pelayanan Utara',
                'level' => PejabatLevel::Kasubid->value,
                'bidang' => 'Sub Bidang Pelayanan Utara',
                'deskripsi' => 'Melayani wilayah utara kota.',
                'display_order' => 10,
                'is_active' => true,
            ],
            [
                'nama' => 'Kartini, ST.',
                'jabatan' => 'Kasubid Pelayanan Selatan',
                'level' => PejabatLevel::Kasubid->value,
                'bidang' => 'Sub Bidang Pelayanan Selatan',
                'deskripsi' => 'Melayani wilayah selatan kota.',
                'display_order' => 11,
                'is_active' => true,
            ],
            [
                'nama' => 'Lukman Hakim, ST.',
                'jabatan' => 'Kasubid Pelayanan Timur',
                'level' => PejabatLevel::Kasubid->value,
                'bidang' => 'Sub Bidang Pelayanan Timur',
                'deskripsi' => 'Melayani wilayah timur kota.',
                'display_order' => 12,
                'is_active' => true,
            ],
        ];

        foreach ($pejabats as $pejabat) {
            Pejabat::create($pejabat);
        }
    }
}
