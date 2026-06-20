@extends('layouts.employee')
@section('title', 'Mon historique')
@section('page-title', 'Mon historique de présence')

@section('content')

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('employee.history') }}" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Mois</label>
            <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Statut</label>
            <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Tous</option>
                <option value="present"         {{ request('status')==='present'         ? 'selected':'' }}>Présent</option>
                <option value="late"            {{ request('status')==='late'            ? 'selected':'' }}>Retard</option>
                <option value="absent"          {{ request('status')==='absent'          ? 'selected':'' }}>Absent</option>
                <option value="early_departure" {{ request('status')==='early_departure' ? 'selected':'' }}>Départ anticipé</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
            Filtrer
        </button>
        @if(request()->hasAny(['month','status']))
            <a href="{{ route('employee.history') }}" class="text-sm text-gray-400 hover:text-gray-600 py-2">Réinitialiser</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <p class="text-sm text-gray-500"><span class="font-semibold text-gray-900">{{ $attendances->total() }}</span> enregistrement(s)</p>
    </div>
    @if($attendances->isEmpty())
        <div class="px-6 py-16 text-center text-gray-400 text-sm">Aucune présence trouvée.</div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left font-semibold">Date</th>
                        <th class="px-6 py-3 text-left font-semibold">Arrivée</th>
                        <th class="px-6 py-3 text-left font-semibold">Départ</th>
                        <th class="px-6 py-3 text-left font-semibold">Durée</th>
                        <th class="px-6 py-3 text-left font-semibold">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($attendances as $a)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-gray-800">{{ $a->date->locale('fr')->isoFormat('ddd D MMM YYYY') }}</td>
                        <td class="px-6 py-3 font-mono text-gray-600">{{ $a->check_in  ? $a->check_in->format('H:i')  : '—' }}</td>
                        <td class="px-6 py-3 font-mono text-gray-600">{{ $a->check_out ? $a->check_out->format('H:i') : '—' }}</td>
                        <td class="px-6 py-3 font-mono text-gray-500">{{ $a->hoursWorkedFormatted() }}</td>
                        <td class="px-6 py-3"><span class="{{ $a->statusBadgeClass() }}">{{ $a->statusLabel() }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $attendances->links() }}</div>
        @endif
    @endif
</div>
@endsection