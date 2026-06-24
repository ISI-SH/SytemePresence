@extends('layouts.employee')

{{-- Titre de la page dans l'onglet du navigateur --}}
@section('title', 'Mes congés')

{{-- Titre affiché dans le dashboard --}}
@section('page-title', 'Mes demandes de congé')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- =========================
         Formulaire de demande
    ========================== -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 h-fit">

        {{-- Titre du formulaire --}}
        <h3 class="font-semibold text-gray-800 mb-5">Nouvelle demande</h3>

        {{-- Formulaire envoyé au contrôleur pour enregistrer une demande de congé --}}
        <form method="POST" action="{{ route('employee.leaves.store') }}" class="space-y-4">

            {{-- Protection CSRF de Laravel --}}
            @csrf

            {{-- Date de début du congé --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Date de début
                </label>

                {{-- L'employé ne peut pas choisir une date passée --}}
                <input type="date"
                       name="start_date"
                       value="{{ old('start_date') }}"
                       min="{{ now()->format('Y-m-d') }}">

                {{-- Affichage des erreurs de validation --}}
                @error('start_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Date de fin du congé --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Date de fin
                </label>

                <input type="date"
                       name="end_date"
                       value="{{ old('end_date') }}"
                       min="{{ now()->format('Y-m-d') }}">

                {{-- Affichage des erreurs de validation --}}
                @error('end_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Motif de la demande --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Motif
                </label>

                {{-- Zone de texte où l'employé explique la raison du congé --}}
                <textarea name="reason" rows="4"
                          placeholder="Décrivez la raison…">{{ old('reason') }}</textarea>

                {{-- Affichage des erreurs de validation --}}
                @error('reason')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bouton d'envoi de la demande --}}
            <button type="submit">
                Soumettre la demande
            </button>

        </form>
    </div>

    <!-- =========================
         Liste des congés
    ========================== -->
    <div class="lg:col-span-2">

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">

            {{-- Titre de la liste --}}
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Mes demandes</h3>
            </div>

            {{-- Si aucune demande n'existe --}}
            @if($leaves->isEmpty())

                <div class="px-6 py-16 text-center text-gray-400 text-sm">
                    Aucune demande de congé.
                </div>

            @else

                {{-- Parcours de toutes les demandes de congé --}}
                <div class="divide-y divide-gray-50">

                    @foreach($leaves as $leave)

                    <div class="px-6 py-4 flex items-start justify-between gap-4">

                        <div class="min-w-0">

                            {{-- Affichage de la période du congé --}}
                            <p class="text-sm font-semibold text-gray-800">
                                Du {{ $leave->start_date->locale('fr')->isoFormat('D MMM YYYY') }}
                                au {{ $leave->end_date->locale('fr')->isoFormat('D MMM YYYY') }}
                            </p>

                            {{-- Motif saisi par l'employé --}}
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                {{ $leave->reason }}
                            </p>

                            {{-- Date de création de la demande --}}
                            <p class="text-xs text-gray-300 mt-1">
                                Soumis le {{ $leave->created_at->locale('fr')->isoFormat('D MMM YYYY') }}
                            </p>

                        </div>

                        {{-- Statut de la demande :
                             En attente, Acceptée ou Refusée --}}
                        <span class="{{ $leave->statusBadgeClass() }}">
                            {{ $leave->statusLabel() }}
                        </span>

                    </div>

                    @endforeach

                </div>

                {{-- Pagination si le nombre de demandes est important --}}
                @if($leaves->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $leaves->links() }}
                    </div>
                @endif

            @endif

        </div>

    </div>

</div>

@endsection