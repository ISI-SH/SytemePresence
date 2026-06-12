@extends('layouts.employee')
@section('title', 'Mes congés')
@section('page-title', 'Mes demandes de congé')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Formulaire -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 h-fit">
        <h3 class="font-semibold text-gray-800 mb-5">Nouvelle demande</h3>
        <form method="POST" action="{{ route('employee.leaves.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Date de début</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" min="{{ now()->format('Y-m-d') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('start_date') border-red-400 @enderror">
                @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Date de fin</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" min="{{ now()->format('Y-m-d') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('end_date') border-red-400 @enderror">
                @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Motif</label>
                <textarea name="reason" rows="4" placeholder="Décrivez la raison…"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-400 @error('reason') border-red-400 @enderror">{{ old('reason') }}</textarea>
                @error('reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl transition-colors text-sm">
                Soumettre la demande
            </button>
        </form>
    </div>

    <!-- Liste -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Mes demandes</h3>
            </div>
            @if($leaves->isEmpty())
                <div class="px-6 py-16 text-center text-gray-400 text-sm">Aucune demande de congé.</div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($leaves as $leave)
                    <div class="px-6 py-4 flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800">
                                Du {{ $leave->start_date->locale('fr')->isoFormat('D MMM YYYY') }}
                                au {{ $leave->end_date->locale('fr')->isoFormat('D MMM YYYY') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $leave->reason }}</p>
                            <p class="text-xs text-gray-300 mt-1">Soumis le {{ $leave->created_at->locale('fr')->isoFormat('D MMM YYYY') }}</p>
                        </div>
                        <span class="{{ $leave->statusBadgeClass() }} flex-shrink-0">{{ $leave->statusLabel() }}</span>
                    </div>
                    @endforeach
                </div>
                @if($leaves->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">{{ $leaves->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection