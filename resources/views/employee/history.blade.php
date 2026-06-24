@extends('layouts.employee')

{{-- Titre de la page dans l'onglet du navigateur --}}
@section('title', 'Mon historique')

{{-- Titre affiché en haut de la page --}}
@section('page-title', 'Mon historique de présence')

@section('content')

{{-- Bloc contenant les filtres de recherche --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-6">

    {{-- Formulaire envoyé en GET pour filtrer l'historique --}}
    <form method="GET" action="{{ route('employee.history') }}" class="flex flex-wrap items-end gap-4">

        {{-- Filtre par mois --}}
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                Mois
            </label>

            {{-- Permet de sélectionner un mois spécifique --}}
            <input type="month"
                   name="month"
                   value="{{ request('month', now()->format('Y-m')) }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- Filtre par statut --}}
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                Statut
            </label>

            <select name="status"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">

                {{-- Affiche tous les statuts --}}
                <option value="">Tous</option>

                {{-- Présent --}}
                <option value="present"
                    {{ request('status')==='present' ? 'selected':'' }}>
                    Présent
                </option>

                {{-- Retard --}}
                <option value="late"
                    {{ request('status')==='late' ? 'selected':'' }}>
                    Retard
                </option>

                {{-- Absent --}}
                <option value="absent"
                    {{ request('status')==='absent' ? 'selected':'' }}>
                    Absent
                </option>

                {{-- Départ anticipé --}}
                <option value="early_departure"
                    {{ request('status')==='early_departure' ? 'selected':'' }}>
                    Départ anticipé
                </option>

            </select>
        </div>

        {{-- Bouton qui lance le filtrage --}}
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
            Filtrer
        </button>

        {{-- Affiche le bouton Réinitialiser uniquement lorsqu'un filtre est appliqué --}}
        @if(request()->hasAny(['month','status']))
            <a href="{{ route('employee.history') }}"
               class="text-sm text-gray-400 hover:text-gray-600 py-2">
                Réinitialiser
            </a>
        @endif

    </form>
</div>

{{-- Tableau principal de l'historique --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm">

    {{-- Nombre total d'enregistrements trouvés --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <p class="text-sm text-gray-500">
            <span class="font-semibold text-gray-900">
                {{ $attendances->total() }}
            </span>
            enregistrement(s)
        </p>
    </div>

    {{-- Si aucun résultat n'est trouvé --}}
    @if($attendances->isEmpty())

        <div class="px-6 py-16 text-center text-gray-400 text-sm">
            Aucune présence trouvée.
        </div>

    @else

        {{-- Tableau des présences --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                {{-- Entête du tableau --}}
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">

                        {{-- Date du pointage --}}
                        <th class="px-6 py-3 text-left font-semibold">Date</th>

                        {{-- Heure d'arrivée --}}
                        <th class="px-6 py-3 text-left font-semibold">Arrivée</th>

                        {{-- Heure de départ --}}
                        <th class="px-6 py-3 text-left font-semibold">Départ</th>

                        {{-- Temps travaillé --}}
                        <th class="px-6 py-3 text-left font-semibold">Durée</th>

                        {{-- Statut de présence --}}
                        <th class="px-6 py-3 text-left font-semibold">Statut</th>

                    </tr>
                </thead>

                {{-- Corps du tableau --}}
                <tbody class="divide-y divide-gray-50">

                    {{-- Parcours de tous les enregistrements de présence --}}
                    @foreach($attendances as $a)

                    <tr class="hover:bg-gray-50 transition-colors">

                        {{-- Date du pointage --}}
                        <td class="px-6 py-3 font-semibold text-gray-800">
                            {{ $a->date->locale('fr')->isoFormat('ddd D MMM YYYY') }}
                        </td>

                        {{-- Heure d'arrivée --}}
                        <td class="px-6 py-3 font-mono text-gray-600">
                            {{ $a->check_in ? $a->check_in->format('H:i') : '—' }}
                        </td>

                        {{-- Heure de départ --}}
                        <td class="px-6 py-3 font-mono text-gray-600">
                            {{ $a->check_out ? $a->check_out->format('H:i') : '—' }}
                        </td>

                        {{-- Nombre d'heures travaillées --}}
                        <td class="px-6 py-3 font-mono text-gray-500">
                            {{ $a->hoursWorkedFormatted() }}
                        </td>

                        {{-- Affichage du statut avec sa couleur correspondante --}}
                        <td class="px-6 py-3">
                            <span class="{{ $a->statusBadgeClass() }}">
                                {{ $a->statusLabel() }}
                            </span>
                        </td>

                    </tr>

                    @endforeach

                </tbody>
            </table>
        </div>

        {{-- Pagination lorsqu'il y a beaucoup de résultats --}}
        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $attendances->links() }}
            </div>
        @endif

    @endif

</div>

@endsection