<?php

return [
    'sso_url' => env('SSO_URL'),
    'apps' => [
        'hublang' => [
            'title' => 'HUBLANG',
            'subtitle' => 'Hubungan Langganan',
            'icon' => 'bx bx-user-plus',
            'url' => '#',
            'color' => 'primary',
        ],
        'bacameter' => [
            'title' => 'ANDROMEDA',
            'subtitle' => 'Bacameter',
            'icon' => 'bx bx-color',
            'url' => '#',
            'color' => 'success',
        ],

        'billing' => [
            'title' => 'BILLING',
            'subtitle' => 'Rekening Pelanggan',
            'icon' => 'bx bx-id-card',
            'url' => '#',
            'color' => 'warning',
        ],

        'akunting' => [
            'title' => 'AKUNTING',
            'subtitle' => 'Keuangan',
            'icon' => 'bx bx-file',
            'url' => '#',
            'color' => 'danger',
        ],
        'hrd' => [
            'title' => 'HRD',
            'subtitle' => 'Kepegawaian & Penggajian',
            'icon' => 'bx bx-bookmark',
            'url' => '#',
            'color' => 'info',
        ],
        'gudang' => [
            'title' => 'GUDANG',
            'subtitle' => 'Aset Manajemen',
            'icon' => 'bx bx-box',
            'url' => '#',
            'color' => 'dark',
        ],
        'kpi' => [
            'title' => 'KPI',
            'subtitle' => 'Kinerja Pegawai',
            'icon' => 'bx bx-line-chart',
            'url' => '#',
            'color' => 'success',
        ],

        'mobile' => [
            'title' => 'MOBILE APP',
            'subtitle' => 'Aplikasi Mobile',
            'icon' => 'bx bx-mobile',
            'url' => '#',
            'color' => 'primary',
        ],
    ],
];
