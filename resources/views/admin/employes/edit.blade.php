@extends('layouts.admin')

@section('page-title', 'Modifier l\'Employé')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Modifier l'employé : {{ $employee->name }}</h2>

                    <form action="{{ route('admin.employes.update', $employee) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $employee->name) }}" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="hire_date" class="block text-sm font-medium text-gray-700">Date d'embauche</label>
                                <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $employee->hire_date ? (is_string($employee->hire_date) ? date('Y-m-d', strtotime($employee->hire_date)) : $employee->hire_date->format('Y-m-d')) : '') }}"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('hire_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
                                <input type="file" name="photo" id="photo"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @if($employee->photo)
                                    <p class="mt-2 text-sm text-gray-500">Photo actuelle : <a href="{{ asset('storage/' . $employee->photo) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">Voir</a></p>
                                @endif
                                @error('photo')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe (laisser vide pour ne pas changer)</label>
                                <input type="password" name="password" id="password"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" {{ $employee->is_active ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Compte actif
                                </label>
                            </div>

                            <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4">
                                <a href="{{ route('admin.employes.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 text-center">
                                    Annuler
                                </a>
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    Mettre à jour
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection
