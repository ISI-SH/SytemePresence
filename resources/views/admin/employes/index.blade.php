@extends('layouts.admin')

@section('page-title', 'Gestion des Employés')

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">Liste des employés</h2>
                    <a href="{{ route('admin.employes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        + Nouvel employé
                    </a>
                </div>

                <!-- Recherche -->
                <form action="{{ route('admin.employes.index') }}" method="GET" class="mb-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou email..." class="flex-1 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Rechercher
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.employes.index') }}" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Tableau des employés -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Email</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Téléphone</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Date embauche</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Photo</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($employees as $employee)
                                <tr>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                                        <div class="text-xs text-gray-500 sm:hidden">{{ $employee->email }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <div class="text-sm text-gray-900">{{ $employee->email }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 hidden md:table-cell">
                                        {{ $employee->phone ?? '-' }}
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 hidden lg:table-cell">
                                        {{ $employee->hire_date ? (is_string($employee->hire_date) ? date('d/m/Y', strtotime($employee->hire_date)) : $employee->hire_date->format('d/m/Y')) : '-' }}
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        @if($employee->photo)
                                            <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->name }}" class="h-10 w-10 rounded-full object-cover">
                                        @else
                                            <span class="text-sm text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        @if($employee->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Actif
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-0 justify-end">
                                            <a href="{{ route('admin.employes.edit', $employee) }}" class="text-indigo-600 hover:text-indigo-900">
                                                Modifier
                                            </a>
                                            <form action="{{ route('admin.employes.toggle', $employee) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                                    {{ $employee->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.employes.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 sm:px-6 py-4 text-center text-gray-500">
                                        Aucun employé trouvé
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($employees->hasPages())
                    <div class="mt-6">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
        </div>
@endsection
