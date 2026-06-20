@extends('layouts.employee')
@section('title', 'Mon tableau de bord')
@section('page-title', 'Mon tableau de bord')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- Carte pointage -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start justify-between mb-5">
            <div>
                <p class="text-sm text-gray-500">Bonjour,</p>
                <h2 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h2>
            </div>
            @if($todayAttendance)
                <span class="{{ $todayAttendance->statusBadgeClass() }} text-sm px-3 py-1">{{ $todayAttendance->statusLabel() }}</span>
            @else
                <span class="badge-default text-sm px-3 py-1">Pas encore pointé</span>
            @endif
        </div>

        @if($schedule)
        <div class="flex items-center gap-2 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 mb-5 text-sm text-blue-700">
            <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Horaire : <strong>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H\hi') }}</strong>
            → <strong>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H\hi') }}</strong>
            &nbsp;·&nbsp; Tolérance : <strong>{{ $schedule->tolerance_minutes }} min</strong>
        </div>
        @endif

        @if(!$todayAttendance || !$todayAttendance->check_in)
            @if(!$hasToken)
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-center">
                    <p class="text-yellow-700 font-semibold text-sm">⚠️ Aucun QR code actif aujourd'hui</p>
                    <p class="text-yellow-600 text-xs mt-1">Contactez l'administrateur.</p>
                </div>
            @else
                <form method="POST" action="{{ route('employee.checkin') }}" id="checkin-form">
                    @csrf
                    <input type="hidden" name="token" id="qr-token-input">
                    <div class="border-2 border-dashed border-blue-200 rounded-xl bg-blue-50 p-6 text-center mb-4">
                        <div id="scanner-placeholder">
                            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-blue-700">Pointez votre caméra vers le QR</p>
                            <p class="text-xs text-blue-500 mt-1">affiché à l'entrée de l'école</p>
                        </div>
                        <div id="qr-reader" class="hidden mx-auto" style="max-width:280px;"></div>
                    </div>
                    <button type="button" id="start-scan-btn"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6
                                   rounded-xl transition-colors flex items-center justify-center gap-2 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Scanner le QR code
                    </button>
                </form>
            @endif
        @else
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <p class="text-xs text-green-600 font-semibold mb-1">✅ Arrivée</p>
                    <p class="text-3xl font-bold text-green-700 font-mono">{{ $todayAttendance->check_in->format('H:i') }}</p>
                    <p class="text-xs text-green-500 mt-1">Via {{ $todayAttendance->method === 'qr' ? 'QR code' : 'Admin' }}</p>
                </div>
                @if(!$todayAttendance->check_out)
                    @if(!$hasToken)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-center">
                            <p class="text-yellow-700 font-semibold text-sm">⚠️ Aucun QR code actif</p>
                            <p class="text-yellow-600 text-xs mt-1">Contactez l'administrateur.</p>
                        </div>
                    @else
                        <form method="POST" action="{{ route('employee.checkout') }}" id="checkout-form">
                            @csrf
                            <input type="hidden" name="token" id="checkout-token-input">
                            <div class="border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 p-4 text-center mb-3">
                                <div id="checkout-scanner-placeholder">
                                    <p class="text-sm font-semibold text-slate-700">Scanner le QR pour le départ</p>
                                    <p class="text-xs text-slate-500 mt-1">affiché à l'entrée</p>
                                </div>
                                <div id="checkout-qr-reader" class="hidden mx-auto" style="max-width:240px;"></div>
                            </div>
                            <button type="button" id="start-checkout-scan-btn"
                                    class="w-full h-full bg-slate-800 hover:bg-slate-700 text-white font-semibold
                                           rounded-xl transition-colors flex flex-col items-center justify-center
                                           gap-2 min-h-[80px] text-sm p-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Scanner pour check-out
                            </button>
                        </form>
                    @endif
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-xs text-gray-500 font-semibold mb-1">🏠 Départ</p>
                        <p class="text-3xl font-bold text-gray-700 font-mono">{{ $todayAttendance->check_out->format('H:i') }}</p>
                        <p class="text-xs text-gray-400 mt-1">Durée : <strong>{{ $todayAttendance->hoursWorkedFormatted() }}</strong></p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Stats du mois -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}
        </h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-green-500"></div><span class="text-sm text-gray-600">Présences</span></div>
                <span class="text-xl font-bold text-gray-900">{{ $monthStats->present ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-yellow-400"></div><span class="text-sm text-gray-600">Retards</span></div>
                <span class="text-xl font-bold text-gray-900">{{ $monthStats->late ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-orange-400"></div><span class="text-sm text-gray-600">Départs anticipés</span></div>
                <span class="text-xl font-bold text-gray-900">{{ $monthStats->early_departure ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-red-500"></div><span class="text-sm text-gray-600">Absences</span></div>
                <span class="text-xl font-bold text-gray-900">{{ $monthStats->absent ?? 0 }}</span>
            </div>
            <div class="border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Moy. heures / jour</span>
                    <span class="text-2xl font-bold text-blue-600 font-mono">{{ $monthStats->avg_hours ?? '—' }}h</span>
                </div>
            </div>
        </div>
        @if($pendingLeaves > 0)
            <div class="mt-5 bg-yellow-50 border border-yellow-200 rounded-xl px-3 py-2.5 text-xs text-yellow-700 text-center">
                {{ $pendingLeaves }} demande(s) de congé en attente
            </div>
        @endif
    </div>
</div>

<!-- Historique récent -->
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Historique récent</h3>
        <a href="{{ route('employee.history') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Voir tout →</a>
    </div>
    @if($history->isEmpty())
        <div class="px-6 py-12 text-center text-gray-400 text-sm">Aucun historique de présence.</div>
    @else
        <div class="divide-y divide-gray-50">
            @foreach($history->take(8) as $a)
            <div class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50 transition-colors">
                <div class="w-28 flex-shrink-0">
                    <p class="text-sm font-semibold text-gray-700">{{ $a->date->locale('fr')->isoFormat('ddd D MMM') }}</p>
                </div>
                <div class="flex-1 flex items-center gap-2 font-mono text-sm text-gray-500">
                    <span>{{ $a->check_in  ? $a->check_in->format('H:i')  : '—' }}</span>
                    <span class="text-gray-300">→</span>
                    <span>{{ $a->check_out ? $a->check_out->format('H:i') : '—' }}</span>
                    <span class="text-xs text-gray-400 ml-2">{{ $a->hoursWorkedFormatted() }}</span>
                </div>
                <span class="{{ $a->statusBadgeClass() }}">{{ $a->statusLabel() }}</span>
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function initQrScanner(options) {
    const btn = document.getElementById(options.btnId);
    if (!btn) return;

    let scanner = null;

    btn.addEventListener('click', function () {
        const placeholder = document.getElementById(options.placeholderId);
        const readerEl = document.getElementById(options.readerId);
        const form = document.getElementById(options.formId);
        const input = document.getElementById(options.inputId);

        placeholder?.classList.add('hidden');
        readerEl?.classList.remove('hidden');
        btn.disabled = true;
        btn.textContent = 'Scan en cours…';

        scanner = new Html5Qrcode(options.readerId);
        scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 240, height: 240 } },
            function (decodedText) {
                input.value = decodedText;
                scanner.stop().then(() => form.submit());
            },
            function () {}
        ).catch(err => {
            alert('Impossible d\'accéder à la caméra : ' + err);
            placeholder?.classList.remove('hidden');
            readerEl?.classList.add('hidden');
            btn.disabled = false;
            btn.textContent = options.btnLabel;
        });
    });
}

initQrScanner({
    formId: 'checkin-form',
    inputId: 'qr-token-input',
    readerId: 'qr-reader',
    placeholderId: 'scanner-placeholder',
    btnId: 'start-scan-btn',
    btnLabel: 'Scanner le QR code'
});

initQrScanner({
    formId: 'checkout-form',
    inputId: 'checkout-token-input',
    readerId: 'checkout-qr-reader',
    placeholderId: 'checkout-scanner-placeholder',
    btnId: 'start-checkout-scan-btn',
    btnLabel: 'Scanner pour check-out'
});
</script>
@endpush