<!-- Layout principal de l'espace employé -->

<!-- Encodage UTF-8 -->
<meta charset="UTF-8">

<!-- Adaptation aux écrans mobiles -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Token de sécurité CSRF utilisé par Laravel -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Titre dynamique de la page -->
<title>@yield('title', 'PrésenceApp')</title>

<!-- Import de la police Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">

<!-- Import de Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Classes CSS utilisées pour afficher les différents statuts -->
<style>
    /* Présent */
    .badge-present

    /* Retard */
    .badge-late

    /* Départ anticipé */
    .badge-early

    /* Absent */
    .badge-absent

    /* Statut par défaut */
    .badge-default

    /* Demande en attente */
    .badge-pending
</style>

<!-- Conteneur principal -->
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
            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <span class="text-white font-bold text-base">PrésenceApp</span>
        </div>

        <!-- Informations de l'employé connecté -->
        <div>

            <!-- Initiales de l'employé -->
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}

            <!-- Nom complet -->
            {{ auth()->user()->name }}

            <!-- Département -->
            {{ auth()->user()->department?->name ?? 'Sans département' }}
        </div>

        <!-- Menu de navigation -->
        <nav>

            <!-- Lien vers le tableau de bord -->
            <a href="{{ route('employee.dashboard') }}">
                Tableau de bord
            </a>

            <!-- Lien vers l'historique -->
            <a href="{{ route('employee.history') }}">
                Mon historique
            </a>

            <!-- Lien vers les demandes de congé -->
            <a href="{{ route('employee.leaves.index') }}">
                Mes congés
            </a>

        </nav>

        <!-- Formulaire de déconnexion -->
        <form method="POST" action="{{ route('logout') }}">

            <!-- Protection CSRF -->
            @csrf

            <!-- Bouton de déconnexion -->
            Se déconnecter

        </form>

    </aside>

    <!-- Zone principale -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between flex-shrink-0">
            <div class="ml-12 lg:ml-0">
                <h1 class="text-lg sm:text-xl font-bold text-gray-900">@yield('page-title')</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5 capitalize">
                    {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                </p>
            </div>
            <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full font-medium hidden sm:inline-block">Espace Employé</span>
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
