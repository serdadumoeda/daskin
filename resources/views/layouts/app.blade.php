<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kemnaker Dashboard')</title>

    <script src="https://cdn.tailwindcss.com/3.4.1"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6', // biru
                        secondary: '#64748b', // abu-abu
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
             border-width: 1px; /* Ensure border is visible */
             border-color: #d1d5db; /* border-gray-300 */
             border-radius: theme('borderRadius.button'); /* 8px */
             box-shadow: theme('boxShadow.sm');
        }
        .form-input:focus {
            border-color: theme('colors.primary');
            --tw-ring-color: theme('colors.primary');
            box-shadow: 0 0 0 2px theme('ringOpacity.50', 'colors.primary');
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
                <a href="{{ url('/') }}" class="font-['Pacifico'] text-xl text-primary" style="font-family: 'Pacifico', cursive;">
                     Kemnaker </a>
                <button id="closeSidebar" class="lg:hidden text-gray-500 hover:text-primary">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <div class="overflow-y-auto flex-1">
                <nav class="py-2">
                    @php
                        $currentRouteName = Route::currentRouteName();

                        if (!function_exists('isSubmenuActive')) {
                            function isSubmenuActive($submenu, $currentRouteName) {
                                if (isset($submenu['route']) && $submenu['route'] !== '#') {
                                    if ($currentRouteName == $submenu['route']) return true;
                                    // Cek jika route saat ini adalah bagian dari resource controller submenu
                                    // atau cocok dengan active_on_prefixes jika ada
                                    $baseRouteForResource = explode('.index', $submenu['route'])[0];
                                    if (str_starts_with($currentRouteName ?? '', $baseRouteForResource . '.')) return true;
                                    
                                    if (isset($submenu['active_on_prefixes']) && is_array($submenu['active_on_prefixes'])) {
                                        foreach($submenu['active_on_prefixes'] as $prefix){
                                            if(str_starts_with($currentRouteName ?? '', $prefix)) return true;
                                        }
                                    }
                                }
                                return false;
                            }
                        }
                        
                        // Definisikan menu Anda di sini
                        // Pastikan nama route ('route') sudah benar dan lengkap sesuai definisi di web.php
                        $sidebarMenu = [
                            'Inspektorat Jenderal' => [
                                'icon' => 'ri-government-line',
                                'submenus' => [
                                    ['name' => '% Progres Tindak Lanjut temuan BPK', 'route' => 'inspektorat.progress-temuan-bpk.index', 'icon' => 'ri-file-chart-line'],
                                    ['name' => '% Progres Tindak Lanjut temuan internal', 'route' => 'inspektorat.progress-temuan-internal.index', 'icon' => 'ri-file-search-line'],
                                ]
                            ],
                            'Sekretariat Jenderal' => [
                                'icon' => 'ri-building-4-line',
                                'submenus' => [
                                    ['name' => 'Jumlah MoU', 'route' => 'sekretariat-jenderal.progress-mou.index', 'icon' => 'ri-honour-line'],
                                    ['name' => 'Jumlah regulasi baru', 'route' => 'sekretariat-jenderal.jumlah-regulasi-baru.index', 'icon' => 'ri-file-list-3-line'],
                                    ['name' => 'Jumlah penanganan kasus', 'route' => 'sekretariat-jenderal.jumlah-penanganan-kasus.index', 'icon' => 'ri-scales-2-line'],
                                    ['name' => 'Jumlah penyelesaian BMN', 'route' => 'sekretariat-jenderal.penyelesaian-bmn.index', 'icon' => 'ri-archive-drawer-line'],
                                    ['name' => '% Kehadiran', 'route' => 'sekretariat-jenderal.persentase-kehadiran.index', 'icon' => 'ri-user-follow-line'],
                                    ['name' => 'Monev monitoring media', 'route' => 'sekretariat-jenderal.monev-monitoring-media.index', 'icon' => 'ri-rss-line'],
                                    ['name' => 'Lulusan Polteknaker bekerja', 'route' => 'sekretariat-jenderal.lulusan-polteknaker-bekerja.index', 'icon' => 'ri-user-star-line'],
                                    ['name' => 'SDM mengikuti pelatihan', 'route' => 'sekretariat-jenderal.sdm-mengikuti-pelatihan.index', 'icon' => 'ri-team-line'],
                                ]
                            ],
                            'Binapenta' => [ 
                                'icon' => 'ri-user-search-line',
                                'submenus' => [
                                    ['name' => 'Jml Penempatan oleh Kemnaker', 'route' => '#', 'icon' => 'ri-user-add-line'], // Ganti '#' dengan route name yang benar
                                    ['name' => 'Jml Lowongan Kerja Baru (Pasker)', 'route' => '#', 'icon' => 'ri-briefcase-4-line'],
                                    ['name' => 'Jml TKA Disetujui', 'route' => '#', 'icon' => 'ri-user-shared-line'],
                                    ['name' => 'Jml TKA Tidak Disetujui', 'route' => '#', 'icon' => 'ri-user-unfollow-line'],
                                    ['name' => 'Jml Penempatan Disabilitas', 'route' => '#', 'icon' => 'ri-wheelchair-line'],
                                ]
                            ],
                            'Binalavotas' => [ 
                                'icon' => 'ri-graduation-cap-line',
                                'submenus' => [
                                    ['name' => 'Jml Lulus Pelatihan Internal', 'route' => '#', 'icon' => 'ri-medal-line'],
                                    ['name' => 'Jml Lulus Pelatihan Eksternal', 'route' => '#', 'icon' => 'ri-award-line'],
                                    ['name' => 'Jml Sertifikasi Kompetensi', 'route' => '#', 'icon' => 'ri-shield-star-line'],
                                ]
                            ],
                            'Binwasnaker' => [ 
                                'icon' => 'ri-shield-check-line',
                                'submenus' => [
                                    ['name' => 'Laporan WLKP Online', 'route' => 'binwasnaker.pelaporan-wlkp-online.index', 'icon' => 'ri-computer-line'],
                                    ['name' => 'Pengaduan Pelanggaran Norma (TL)', 'route' => 'binwasnaker.pengaduan-pelanggaran-norma.index', 'icon' => 'ri-alert-line'],
                                    ['name' => 'Penerapan SMK3', 'route' => 'binwasnaker.penerapan-smk3.index', 'icon' => 'ri-shield-keyhole-line'],
                                    ['name' => 'Self-Assessment Norma 100', 'route' => 'binwasnaker.self-assessment-norma100.index', 'icon' => 'ri-check-double-line'],
                                ]
                            ],
                            'PHI' => [ 
                                'icon' => 'ri-scales-3-line',
                                'submenus' => [
                                    ['name' => 'Jumlah PHK', 'route' => 'phi.jumlah-phk.index', 'icon' => 'ri-user-unfollow-fill'],
                                    ['name' => 'Perselisihan (TL)', 'route' => 'phi.perselisihan-ditindaklanjuti.index', 'icon' => 'ri-auction-line'],
                                    ['name' => 'Mediasi Berhasil', 'route' => 'phi.mediasi-berhasil.index', 'icon' => 'ri-shake-hands-line'],
                                    ['name' => 'Perusahaan Penerap SUSU', 'route' => 'phi.perusahaan-menerapkan-susu.index', 'icon' => 'ri-currency-line'],
                                ]
                            ],
                            'Barenbang' => [ 
                                'icon' => 'ri-bar-chart-box-line',
                                'submenus' => [
                                    ['name' => 'Jml Kajian & Rekomendasi', 'route' => '#', 'icon' => 'ri-lightbulb-flash-line'],
                                    ['name' => 'Data Ketenagakerjaan', 'route' => null, 'is_header' => true], // Ini akan jadi sub-judul
                                    ['name' => 'Jumlah Tenaga Kerja', 'route' => '#', 'icon' => 'ri-group-line', 'is_sub_item' => true],
                                    ['name' => 'Jml Lowongan Kerja Baru (Agg.)', 'route' => '#', 'icon' => 'ri-briefcase-line', 'is_sub_item' => true],
                                    ['name' => 'Jml Aplikasi Terintegrasi SiapKerja', 'route' => '#', 'icon' => 'ri-link-m', 'is_sub_item' => true],
                                ]
                            ],
                        ];
                    @endphp

                    @foreach ($sidebarMenu as $deptName => $deptDetails)
                        @php
                            $parentSlug = Str::slug($deptName);
                            $hasActiveChild = false; // Cek apakah ada submenu yang aktif di bawah parent ini
                            if (!empty($deptDetails['submenus'])) {
                                foreach ($deptDetails['submenus'] as $submenu) {
                                    if (isSubmenuActive($submenu, $currentRouteName)) {
                                        $hasActiveChild = true;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        <div class="mb-1 sidebar-parent-item {{ $hasActiveChild ? 'expanded active-parent' : '' }}">
                            <button type="button" 
                                    class="sidebar-parent-button flex items-center justify-between w-full px-4 py-2.5 text-gray-700 focus:outline-none"
                                    onclick="toggleSubmenu('{{ $parentSlug }}')">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 flex items-center justify-center mr-2 main-menu-icon"><i class="{{ $deptDetails['icon'] }}"></i></div>
                                    <span class="text-sm">{{ $deptName }}</span>
                                </div>
                                @if (!empty($deptDetails['submenus']))
                                    <i id="arrow-{{ $parentSlug }}" class="arrow-icon ri-arrow-right-s-line text-lg text-gray-500 transition-transform duration-300 {{ $hasActiveChild ? 'transform rotate-90' : '' }}"></i>
                                @endif
                            </button>
                            
                            @if (!empty($deptDetails['submenus']))
                                <div class="submenu-list {{ $hasActiveChild ? 'expanded' : '' }}" id="submenu-{{ $parentSlug }}">
                                    <div class="pt-1 pb-2">
                                        @foreach ($deptDetails['submenus'] as $submenu)
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
                                                    <div class="w-5 h-5 mr-2"></div> @endif
                                                    <span>{{ $submenu['name'] }}</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </nav>
            </div>
            <div class="p-4 border-t border-gray-100 mt-auto">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="ri-user-line text-gray-600"></i>
                    </div>
                    <div class="ml-2">
                        <div class="text-sm font-medium text-gray-700">{{ Auth::check() ? Auth::user()->name : 'Pengguna Tamu' }}</div>
                        <div class="text-xs text-gray-500">{{ Auth::check() && Auth::user()->role ? Auth::user()->role : 'Role Pengguna' }}</div>
                    </div>
                    <div class="ml-auto">
                         <button class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-primary" title="Settings">
                            <i class="ri-settings-3-line"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <div class="relative hidden md:block">
                            <div class="flex items-center bg-gray-100 rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-primary focus-within:ring-opacity-50">
                                <div class="w-4 h-4 flex items-center justify-center text-gray-500"><i class="ri-search-line"></i></div>
                                <input type="text" placeholder="Search..." class="ml-2 bg-transparent border-none outline-none text-sm text-gray-700 placeholder-gray-500 w-32 md:w-48">
                            </div>
                        </div>
                        <div class="flex items-center space-x-1 sm:space-x-3">
                            <button class="relative w-8 h-8 flex items-center justify-center text-gray-500 hover:text-primary">
                                <i class="ri-notification-3-line text-xl"></i>
                                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                        </div>
                    </div>
                </div>
                 @hasSection('header_filters')
                    <div class="px-4 sm:px-6 py-3 border-t border-gray-100 flex flex-col md:flex-row items-stretch md:items-center md:justify-between gap-3 md:gap-4">
                        @yield('header_filters')
                    </div>
                 @endif
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50">
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
    <script>
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
    </script>
    @stack('scripts')
</body>
</html>
