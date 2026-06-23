@extends('layouts.admin')

@section('page-title', 'Tableau de bord')

@section('content')
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
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">QR Code de pointage</h3>
                        <span id="qr-countdown" class="text-xs font-mono text-indigo-600 bg-indigo-50 px-2 py-1 rounded"></span>
                    </div>
                    @if($qrCode)
                        <div class="flex items-center justify-center">
                            <div class="text-center">
                                <div class="bg-white p-4 border-2 border-gray-300 rounded-lg inline-block">
                                    <img id="admin-qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $qrCode->token }}" alt="QR Code" class="w-48 h-48">
                                </div>
                                <p class="text-xs text-gray-500 mt-3">Renouvelé chaque minute · valable pour arrivée et départ</p>
                                <p id="qr-expires-label" class="text-sm text-gray-500 mt-1">Expire à {{ $qrCode->expires_at->format('H:i:s') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-gray-500">
                            <p>Aucun QR code actif</p>
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
<script>
(function () {
    const qrImage = document.getElementById('admin-qr-image');
    const countdown = document.getElementById('qr-countdown');
    const expiresLabel = document.getElementById('qr-expires-label');
    if (!qrImage) return;

    let secondsLeft = {{ $qrCode?->secondsUntilExpiry() ?? 0 }};

    function updateCountdown() {
        if (!countdown) return;
        countdown.textContent = secondsLeft > 0 ? 'Nouveau QR dans ' + secondsLeft + 's' : 'Mise à jour…';
        if (secondsLeft > 0) secondsLeft--;
    }

    async function refreshQr() {
        try {
            const res = await fetch('{{ route('admin.qr.current') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const data = await res.json();
            qrImage.src = data.qr_url;
            secondsLeft = data.seconds_left;
            if (expiresLabel && data.expires_at) {
                const d = new Date(data.expires_at);
                expiresLabel.textContent = 'Expire à ' + d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }
        } catch (e) {}
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
    setInterval(refreshQr, 1000);
})();
</script>
@endsection
