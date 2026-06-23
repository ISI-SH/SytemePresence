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

    <!-- Barre latérale de navigation -->
    <aside>

        <!-- Logo de l'application -->
        <div>

            <!-- Icône du logo -->

            <!-- Nom de l'application -->
            PrésenceApp
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

        <!-- En-tête -->
        <header>

            <!-- Titre dynamique de la page -->
            @yield('page-title')

            <!-- Date actuelle -->
            {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}

            <!-- Indication du rôle connecté -->
            Espace Employé

        </header>

        <!-- Message de succès -->
        @if(session('success'))

            <!-- Affiché après une action réussie -->
            {{ session('success') }}

        @endif

        <!-- Message d'erreur -->
        @if(session('error'))

            <!-- Affiché lorsqu'une opération échoue -->
            {{ session('error') }}

        @endif

        <!-- Zone où les vues enfant sont injectées -->
        <main>

            @yield('content')

        </main>

    </div>

</div>

<!-- Scripts spécifiques à chaque page -->
@stack('scripts')