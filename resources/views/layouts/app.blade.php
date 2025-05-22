<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kemnaker Dashboard')</title>
    <link rel="icon" href="{{ asset('image/logo/logo_kemnaker.svg') }}" type="image/svg+xml">

    {{-- ... (Script & CSS lainnya tetap sama) ... --}}
    <script src="https://cdn.tailwindcss.com/3.4.1"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#64748b',
                    },
                    borderRadius: {'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>


    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; }
        .sidebar-parent-button.active-parent,
        .sidebar-parent-button.expanded {
             background-color: rgba(59, 130, 246, 0.05);
             color: #3b82f6;
        }
        .sidebar-parent-button.active-parent .main-menu-icon,
        .sidebar-parent-button.expanded .main-menu-icon {
             color: #3b82f6;
        }
         .sidebar-parent-button.active-parent > div > span:first-child,
         .sidebar-parent-button.expanded > div > span:first-child {
            font-weight: 600;
        }
        .sidebar-submenu-item.active {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            font-weight: 500;
        }
        .sidebar-parent-button:hover:not(.active-parent):not(.expanded) {
            background-color: rgba(59, 130, 246, 0.03);
        }
         .sidebar-submenu-item:hover:not(.active) {
            background-color: rgba(59, 130, 246, 0.03);
        }
        .submenu-list {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }
        .submenu-list.expanded {
            max-height: 1000px; /* Adjust as needed */
        }
        .form-input {
             border-width: 1px;
             border-color: #d1d5db;
             border-radius: theme('borderRadius.button');
             box-shadow: theme('boxShadow.sm');
        }
        .form-input:focus {
            border-color: theme('colors.primary');
            --tw-ring-color: theme('colors.primary');
            box-shadow: 0 0 0 2px theme('ringOpacity.50', 'colors.primary');
        }
        .chart-container {
            width: 100%;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            border-radius: theme('borderRadius.DEFAULT');
            color: #9ca3af;
            font-size: theme('fontSize.xs');
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="flex h-screen overflow-hidden bg-gray-100">
        <div id="sidebar" class="w-64 bg-white shadow-md flex flex-col h-full
                                fixed inset-y-0 left-0 z-30
                                transform -translate-x-full lg:translate-x-0
                                transition-transform duration-300 ease-in-out
                                lg:relative">
            <div class="p-4 flex items-center justify-between border-b border-gray-100">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('image/logo/logo_daskin.png') }}" alt="Logo Kemnaker" class="h-13 mr-5">
                </a>
                <button id="closeSidebar" class="lg:hidden text-gray-500 hover:text-primary">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <div class="overflow-y-auto flex-1">
                <nav class="py-2">
                    @php
                        $currentRouteName = Route::currentRouteName();
                        $user = Auth::user();

                        if (!function_exists('isSubmenuActive')) {
                            function isSubmenuActive($submenu, $currentRouteName) {
                                if (isset($submenu['route']) && $submenu['route'] !== '#') {
                                    if ($currentRouteName == $submenu['route']) return true;
                                    $baseRoute = explode('.index', $submenu['route'])[0];
                                    if (str_starts_with($currentRouteName ?? '', $baseRoute . '.')) return true;
                                     if (isset($submenu['active_on_prefixes']) && is_array($submenu['active_on_prefixes'])) {
                                        foreach($submenu['active_on_prefixes'] as $prefix){
                                            if(str_starts_with($currentRouteName ?? '', $prefix)) return true;
                                        }
                                    }
                                }
                                return false;
                            }
                        }

                        // Definisikan role read-only untuk kemudahan
                        $readOnlySpecificRoles = [
                            App\Models\User::ROLE_MENTERI,
                            App\Models\User::ROLE_WAKIL_MENTERI,
                            App\Models\User::ROLE_STAFF_KHUSUS,
                            App\Models\User::ROLE_USER,
                        ];

                        $sidebarMenu = [];
                        if (Auth::user()->role === 'user') {
                            $sidebarMenu = [
                                'Dashboard Utama' => [
                                    'icon' => 'ri-home-smile-line',
                                    'route' => 'dashboard',
                                    // Semua role yang terautentikasi bisa melihat Dashboard Utama
                                    'roles' => array_merge(
                                        [App\Models\User::ROLE_SUPERADMIN, App\Models\User::ROLE_ITJEN, App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_BINAPENTA, App\Models\User::ROLE_BINALAVOTAS, App\Models\User::ROLE_BINWASNAKER, App\Models\User::ROLE_PHI, App\Models\User::ROLE_BARENBANG],
                                        $readOnlySpecificRoles
                                    ),
                                ],
                                'Inspektorat Jenderal' => [
                                    'icon' => 'ri-government-line',
                                    'route' => 'inspektorat.dashboard',
                                    // Superadmin, Itjen, dan semua ReadOnlyUser bisa melihat parent menu ini
                                    'roles' => array_merge([App\Models\User::ROLE_ITJEN, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                ],
                                'Sekretariat Jenderal' => [
                                    'icon' => 'ri-building-4-line',
                                    'route' => 'sekretariat-jenderal.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                ],
                                'Binapenta' => [
                                    'icon' => 'ri-user-search-line',
                                    'route' => 'binapenta.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_BINAPENTA, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                ],
                                'Binalavotas' => [
                                    'icon' => 'ri-graduation-cap-line',
                                    'route' => 'binalavotas.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_BINALAVOTAS, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                ],
                                'Binwasnaker & K3' => [
                                    'icon' => 'ri-shield-check-line',
                                    'route' => 'binwasnaker.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_BINWASNAKER, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                ],
                                'PHI & JAMSOS' => [
                                    'icon' => 'ri-scales-3-line',
                                    'route' => 'phi.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_PHI, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                ],
                                'Barenbang' => [
                                    'icon' => 'ri-bar-chart-box-line',
                                    'route' => 'barenbang.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_BARENBANG, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                ],
                            ];
                        } else {
                            $sidebarMenu = [
                                'Dashboard Utama' => [
                                    'icon' => 'ri-home-smile-line',
                                    'route' => 'dashboard',
                                    // Semua role yang terautentikasi bisa melihat Dashboard Utama
                                    'roles' => array_merge(
                                        [App\Models\User::ROLE_SUPERADMIN, App\Models\User::ROLE_ITJEN, App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_BINAPENTA, App\Models\User::ROLE_BINALAVOTAS, App\Models\User::ROLE_BINWASNAKER, App\Models\User::ROLE_PHI, App\Models\User::ROLE_BARENBANG],
                                        $readOnlySpecificRoles
                                    ),
                                ],
                                'Inspektorat Jenderal' => [
                                    'icon' => 'ri-government-line',
                                    'route' => 'inspektorat.dashboard',
                                    // Superadmin, Itjen, dan semua ReadOnlyUser bisa melihat parent menu ini
                                    'roles' => array_merge([App\Models\User::ROLE_ITJEN, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                    'submenus' => [
                                        // Submenu Dashboard bisa dilihat oleh Itjen, Superadmin, dan ReadOnlyUser
                                        ['name' => 'Dashboard Itjen', 'route' => 'inspektorat.dashboard', 'icon' => 'ri-pie-chart-box-line', 'active_on_prefixes' => ['inspektorat.dashboard'], 'roles' => array_merge([App\Models\User::ROLE_ITJEN, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles)],
                                        // Submenu CRUD hanya untuk Itjen dan Superadmin
                                        ['name' => '% Progres Tindak Lanjut temuan BPK', 'route' => 'inspektorat.progress-temuan-bpk.index', 'icon' => 'ri-file-chart-line', 'roles' => [App\Models\User::ROLE_ITJEN, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => '% Progres Tindak Lanjut temuan internal', 'route' => 'inspektorat.progress-temuan-internal.index', 'icon' => 'ri-file-search-line', 'roles' => [App\Models\User::ROLE_ITJEN, App\Models\User::ROLE_SUPERADMIN]],
                                    ]
                                ],
                                'Sekretariat Jenderal' => [
                                    'icon' => 'ri-building-4-line',
                                    'route' => 'sekretariat-jenderal.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                    'submenus' => [
                                        ['name' => 'Dashboard Sekjen', 'route' => 'sekretariat-jenderal.dashboard', 'icon' => 'ri-pie-chart-box-line', 'active_on_prefixes' => ['sekretariat-jenderal.dashboard'], 'roles' => array_merge([App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles)],
                                        ['name' => 'Jumlah MoU', 'route' => 'sekretariat-jenderal.progress-mou.index', 'icon' => 'ri-honour-line', 'roles' => [App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Jumlah regulasi baru', 'route' => 'sekretariat-jenderal.jumlah-regulasi-baru.index', 'icon' => 'ri-file-list-3-line', 'roles' => [App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Jumlah penanganan kasus', 'route' => 'sekretariat-jenderal.jumlah-penanganan-kasus.index', 'icon' => 'ri-scales-2-line', 'roles' => [App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Jumlah penyelesaian BMN', 'route' => 'sekretariat-jenderal.penyelesaian-bmn.index', 'icon' => 'ri-archive-drawer-line', 'roles' => [App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => '% Kehadiran', 'route' => 'sekretariat-jenderal.persentase-kehadiran.index', 'icon' => 'ri-user-follow-line', 'roles' => [App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Monev monitoring media', 'route' => 'sekretariat-jenderal.monev-monitoring-media.index', 'icon' => 'ri-rss-line', 'roles' => [App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Lulusan Polteknaker bekerja', 'route' => 'sekretariat-jenderal.lulusan-polteknaker-bekerja.index', 'icon' => 'ri-user-star-line', 'roles' => [App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'SDM mengikuti pelatihan', 'route' => 'sekretariat-jenderal.sdm-mengikuti-pelatihan.index', 'icon' => 'ri-team-line', 'roles' => [App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Indikator Kinerja Pelaksanaan Anggaran', 'route' => 'sekretariat-jenderal.ikpa.index', 'icon' => 'ri-money-dollar-circle-line', 'roles' => [App\Models\User::ROLE_SEKJEN, App\Models\User::ROLE_SUPERADMIN]],
                                    ]
                                ],
                                'Binapenta' => [
                                    'icon' => 'ri-user-search-line',
                                    'route' => 'binapenta.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_BINAPENTA, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                    'submenus' => [
                                        ['name' => 'Dashboard Binapenta', 'route' => 'binapenta.dashboard', 'icon' => 'ri-pie-chart-box-line', 'active_on_prefixes' => ['binapenta.dashboard'], 'roles' => array_merge([App\Models\User::ROLE_BINAPENTA, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles)],
                                        ['name' => 'Jml Penempatan oleh Kemnaker', 'route' => 'binapenta.jumlah-penempatan-kemnaker.index', 'icon' => 'ri-user-add-line', 'roles' => [App\Models\User::ROLE_BINAPENTA, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Jml Lowongan Kerja Baru (Pasker)', 'route' => 'binapenta.jumlah-lowongan-pasker.index', 'icon' => 'ri-briefcase-4-line', 'roles' => [App\Models\User::ROLE_BINAPENTA, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Persetujuan RPTKA', 'route' => 'binapenta.persetujuan-rptka.index', 'icon' => 'ri-user-shared-line', 'roles' => [App\Models\User::ROLE_BINAPENTA, App\Models\User::ROLE_SUPERADMIN]],
                                    ]
                                ],
                                'Binalavotas' => [
                                    'icon' => 'ri-graduation-cap-line',
                                    'route' => 'binalavotas.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_BINALAVOTAS, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                    'submenus' => [
                                        ['name' => 'Dashboard Binalavotas', 'route' => 'binalavotas.dashboard', 'icon' => 'ri-pie-chart-box-line', 'active_on_prefixes' => ['binalavotas.dashboard'], 'roles' => array_merge([App\Models\User::ROLE_BINALAVOTAS, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles)],
                                        ['name' => 'Jumlah Kepesertaan Pelatihan', 'route' => 'binalavotas.jumlah-kepesertaan-pelatihan.index', 'icon' => 'ri-group-line', 'roles' => [App\Models\User::ROLE_BINALAVOTAS, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Jml Sertifikasi Kompetensi', 'route' => 'binalavotas.jumlah-sertifikasi-kompetensi.index', 'icon' => 'ri-shield-star-line', 'roles' => [App\Models\User::ROLE_BINALAVOTAS, App\Models\User::ROLE_SUPERADMIN]],
                                    ]
                                ],
                                'Binwasnaker & K3' => [
                                    'icon' => 'ri-shield-check-line',
                                    'route' => 'binwasnaker.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_BINWASNAKER, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                    'submenus' => [
                                        ['name' => 'Dashboard Binwasnaker', 'route' => 'binwasnaker.dashboard', 'icon' => 'ri-pie-chart-box-line', 'active_on_prefixes' => ['binwasnaker.dashboard'], 'roles' => array_merge([App\Models\User::ROLE_BINWASNAKER, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles)],
                                        ['name' => 'Laporan WLKP Online', 'route' => 'binwasnaker.pelaporan-wlkp-online.index', 'icon' => 'ri-computer-line', 'roles' => [App\Models\User::ROLE_BINWASNAKER, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Pengaduan Pelanggaran Norma (TL)', 'route' => 'binwasnaker.pengaduan-pelanggaran-norma.index', 'icon' => 'ri-alert-line', 'roles' => [App\Models\User::ROLE_BINWASNAKER, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Penerapan SMK3', 'route' => 'binwasnaker.penerapan-smk3.index', 'icon' => 'ri-shield-keyhole-line', 'roles' => [App\Models\User::ROLE_BINWASNAKER, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Self-Assessment Norma 100', 'route' => 'binwasnaker.self-assessment-norma100.index', 'icon' => 'ri-check-double-line', 'roles' => [App\Models\User::ROLE_BINWASNAKER, App\Models\User::ROLE_SUPERADMIN]],
                                    ]
                                ],
                                'PHI & JAMSOS' => [
                                    'icon' => 'ri-scales-3-line',
                                    'route' => 'phi.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_PHI, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                    'submenus' => [
                                        ['name' => 'Dashboard PHI', 'route' => 'phi.dashboard', 'icon' => 'ri-pie-chart-box-line', 'active_on_prefixes' => ['phi.dashboard'], 'roles' => array_merge([App\Models\User::ROLE_PHI, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles)],
                                        ['name' => 'Jumlah PHK', 'route' => 'phi.jumlah-phk.index', 'icon' => 'ri-user-unfollow-fill', 'roles' => [App\Models\User::ROLE_PHI, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Perselisihan (TL)', 'route' => 'phi.perselisihan-ditindaklanjuti.index', 'icon' => 'ri-auction-line', 'roles' => [App\Models\User::ROLE_PHI, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Mediasi Berhasil', 'route' => 'phi.mediasi-berhasil.index', 'icon' => 'ri-shake-hands-line', 'roles' => [App\Models\User::ROLE_PHI, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Perusahaan Penerap SUSU', 'route' => 'phi.perusahaan-menerapkan-susu.index', 'icon' => 'ri-currency-line', 'roles' => [App\Models\User::ROLE_PHI, App\Models\User::ROLE_SUPERADMIN]],
                                    ]
                                ],
                                'Barenbang' => [
                                    'icon' => 'ri-bar-chart-box-line',
                                    'route' => 'barenbang.dashboard',
                                    'roles' => array_merge([App\Models\User::ROLE_BARENBANG, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles),
                                    'submenus' => [
                                        ['name' => 'Dashboard Barenbang', 'route' => 'barenbang.dashboard', 'icon' => 'ri-pie-chart-box-line', 'active_on_prefixes' => ['barenbang.dashboard'], 'roles' => array_merge([App\Models\User::ROLE_BARENBANG, App\Models\User::ROLE_SUPERADMIN], $readOnlySpecificRoles)],
                                        ['name' => 'Jml Kajian & Rekomendasi', 'route' => 'barenbang.jumlah-kajian-rekomendasi.index', 'icon' => 'ri-lightbulb-flash-line', 'roles' => [App\Models\User::ROLE_BARENBANG, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Data Ketenagakerjaan', 'route' => 'barenbang.data-ketenagakerjaan.index', 'icon' => 'ri-database-2-line', 'roles' => [App\Models\User::ROLE_BARENBANG, App\Models\User::ROLE_SUPERADMIN]],
                                        ['name' => 'Jml Aplikasi Terintegrasi SiapKerja', 'route' => 'barenbang.aplikasi-integrasi-siapkerja.index', 'icon' => 'ri-link-m', 'roles' => [App\Models\User::ROLE_BARENBANG, App\Models\User::ROLE_SUPERADMIN]],
                                    ]
                                ],
                            ];
                        }
                    @endphp

                    @if (Auth::check())
                        @foreach ($sidebarMenu as $deptName => $deptDetails)
                            @php
                                $canAccessParent = false;
                                // Pengecekan untuk parent menu: jika user punya salah satu role yang didefinisikan di $deptDetails['roles']
                                if ($user && isset($deptDetails['roles']) && is_array($deptDetails['roles'])) {
                                    foreach ($deptDetails['roles'] as $role) {
                                        if ($user->hasRole($role)) {
                                            $canAccessParent = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp

                            @if ($canAccessParent)
                                @php
                                    $parentSlug = Str::slug($deptName);
                                    $hasActiveChild = false;
                                    if (isset($deptDetails['route']) && Route::has($deptDetails['route']) && $currentRouteName == $deptDetails['route']) {
                                        $hasActiveChild = true;
                                    }
                                    if (!$hasActiveChild && !empty($deptDetails['submenus'])) {
                                        foreach ($deptDetails['submenus'] as $submenu) {
                                            // Cek apakah submenu ini boleh diakses oleh role user saat ini
                                            $canAccessSubmenu = false;
                                            if (isset($submenu['roles']) && is_array($submenu['roles'])) {
                                                foreach($submenu['roles'] as $smRole) {
                                                    if ($user->hasRole($smRole)) {
                                                        $canAccessSubmenu = true;
                                                        break;
                                                    }
                                                }
                                            } else {
                                                // Jika submenu tidak mendefinisikan 'roles', asumsikan semua yang bisa lihat parent bisa lihat submenu ini
                                                // ATAU defaultnya adalah role parent. Untuk kasus kita, lebih baik definisikan roles di tiap submenu.
                                                // Untuk amannya, jika tidak ada 'roles' di submenu, kita bisa samakan dengan role parent
                                                // atau hanya izinkan superadmin jika tidak spesifik.
                                                // Tapi karena kita sudah tambahkan 'roles' di semua submenu penting, ini seharusnya aman.
                                                // Jika submenu data-CRUD tidak punya 'roles', maka user read-only bisa melihatnya, ini yang kita hindari.
                                                // Jadi, pastikan semua submenu memiliki 'roles' yang sesuai.
                                                if (isset($deptDetails['roles']) && is_array($deptDetails['roles'])) {
                                                    foreach ($deptDetails['roles'] as $role) { // Default ke role parent jika submenu tidak spesifik
                                                        if ($user->hasRole($role)) {
                                                            $canAccessSubmenu = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }

                                            if ($canAccessSubmenu && isSubmenuActive($submenu, $currentRouteName)) {
                                                $hasActiveChild = true;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <div class="mb-1 sidebar-parent-item {{ $hasActiveChild ? 'expanded active-parent' : '' }}">
                                    <a href="{{ isset($deptDetails['route']) && Route::has($deptDetails['route']) ? route($deptDetails['route']) : '#' }}"
                                       onclick="{{ !empty($deptDetails['submenus']) && (!isset($deptDetails['route']) || $deptDetails['route'] === '#') ? "event.preventDefault(); toggleSubmenu('".$parentSlug."');" : ((!empty($deptDetails['submenus'])) ? "toggleSubmenu('".$parentSlug."');" : "") }}"
                                       class="sidebar-parent-button flex items-center justify-between w-full px-4 py-2.5 text-gray-700 focus:outline-none">
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 flex items-center justify-center mr-2 main-menu-icon"><i class="{{ $deptDetails['icon'] }}"></i></div>
                                            <span class="text-sm">{{ $deptName }}</span>
                                        </div>
                                        @if (!empty($deptDetails['submenus']))
                                            @php
                                                // Cek apakah ada setidaknya satu submenu yang bisa diakses user saat ini
                                                $hasVisibleSubmenus = false;
                                                foreach ($deptDetails['submenus'] as $submenu) {
                                                    if (isset($submenu['roles']) && is_array($submenu['roles'])) {
                                                        foreach($submenu['roles'] as $smRole) {
                                                            if ($user->hasRole($smRole)) {
                                                                $hasVisibleSubmenus = true;
                                                                break 2;
                                                            }
                                                        }
                                                    } else { // Jika tidak ada roles di submenu, asumsikan bisa dilihat jika parent bisa dilihat
                                                       $hasVisibleSubmenus = true; break;
                                                    }
                                                }
                                            @endphp
                                            @if($hasVisibleSubmenus)
                                                <i id="arrow-{{ $parentSlug }}" class="arrow-icon ri-arrow-right-s-line text-lg text-gray-500 transition-transform duration-300 {{ $hasActiveChild ? 'transform rotate-90' : '' }}"></i>
                                            @endif
                                        @endif
                                    </a>

                                    @if (!empty($deptDetails['submenus']))
                                        <div class="submenu-list {{ $hasActiveChild ? 'expanded' : '' }}" id="submenu-{{ $parentSlug }}">
                                            <div class="pt-1 pb-2">
                                                @foreach ($deptDetails['submenus'] as $submenu)
                                                    @php
                                                        $canAccessThisSubmenu = false;
                                                        if (isset($submenu['roles']) && is_array($submenu['roles'])) {
                                                            foreach($submenu['roles'] as $smRole) {
                                                                if ($user->hasRole($smRole)) {
                                                                    $canAccessThisSubmenu = true;
                                                                    break;
                                                                }
                                                            }
                                                        } else {
                                                            // Default behavior jika 'roles' tidak ada di submenu
                                                            // Anda bisa set $canAccessThisSubmenu = true; jika ingin semua submenu terlihat jika parent terlihat
                                                            // atau $canAccessThisSubmenu = $user->isSuperAdmin(); jika hanya superadmin
                                                            // Untuk amannya, jika tidak didefinisikan, anggap bisa diakses jika parent bisa diakses (sesuai $canAccessParent)
                                                            // Namun, karena kita sudah mendefinisikan 'roles' di semua submenu penting, ini seharusnya aman.
                                                            // Jika submenu adalah link CRUD, harus ada 'roles' yang spesifik.
                                                            // Untuk link dashboard, kita sudah tambahkan $readOnlySpecificRoles.
                                                            $canAccessThisSubmenu = true; // Default jika tidak ada roles spesifik di submenu
                                                        }
                                                    @endphp

                                                    @if($canAccessThisSubmenu)
                                                        @if(isset($submenu['is_header']) && $submenu['is_header'])
                                                            <div class="px-6 py-1 text-xs font-semibold text-gray-400 uppercase mt-1">{{ $submenu['name'] }}</div>
                                                        @else
                                                            <a href="{{ $submenu['route'] === '#' ? '#' : (Route::has($submenu['route']) ? route($submenu['route']) : '#!') }}"
                                                               class="sidebar-submenu-item flex items-center w-full py-1.5 pr-4
                                                                      {{ (isset($submenu['is_sub_item']) && $submenu['is_sub_item']) ? 'pl-10' : 'pl-6' }}
                                                                      text-xs text-gray-600 hover:text-primary
                                                                      {{ isSubmenuActive($submenu, $currentRouteName) ? 'active' : '' }}">
                                                                @if(isset($submenu['icon']))
                                                                <div class="w-5 h-5 flex items-center justify-center mr-2 opacity-75"><i class="{{ $submenu['icon'] }}"></i></div>
                                                                @else
                                                                <div class="w-5 h-5 mr-2"></div>
                                                                @endif
                                                                <span>{{ $submenu['name'] }}</span>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @endif
                </nav>
            </div>
            {{-- ... (User Info & Logout tetap sama) ... --}}
            <div class="p-4 border-t border-gray-100 mt-auto">
                @if (Auth::check())
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="ri-user-line text-gray-600"></i>
                        </div>
                        <div class="ml-2">
                            <div class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ Str::ucfirst(Auth::user()->role) }}</div>
                        </div>
                        <div class="ml-auto">
                            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="button" onclick="confirmLogout()"
                                        class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-primary" title="Logout">
                                    <i class="ri-logout-box-r-line"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                     <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
                        Login
                    </a>
                @endif
            </div>
        </div>

        {{-- ... (Main Content Area tetap sama) ... --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm z-10 sticky top-0">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <div class="flex items-center">
                        <button id="sidebarToggle" class="lg:hidden text-gray-500 hover:text-primary focus:outline-none mr-3">
                            <div class="w-6 h-6 flex items-center justify-center">
                                <i class="ri-menu-line text-xl"></i>
                            </div>
                        </button>
                        <div class="text-lg font-semibold text-gray-800 lg:ml-0">@yield('page_title', 'Dashboard')</div>
                    </div>
                    {{-- ... (bagian search dan notifikasi tetap sama) ... --}}
                </div>
                 @hasSection('header_filters')
                    <div class="px-4 sm:px-6 py-3 border-t border-gray-100 flex flex-col md:flex-row items-stretch md:items-center md:justify-between gap-3 md:gap-4">
                        @yield('header_filters')
                    </div>
                 @endif
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50">
                 {{-- ... (Session messages dan error handling tetap sama) ... --}}
                 @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-md text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any() && !session('import_errors'))
                    <div class="mb-4 p-3 bg-red-100 border-red-300 text-red-700 rounded-md text-sm">
                        <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
                        <ul class="mt-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                 @if (session('import_errors'))
                    <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">
                        <strong class="font-bold">Beberapa data gagal diimpor karena kesalahan validasi:</strong>
                        <ul class="mt-1 list-disc list-inside text-xs">
                            @foreach (session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
        <div id="mainContentOverlay" class="fixed inset-0 bg-black bg-opacity-25 z-20 hidden lg:hidden"></div>
    </div>
    {{-- ... (JavaScript tetap sama) ... --}}
    <script>
        // JavaScript untuk toggle sidebar mobile & submenu (sama seperti sebelumnya)
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const closeSidebarButton = document.getElementById('closeSidebar');
            const mainContentOverlay = document.getElementById('mainContentOverlay');

            function openSidebar() {
                if (sidebar) sidebar.classList.remove('-translate-x-full');
                if (mainContentOverlay) mainContentOverlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.add('-translate-x-full');
                if (mainContentOverlay) mainContentOverlay.classList.add('hidden');
                 document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', (e) => { e.stopPropagation(); openSidebar(); });
            }
            if (closeSidebarButton) {
                closeSidebarButton.addEventListener('click', (e) => { e.stopPropagation(); closeSidebar(); });
            }
            if (mainContentOverlay) {
                mainContentOverlay.addEventListener('click', () => { closeSidebar(); });
            }

            document.addEventListener('click', function(event) {
                if (!sidebar || !sidebarToggle || !event.target) return;
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggler = sidebarToggle.contains(event.target);
                const sidebarVisible = !sidebar.classList.contains('-translate-x-full');

                if (!isClickInsideSidebar && !isClickOnToggler && sidebarVisible && window.innerWidth < 1024) {
                    closeSidebar();
                }
            });

            window.toggleSubmenu = function(submenuIdBase) {
                const submenu = document.getElementById('submenu-' + submenuIdBase);
                const arrow = document.getElementById('arrow-' + submenuIdBase);
                const parentItem = arrow ? arrow.closest('.sidebar-parent-item') : null;

                if (submenu) {
                    submenu.classList.toggle('expanded');
                    if (arrow) {
                        arrow.classList.toggle('rotate-90');
                    }
                    if(parentItem){
                        parentItem.classList.toggle('expanded');
                    }
                }
            }
        });

        function confirmLogout() {
            if (confirm("Anda ingin keluar?")) {
                document.getElementById('logout-form').submit();
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
