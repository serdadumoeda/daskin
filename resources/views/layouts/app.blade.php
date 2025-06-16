<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kemnaker Dashboard')</title>
    <link rel="icon" href="{{ asset('image/logo/logo_kemnaker.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>

    {{-- ================== PERUBAHAN 1: TAMBAHKAN SCRIPT ALPINEJS ================== --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .sidebar-parent-button { transition: background-color 0.3s ease, color 0.3s ease; border-left: 3px solid transparent; padding-left: calc(1rem - 3px); }
        .sidebar-parent-button:hover { background-color: #3E8785; color: #ffffff; }
        .sidebar-parent-button.active-parent, .sidebar-parent-button.expanded { background-color: #3E8785 !important; color: #ffffff !important; border-left-color: #FFBF00 !important; }
        .sidebar-submenu-item { padding-left: calc(1.5rem - 3px); border-left: 3px solid transparent; transition: background-color 0.3s ease, color 0.3s ease; }
        .sidebar-submenu-item:hover { background-color: #3E8785; color: #ffffff; }
        .sidebar-submenu-item.active { background-color: #3E8785 !important; color: #ffffff !important; font-weight: 500; border-left-color: #FFBF00 !important; }
        .submenu-list { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-in-out; background-color: rgba(0,0,0,0.1); }
        .submenu-list.expanded { max-height: 1000px; }
    </style>
</head>
<body class="font-inter bg-gray-50">
    <div class="flex h-screen overflow-hidden bg-gray-100">
        <div id="sidebar" class="bg-sidebar-bg w-64 shadow-md flex flex-col h-full fixed inset-y-0 left-0 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:relative">
            <div class="p-4 flex items-center justify-between border-b border-sidebar-border-color">
                <a href="{{ route('dashboard') }}"><span class="text-white font-semibold text-lg">Dashboard Kinerja</span></a>
                <button id="closeSidebar" class="lg:hidden text-sidebar-text hover:text-white"><i class="ri-close-line text-2xl"></i></button>
            </div>
            <div class="overflow-y-auto flex-1 pt-2">
                <nav class="py-2">
                    @php
                        $user = Auth::user();
                        $currentRouteName = Route::currentRouteName();

                        if (!function_exists('isSubmenuActive')) {
                            function isSubmenuActive($submenu, $currentRouteName) {
                                if (isset($submenu['route']) && $submenu['route'] !== '#') {
                                    if ($currentRouteName == $submenu['route']) return true;
                                    $baseRoute = explode('.index', $submenu['route'])[0];
                                    if (str_starts_with($currentRouteName ?? '', $baseRoute)) return true;
                                }
                                return false;
                            }
                        }
                        
                        $readOnlySpecificRoles = ['menteri', 'wakil_menteri', 'staff_khusus', 'user'];
                        $sidebarMenu = [
                            'Dashboard Utama' => ['icon' => 'ri-home-smile-line', 'route' => 'dashboard', 'roles' => array_merge(['superadmin', 'itjen', 'sekjen', 'binapenta', 'binalavotas', 'binwasnaker', 'phi', 'barenbang'], $readOnlySpecificRoles)],
                            'Inspektorat Jenderal' => ['icon' => 'ri-government-line', 'route' => 'inspektorat.dashboard', 'roles' => array_merge(['itjen', 'superadmin'], $readOnlySpecificRoles), 'submenus' => [['name' => '% Progres Tindak Lanjut temuan BPK', 'route' => 'inspektorat.progress-temuan-bpk.index', 'icon' => 'ri-file-chart-line', 'roles' => ['itjen', 'superadmin']], ['name' => '% Progres Tindak Lanjut temuan internal', 'route' => 'inspektorat.progress-temuan-internal.index', 'icon' => 'ri-file-search-line', 'roles' => ['itjen', 'superadmin']]]],
                            'Sekretariat Jenderal' => ['icon' => 'ri-building-4-line', 'route' => 'sekretariat-jenderal.dashboard', 'roles' => array_merge(['sekjen', 'superadmin'], $readOnlySpecificRoles), 'submenus' => [['name' => 'Jumlah MoU', 'route' => 'sekretariat-jenderal.progress-mou.index', 'icon' => 'ri-honour-line', 'roles' => ['sekjen', 'superadmin']], ['name' => 'Jumlah regulasi baru', 'route' => 'sekretariat-jenderal.jumlah-regulasi-baru.index', 'icon' => 'ri-file-list-3-line', 'roles' => ['sekjen', 'superadmin']], ['name' => 'Jumlah penanganan kasus', 'route' => 'sekretariat-jenderal.jumlah-penanganan-kasus.index', 'icon' => 'ri-scales-2-line', 'roles' => ['sekjen', 'superadmin']], ['name' => 'Jumlah penyelesaian BMN', 'route' => 'sekretariat-jenderal.penyelesaian-bmn.index', 'icon' => 'ri-archive-drawer-line', 'roles' => ['sekjen', 'superadmin']], ['name' => 'Kehadiran', 'route' => 'sekretariat-jenderal.persentase-kehadiran.index', 'icon' => 'ri-user-follow-line', 'roles' => ['sekjen', 'superadmin']], ['name' => 'Monev monitoring media', 'route' => 'sekretariat-jenderal.monev-monitoring-media.index', 'icon' => 'ri-rss-line', 'roles' => ['sekjen', 'superadmin']], ['name' => 'Lulusan Polteknaker bekerja', 'route' => 'sekretariat-jenderal.lulusan-polteknaker-bekerja.index', 'icon' => 'ri-user-star-line', 'roles' => ['sekjen', 'superadmin']], ['name' => 'SDM mengikuti pelatihan', 'route' => 'sekretariat-jenderal.sdm-mengikuti-pelatihan.index', 'icon' => 'ri-team-line', 'roles' => ['sekjen', 'superadmin']], ['name' => 'Indikator Kinerja Pelaksanaan Anggaran', 'route' => 'sekretariat-jenderal.ikpa.index', 'icon' => 'ri-money-dollar-circle-line', 'roles' => ['sekjen', 'superadmin']]]],
                            'Binapenta' => ['icon' => 'ri-user-search-line', 'route' => 'binapenta.dashboard', 'roles' => array_merge(['binapenta', 'superadmin'], $readOnlySpecificRoles), 'submenus' => [['name' => 'Jml Penempatan oleh Kemnaker', 'route' => 'binapenta.jumlah-penempatan-kemnaker.index', 'icon' => 'ri-user-add-line', 'roles' => ['binapenta', 'superadmin']], ['name' => 'Jml Lowongan Kerja Baru (Pasker)', 'route' => 'binapenta.jumlah-lowongan-pasker.index', 'icon' => 'ri-briefcase-4-line', 'roles' => ['binapenta', 'superadmin']], ['name' => 'Persetujuan RPTKA', 'route' => 'binapenta.persetujuan-rptka.index', 'icon' => 'ri-user-shared-line', 'roles' => ['binapenta', 'superadmin']]]],
                            'Binalavotas' => ['icon' => 'ri-graduation-cap-line', 'route' => 'binalavotas.dashboard', 'roles' => array_merge(['binalavotas', 'superadmin'], $readOnlySpecificRoles), 'submenus' => [['name' => 'Jumlah Kepesertaan Pelatihan', 'route' => 'binalavotas.jumlah-kepesertaan-pelatihan.index', 'icon' => 'ri-group-line', 'roles' => ['binalavotas', 'superadmin']], ['name' => 'Jml Sertifikasi Kompetensi', 'route' => 'binalavotas.jumlah-sertifikasi-kompetensi.index', 'icon' => 'ri-shield-star-line', 'roles' => ['binalavotas', 'superadmin']]]],
                            'Binwasnaker & K3' => ['icon' => 'ri-shield-check-line', 'route' => 'binwasnaker.dashboard', 'roles' => array_merge(['binwasnaker', 'superadmin'], $readOnlySpecificRoles), 'submenus' => [['name' => 'Laporan WLKP Online', 'route' => 'binwasnaker.pelaporan-wlkp-online.index', 'icon' => 'ri-computer-line', 'roles' => ['binwasnaker', 'superadmin']], ['name' => 'Pengaduan Pelanggaran Norma (TL)', 'route' => 'binwasnaker.pengaduan-pelanggaran-norma.index', 'icon' => 'ri-alert-line', 'roles' => ['binwasnaker', 'superadmin']], ['name' => 'Penerapan SMK3', 'route' => 'binwasnaker.penerapan-smk3.index', 'icon' => 'ri-shield-keyhole-line', 'roles' => ['binwasnaker', 'superadmin']], ['name' => 'Self-Assessment Norma 100', 'route' => 'binwasnaker.self-assessment-norma100.index', 'icon' => 'ri-check-double-line', 'roles' => ['binwasnaker', 'superadmin']]]],
                            'PHI & JAMSOS' => ['icon' => 'ri-scales-3-line', 'route' => 'phi.dashboard', 'roles' => array_merge(['phi', 'superadmin'], $readOnlySpecificRoles), 'submenus' => [['name' => 'Jumlah PHK', 'route' => 'phi.jumlah-phk.index', 'icon' => 'ri-user-unfollow-fill', 'roles' => ['phi', 'superadmin']], ['name' => 'Perselisihan (TL)', 'route' => 'phi.perselisihan-ditindaklanjuti.index', 'icon' => 'ri-auction-line', 'roles' => ['phi', 'superadmin']], ['name' => 'Mediasi Berhasil', 'route' => 'phi.mediasi-berhasil.index', 'icon' => 'ri-shake-hands-line', 'roles' => ['phi', 'superadmin']], ['name' => 'Perusahaan Penerap SUSU', 'route' => 'phi.perusahaan-menerapkan-susu.index', 'icon' => 'ri-currency-line', 'roles' => ['phi', 'superadmin']]]],
                            'Barenbang' => ['icon' => 'ri-bar-chart-box-line', 'route' => 'barenbang.dashboard', 'roles' => array_merge(['barenbang', 'superadmin'], $readOnlySpecificRoles), 'submenus' => [['name' => 'Jml Kajian & Rekomendasi', 'route' => 'barenbang.jumlah-kajian-rekomendasi.index', 'icon' => 'ri-lightbulb-flash-line', 'roles' => ['barenbang', 'superadmin']], ['name' => 'Data Ketenagakerjaan', 'route' => 'barenbang.data-ketenagakerjaan.index', 'icon' => 'ri-database-2-line', 'roles' => ['barenbang', 'superadmin']], ['name' => 'Jml Aplikasi Terintegrasi SiapKerja', 'route' => 'barenbang.aplikasi-integrasi-siapkerja.index', 'icon' => 'ri-link-m', 'roles' => ['barenbang', 'superadmin']]]],
                        ];
                    @endphp

                    @if ($user)
                        {{-- Logika perulangan menu utama tidak diubah --}}
                        @foreach ($sidebarMenu as $menuName => $menuDetails)
                             @php
                                $canAccessMenu = $user->hasRole('superadmin');
                                if (!$canAccessMenu) {
                                    if (isset($menuDetails['permission'])) { $canAccessMenu = $user->can($menuDetails['permission']); } 
                                    elseif (isset($menuDetails['roles'])) { $canAccessMenu = $user->hasAnyRole($menuDetails['roles']); }
                                }
                             @endphp
                             @if ($canAccessMenu)
                                @php
                                    $parentSlug = Str::slug($menuName);
                                    $hasActiveChild = false;
                                    if (isset($menuDetails['route']) && Route::has($menuDetails['route']) && ($currentRouteName == $menuDetails['route'])) { $hasActiveChild = true; }
                                    if (!$hasActiveChild && !empty($menuDetails['submenus'])) {
                                        foreach ($menuDetails['submenus'] as $submenu) {
                                            if (isSubmenuActive($submenu, $currentRouteName)) { $hasActiveChild = true; break; }
                                        }
                                    }
                                @endphp
                                <div class="mb-0.5 sidebar-parent-item {{ $hasActiveChild ? 'expanded active-parent' : '' }}">
                                    <a href="{{ isset($menuDetails['route']) && Route::has($menuDetails['route']) ? route($menuDetails['route']) : '#' }}"
                                       onclick="{{ !empty($menuDetails['submenus']) && (!isset($menuDetails['route']) || $menuDetails['route'] === '#') ? "event.preventDefault(); toggleSubmenu('".$parentSlug."');" : "" }}"
                                       class="sidebar-parent-button text-sidebar-text flex items-center justify-between w-full px-4 py-3 focus:outline-none">
                                        <div class="flex items-center">
                                            <div class="w-5 h-5 flex items-center justify-center mr-3 main-menu-icon text-lg"><i class="{{ $menuDetails['icon'] }}"></i></div>
                                            <span class="text-sm">{{ $menuName }}</span>
                                        </div>
                                        @if (!empty($menuDetails['submenus']))<i id="arrow-{{ $parentSlug }}" class="arrow-icon ri-arrow-right-s-line text-xl text-sidebar-text transition-transform duration-300 {{ $hasActiveChild ? 'transform rotate-90' : '' }}"></i>@endif
                                    </a>
                                    @if (!empty($menuDetails['submenus']))
                                        <div class="submenu-list {{ $hasActiveChild ? 'expanded' : '' }}" id="submenu-{{ $parentSlug }}">
                                            <div class="pt-1 pb-1">
                                                @foreach ($menuDetails['submenus'] as $submenu)
                                                    @if($user->hasRole('superadmin') || empty($submenu['roles']) || $user->hasAnyRole($submenu['roles']))
                                                        <a href="{{ $submenu['route'] === '#' ? '#' : (Route::has($submenu['route']) ? route($submenu['route']) : '#!') }}"
                                                           class="sidebar-submenu-item text-sidebar-text flex items-center w-full py-2.5 pr-4 {{ (isset($submenu['is_sub_item']) && $submenu['is_sub_item']) ? 'pl-10' : 'pl-6' }} text-xs hover:text-white {{ isSubmenuActive($submenu, $currentRouteName) ? 'active' : '' }}">
                                                            @if(isset($submenu['icon']))<div class="w-5 h-5 flex items-center justify-center mr-2 opacity-75 text-base"><i class="{{ $submenu['icon'] }}"></i></div>@else<div class="w-5 h-5 mr-2"></div>@endif
                                                            <span>{{ $submenu['name'] }}</span>
                                                        </a>
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
            
            {{-- ======================================================= --}}
            {{-- ===== AWAL PERUBAHAN 2: USER MENU & DROPDOWN BARU ===== --}}
            {{-- ======================================================= --}}
            <div class="p-4 border-t border-sidebar-border-color mt-auto">
                 @if (Auth::check())
                 <div x-data="{ open: false }" class="relative">
                    {{-- Tombol untuk membuka/menutup dropdown --}}
                    <button @click="open = !open" class="flex items-center w-full text-left p-2 rounded-md hover:bg-gray-700">
                        <div class="w-9 h-9 rounded-full bg-sidebar-text text-sidebar-bg flex items-center justify-center text-xl mr-3 flex-shrink-0"><i class="ri-user-fill"></i></div>
                        <div class="flex-grow overflow-hidden">
                            <div class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-sidebar-text truncate">{{ Auth::user()->getRoleNames()->map(fn($role) => Str::ucfirst($role))->implode(', ') }}</div>
                        </div>
                        <div class="ml-auto">
                            <i class="ri-arrow-up-s-line text-sidebar-text text-xl transition-transform" :class="{'transform rotate-180': open}"></i>
                        </div>
                    </button>
            
                    {{-- Konten Dropdown yang Melayang --}}
                    <div x-show="open" 
                         @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute bottom-full left-0 mb-2 w-full bg-gray-800 rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5"
                         style="display: none;">
                        
                        {{-- Menu User Management (Hanya untuk yang punya izin) --}}
                        @can('manage users')
                            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white w-full text-left">
                                <i class="ri-user-settings-line w-5 mr-2"></i>
                                <span>User Management</span>
                            </a>
                        @endcan
            
                        {{-- Menu Logout --}}
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white w-full text-left">
                           <i class="ri-logout-box-r-line w-5 mr-2"></i>
                           <span>Logout</span>
                        </a>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Sisa dari file tidak berubah --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm z-10 sticky top-0"><div class="flex items-center justify-between h-16 px-4 sm:px-6"><div class="flex items-center"><button id="sidebarToggle" class="lg:hidden text-gray-500 hover:text-primary focus:outline-none mr-3"><div class="w-6 h-6 flex items-center justify-center"><i class="ri-menu-line text-xl"></i></div></button><div class="text-lg font-semibold text-gray-800 lg:ml-0">@yield('page_title', 'Dashboard')</div></div></div>@hasSection('header_filters')<div class="px-4 sm:px-6 py-3 border-t border-gray-100 flex flex-col md:flex-row items-stretch md:items-center md:justify-between gap-3 md:gap-4">@yield('header_filters')</div>@endif</header>
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50">@if (session('success'))<div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-md text-sm">{{ session('success') }}</div>@endif @if (session('error'))<div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">{{ session('error') }}</div>@endif @if ($errors->any())<div class="mb-4 p-3 bg-red-100 border-red-300 text-red-700 rounded-md text-sm"><strong class="font-bold">Oops! Terjadi kesalahan:</strong><ul class="mt-1 list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif @yield('content')</main>
        </div>
        <div id="mainContentOverlay" class="fixed inset-0 bg-black bg-opacity-25 z-20 hidden lg:hidden"></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const closeSidebarButton = document.getElementById('closeSidebar');
            const mainContentOverlay = document.getElementById('mainContentOverlay');
            function openSidebar() { if (sidebar) sidebar.classList.remove('-translate-x-full'); if (mainContentOverlay) mainContentOverlay.classList.remove('hidden'); document.body.classList.add('overflow-hidden', 'lg:overflow-auto'); }
            function closeSidebar() { if (sidebar) sidebar.classList.add('-translate-x-full'); if (mainContentOverlay) mainContentOverlay.classList.add('hidden'); document.body.classList.remove('overflow-hidden', 'lg:overflow-auto'); }
            if (sidebarToggle) { sidebarToggle.addEventListener('click', (e) => { e.stopPropagation(); openSidebar(); }); }
            if (closeSidebarButton) { closeSidebarButton.addEventListener('click', (e) => { e.stopPropagation(); closeSidebar(); }); }
            if (mainContentOverlay) { mainContentOverlay.addEventListener('click', () => { closeSidebar(); }); }
            window.toggleSubmenu = function(submenuIdBase) { const submenu = document.getElementById('submenu-' + submenuIdBase); const arrow = document.getElementById('arrow-' + submenuIdBase); const parentItem = arrow ? arrow.closest('.sidebar-parent-item') : null; if (submenu) { submenu.classList.toggle('expanded'); if (arrow) { arrow.classList.toggle('rotate-90'); } if(parentItem){ if (submenu.classList.contains('expanded')) { parentItem.classList.add('expanded'); } else { if (!parentItem.classList.contains('active-parent')) { parentItem.classList.remove('expanded'); } } } } }
        });
    </script>
    @stack('scripts')
</body>
</html>