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
                        <a href="{{ route('admin.dashboard') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
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
                        <a href="{{ route('admin.settings.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
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
        <!-- Statistiques du jour -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Présents -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Présents aujourd'hui</dt>
                                <dd class="text-3xl font-semibold text-gray-900">{{ $presentCount }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Retards -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Retards aujourd'hui</dt>
                                <dd class="text-3xl font-semibold text-gray-900">{{ $lateCount }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Absents -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Absents aujourd'hui</dt>
                                <dd class="text-3xl font-semibold text-gray-900">{{ $absentCount }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taux de présence et QR Code -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Taux de présence hebdomadaire -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Taux de présence hebdomadaire</h3>
                    <div class="flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-5xl font-bold text-indigo-600">{{ $weeklyAttendanceRate }}%</div>
                            <p class="text-sm text-gray-500 mt-2">Semaine en cours</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code actif -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">QR Code actif du jour</h3>
                    @if($qrCode)
                        <div class="flex items-center justify-center">
                            <div class="text-center">
                                <div class="bg-white p-4 border-2 border-gray-300 rounded-lg inline-block">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $qrCode->token }}" alt="QR Code" class="w-48 h-48">
                                </div>
                                <p class="text-sm text-gray-600 mt-4">Token: {{ $qrCode->token }}</p>
                                <p class="text-sm text-gray-500">Expire: {{ $qrCode->expires_at->format('H:i') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-gray-500">
                            <p>Aucun QR code actif pour aujourd'hui</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Graphique des tendances -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-8">
            <div class="p-5">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Tendances de présence (7 derniers jours)</h3>
                <div class="h-64">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json(collect($last7Days)->pluck('date')),
            datasets: [
                {
                    label: 'Présents',
                    data: @json(collect($last7Days)->pluck('present')),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.1
                },
                {
                    label: 'Retards',
                    data: @json(collect($last7Days)->pluck('late')),
                    borderColor: 'rgb(234, 179, 8)',
                    backgroundColor: 'rgba(234, 179, 8, 0.1)',
                    tension: 0.1
                },
                {
                    label: 'Absents',
                    data: @json(collect($last7Days)->pluck('absent')),
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
