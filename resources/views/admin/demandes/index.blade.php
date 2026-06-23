@extends('layouts.admin')

@section('page-title', 'Gestion des Congés')

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Demandes de congé en attente</h2>

                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Tableau des demandes -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date début</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date fin</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Motif</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Date demande</th>
                                <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($demandes as $demande)
                                <tr>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $demande->user->name }}</div>
                                        <div class="text-xs text-gray-500 sm:hidden">{{ $demande->user->email }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $demande->start_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $demande->end_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-900 hidden sm:table-cell">
                                        {{ $demande->reason }}
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                        {{ $demande->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex flex-col sm:flex-row gap-2 justify-end">
                                            <form action="{{ route('admin.demandes.approve', $demande) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir accepter cette demande de congé ?');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded-md hover:bg-green-700">
                                                    Accepter
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.demandes.reject', $demande) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir refuser cette demande de congé ?');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700">
                                                    Refuser
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 sm:px-6 py-4 text-center text-gray-500">
                                        Aucune demande de congé en attente
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($demandes->hasPages())
                    <div class="mt-6">
                        {{ $demandes->links() }}
                    </div>
                @endif
            </div>
        </div>
@endsection
