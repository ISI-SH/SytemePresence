@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Dashboard Admin</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('admin.dashboard') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.employes.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Employés
                        </a>
                        <a href="{{ route('admin.pointages.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Pointages
                        </a>
                        <a href="{{ route('admin.demandes.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Congés
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Paramètres
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-700 mr-4">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-900">Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Paramètres de présence</h1>
                
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Heure d'arrivée</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="fixed_arrival_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Heure d'arrivée fixe
                                </label>
                                <input type="time" 
                                       id="fixed_arrival_time" 
                                       name="fixed_arrival_time" 
                                       value="{{ $settings['fixed_arrival_time'] }}"
                                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                <p class="mt-1 text-sm text-gray-500">
                                    Tout pointage après cette heure sera considéré comme un retard.
                                </p>
                            </div>
                            
                            <div>
                                <label for="late_tolerance_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tolérance de retard (minutes)
                                </label>
                                <input type="number" 
                                       id="late_tolerance_minutes" 
                                       name="late_tolerance_minutes" 
                                       value="{{ $settings['late_tolerance_minutes'] }}"
                                       min="0" 
                                       max="60"
                                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                <p class="mt-1 text-sm text-gray-500">
                                    Nombre de minutes de tolérance avant de considérer un employé en retard.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Horaires de travail</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="work_start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Heure de début de travail
                                </label>
                                <input type="time" 
                                       id="work_start_time" 
                                       name="work_start_time" 
                                       value="{{ $settings['work_start_time'] }}"
                                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                            </div>
                            
                            <div>
                                <label for="work_end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Heure de fin de travail
                                </label>
                                <input type="time" 
                                       id="work_end_time" 
                                       name="work_end_time" 
                                       value="{{ $settings['work_end_time'] }}"
                                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Durée de travail</h2>
                        
                        <div class="max-w-xs">
                            <label for="required_daily_hours" class="block text-sm font-medium text-gray-700 mb-2">
                                Durée de travail quotidienne requise (heures)
                            </label>
                            <input type="number" 
                                   id="required_daily_hours" 
                                   name="required_daily_hours" 
                                   value="{{ $settings['required_daily_hours'] }}"
                                   min="1" 
                                   max="12"
                                   class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   required>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Enregistrer les paramètres
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
