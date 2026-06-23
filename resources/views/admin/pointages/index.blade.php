@extends('layouts.admin')

@section('page-title', 'Historique des Pointages')

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Filtres</h2>

                <!-- Formulaire de filtres -->
                <form action="{{ route('admin.pointages.index') }}" method="GET" class="mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="date_start" class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                            <input type="date" name="date_start" id="date_start" value="{{ request('date_start') }}" 
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="date_end" class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                            <input type="date" name="date_end" id="date_end" value="{{ request('date_end') }}" 
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
                            <select name="employee_id" id="employee_id" 
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Tous les employés</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select name="status" id="status" 
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Tous les statuts</option>
                                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Présent</option>
                                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>En retard</option>
                                <option value="early_departure" {{ request('status') == 'early_departure' ? 'selected' : '' }}>Départ anticipé</option>
                                <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Filtrer
                        </button>
                        <a href="{{ route('admin.pointages.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                            Réinitialiser
                        </a>
                        @if(request()->hasAny(['date_start', 'date_end', 'employee_id', 'status']))
                            <a href="{{ route('admin.pointages.export', request()->all()) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                Exporter CSV
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Tableau des pointages -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrivée</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Départ</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Heures travaillées</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pointages as $pointage)
                                <tr>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $pointage->user->name }}</div>
                                        <div class="text-xs text-gray-500 sm:hidden">{{ $pointage->user->email }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $pointage->date ? $pointage->date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $pointage->check_in ? $pointage->check_in->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 hidden sm:table-cell">
                                        {{ $pointage->check_out ? $pointage->check_out->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        @if($pointage->status == 'present')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Présent
                                            </span>
                                        @elseif($pointage->status == 'late')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                En retard
                                            </span>
                                        @elseif($pointage->status == 'early_departure')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Départ anticipé
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Absent
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 hidden md:table-cell">
                                        {{ $pointage->hoursWorkedFormatted() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 sm:px-6 py-4 text-center text-gray-500">
                                        Aucun pointage trouvé
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($pointages->hasPages())
                    <div class="mt-6">
                        {{ $pointages->links() }}
                    </div>
                @endif
            </div>
        </div>
@endsection
