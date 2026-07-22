<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true', mobileMenuOpen: false, get isCollapsed() { return this.sidebarCollapsed && window.innerWidth >= 1024 } }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Sistem Kehadiran Karyawan</title>
    <meta name="description" content="Sistem Manajemen Keamanan - Monitoring Kehadiran Karyawan Overhaul">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#eef7ff', 100: '#d9edff', 200: '#bce0ff', 300: '#8ecdff',
                            400: '#59b0ff', 500: '#338dfc', 600: '#1d6ef1', 700: '#1558de',
                            800: '#1847b4', 900: '#193f8e', 950: '#142856',
                        },
                    },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        * { transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease; }
        @keyframes pulse-live { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        .animate-pulse-live { animation: pulse-live 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .dark .glass { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.06); }
        .gradient-text { background: linear-gradient(135deg, #338dfc 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .log-row { transition: all 0.15s ease; }
        .log-row:hover { background: rgba(51, 141, 252, 0.05); }
        .dark .log-row:hover { background: rgba(51, 141, 252, 0.1); }
        @keyframes flashNew { 0% { background: rgba(52, 211, 153, 0.2); } 100% { background: transparent; } }
        .flash-new { animation: flashNew 2s ease-out; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Backdrop Overlay for Mobile -->
        <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" x-transition.opacity class="fixed inset-0 bg-black/50 z-20 lg:hidden"></div>

        <!-- Sidebar -->
        <aside class="bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 fixed h-full z-30 transition-all duration-300 ease-in-out lg:flex lg:flex-col"
               :class="[
                   isCollapsed ? 'lg:w-20' : 'lg:w-72',
                   mobileMenuOpen ? 'w-72 translate-x-0' : 'w-72 -translate-x-full lg:translate-x-0'
               ]">
            <div class="flex items-center border-b border-slate-200 dark:border-slate-800 transition-all duration-300 py-5"
                 :class="isCollapsed ? 'flex-col justify-center px-4 gap-4' : 'px-6 justify-between gap-3'">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-brand-500/25 shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div x-show="!isCollapsed" x-transition.opacity.duration.200ms class="leading-tight select-none">
                        <h1 class="text-sm font-bold gradient-text">Sistem Kehadiran</h1>
                        <h1 class="text-sm font-bold gradient-text">Karyawan Overhaul</h1>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">Portal Manajemen Keamanan</p>
                    </div>
                </div>
                <button @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)"
                        class="hidden lg:block p-1.5 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0">
                    <svg class="w-5 h-5 transition-transform duration-300" :class="isCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
            <nav class="flex-1 py-6 space-y-1 overflow-y-auto" :class="isCollapsed ? 'px-2' : 'px-4'">
                <p x-show="!isCollapsed" x-transition.opacity.duration.200ms class="px-3 mb-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider whitespace-nowrap">Monitoring</p>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                   :class="isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    <span x-show="!isCollapsed" x-transition.opacity.duration.200ms class="whitespace-nowrap">Dashboard Karyawan Overhaul</span>
                </a>
                <p x-show="!isCollapsed" x-transition.opacity.duration.200ms class="px-3 mt-6 mb-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider whitespace-nowrap">Manajemen</p>
                <a href="{{ route('admin.staffs.index') }}" class="flex items-center rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.staffs.*') ? 'bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                   :class="isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span x-show="!isCollapsed" x-transition.opacity.duration.200ms class="whitespace-nowrap">Data Pegawai</span>
                </a>
                <a href="{{ route('admin.profile.show') }}" class="flex items-center rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.profile.show') ? 'bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                   :class="isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span x-show="!isCollapsed" x-transition.opacity.duration.200ms class="whitespace-nowrap">Profil Admin</span>
                </a>
                <a href="{{ route('admin.geofences.index') }}" class="flex items-center rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.geofences.*') ? 'bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                   :class="isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="!isCollapsed" x-transition.opacity.duration.200ms class="whitespace-nowrap">Zona Geofence</span>
                </a>
                <a href="{{ route('admin.schedules.index') }}" class="flex items-center rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.schedules.*') ? 'bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                   :class="isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span x-show="!isCollapsed" x-transition.opacity.duration.200ms class="whitespace-nowrap">Jadwal Shift</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                   :class="isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2.5'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="!isCollapsed" x-transition.opacity.duration.200ms class="whitespace-nowrap">Laporan</span>
                </a>
            </nav>
            <div class="py-4 border-t border-slate-200 dark:border-slate-800 transition-all duration-300"
                 :class="isCollapsed ? 'px-2' : 'px-4'">
                <div class="flex items-center gap-3"
                     :class="isCollapsed ? 'flex-col justify-center' : 'px-3 justify-between'">
                    <a href="{{ route('admin.profile.show') }}" class="flex items-center rounded-xl transition-all"
                       :class="isCollapsed ? 'justify-center' : 'gap-3 flex-1 min-w-0 hover:bg-slate-100 dark:hover:bg-slate-800 p-1.5'" title="Buka Profil Admin">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-400 to-indigo-500 flex items-center justify-center text-white text-sm font-bold shrink-0">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0" x-show="!isCollapsed" x-transition.opacity.duration.200ms>
                            <p class="text-sm font-semibold truncate text-slate-800 dark:text-slate-200 whitespace-nowrap">{{ auth()->user()->name ?? 'Admin' }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate whitespace-nowrap">{{ auth()->user()->role?->label() ?? 'Administrator' }}</p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" :class="isCollapsed ? 'w-full flex justify-center' : ''">
                        @csrf
                        <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/50 transition-colors" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        <div class="flex-1 min-w-0 transition-all duration-300 ease-in-out"
             :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-72'">
            <header class="sticky top-0 z-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-4">
                        <!-- Mobile Hamburger Button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-1.5 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors lg:hidden" title="Buka Menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h2 class="text-lg font-semibold">@yield('header', 'Dashboard')</h2>
                        <div class="hidden sm:flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse-live"></span>
                            <span class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Live Monitoring</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </button>
                        <div class="hidden md:flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span id="current-time">--:--:--</span>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-6">@yield('content')</main>
        </div>
    </div>
    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
    @stack('scripts')
</body>
</html>
