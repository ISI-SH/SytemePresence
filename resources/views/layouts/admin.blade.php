<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PrésenceApp - Admin')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter','sans-serif'] } } } }</script>
    <style>
        .badge-present { @apply inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800; }
        .badge-late    { @apply inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800; }
        .badge-early   { @apply inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800; }
        .badge-absent  { @apply inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800; }
        .badge-default { @apply inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600; }
        .badge-pending { @apply inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex h-screen overflow-hidden">

    <!-- MOBILE MENU BUTTON -->
    <button id="mobile-menu-btn" class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-slate-900 text-white rounded-lg shadow-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <!-- MOBILE OVERLAY -->
    <div id="mobile-overlay" class="lg:hidden fixed inset-0 bg-black/50 z-40 hidden"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-slate-900 flex flex-col flex-shrink-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
            <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-white font-bold text-base">PrésenceApp</span>
        </div>
        <!-- User -->
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-700">
            <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 truncate">Administrateur</p>
            </div>
        </div>
        <!-- Nav -->
        <nav class="flex-1 px-3 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
                Tableau de bord
            </a>
            <a href="{{ route('admin.employes.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.employes.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Employés
            </a>
            <a href="{{ route('admin.pointages.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.pointages.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Pointages
            </a>
            <a href="{{ route('admin.demandes.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.demandes.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Congés
            </a>
            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Paramètres
            </a>
        </nav>
        <!-- Logout -->
        <div class="px-3 pb-4 pt-4 border-t border-slate-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                               text-slate-400 hover:bg-red-900/30 hover:text-red-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Se déconnecter
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between flex-shrink-0">
            <div class="ml-12 lg:ml-0">
                <h1 class="text-lg sm:text-xl font-bold text-gray-900">@yield('page-title', 'Administration')</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5 capitalize">
                    {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                </p>
            </div>
            <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full font-medium hidden sm:inline-block">Espace Admin</span>
        </header>

        <!-- Flash messages -->
        <div class="px-4 sm:px-6 lg:px-8 pt-4 flex-shrink-0">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-xl mb-2">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl mb-2">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <!-- Content -->
        <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-overlay');

    mobileMenuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
</script>
@stack('scripts')
</body>
</html>
