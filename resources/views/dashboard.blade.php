@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional hari ini')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    {{-- Transaksi Hari Ini --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-slate-500">Transaksi Hari Ini</p>
            <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16l3-2 2 2 2-2 2 2 2-2 3 2V4a2 2 0 0 0-2-2z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ $transaksiHariIni }}</p>
        <p class="text-xs text-slate-400 mt-1">transaksi aktif masuk</p>
    </div>

    {{-- Pendapatan Hari Ini --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-slate-500">Pendapatan Hari Ini</p>
            <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-800">
            Rp{{ number_format($pendapatanHariIni, 0, ',', '.') }}
        </p>
        <p class="text-xs text-slate-400 mt-1">pembayaran masuk</p>
    </div>

    {{-- Cucian Aktif --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-slate-500">Cucian Diproses</p>
            <div class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ $cucianBelumSelesai }}</p>
        <p class="text-xs text-slate-400 mt-1">diterima &amp; sedang diproses</p>
    </div>

    {{-- Belum Lunas --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-slate-500">Belum Lunas</p>
            <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ $belumLunas }}</p>
        <p class="text-xs text-slate-400 mt-1">transaksi aktif belum lunas</p>
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Transaksi Terbaru --}}
    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-700 text-sm">Transaksi Terbaru</h3>
            <span class="text-xs text-slate-400">10 terakhir</span>
        </div>

        @if($transaksiTerbaru->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16l3-2 2 2 2-2 2 2 2-2 3 2V4a2 2 0 0 0-2-2z"/>
                </svg>
                <p class="text-sm text-slate-400">Belum ada transaksi</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No. Transaksi</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelanggan</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Cucian</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($transaksiTerbaru as $t)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3.5">
                                    <span class="font-mono text-xs font-medium text-blue-700">{{ $t->no_transaksi }}</span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <p class="font-medium text-slate-800">{{ $t->pelanggan->nama ?? '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ $t->pelanggan->no_hp ?? '' }}</p>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-slate-800">
                                    Rp{{ number_format($t->total_biaya, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3.5">
                                    @php
                                        $statusClass = match($t->status_cucian) {
                                            'Diterima'  => 'bg-blue-100 text-blue-700',
                                            'Diproses'  => 'bg-amber-100 text-amber-700',
                                            'Selesai'   => 'bg-green-100 text-green-700',
                                            'Diambil'   => 'bg-slate-100 text-slate-600',
                                            default     => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $t->status_cucian }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5">
                                    @if($t->status_bayar === 'Lunas')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Lunas</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Belum Lunas</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Rekap Status Cucian --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700 text-sm">Status Cucian Aktif</h3>
        </div>
        <div class="p-6 space-y-4">
            @php
                $statusList = [
                    ['label' => 'Diterima',  'color' => 'bg-blue-500',   'light' => 'bg-blue-50',   'text' => 'text-blue-700'],
                    ['label' => 'Diproses',  'color' => 'bg-amber-500',  'light' => 'bg-amber-50',  'text' => 'text-amber-700'],
                    ['label' => 'Selesai',   'color' => 'bg-green-500',  'light' => 'bg-green-50',  'text' => 'text-green-700'],
                    ['label' => 'Diambil',   'color' => 'bg-slate-400',  'light' => 'bg-slate-50',  'text' => 'text-slate-600'],
                ];
                $totalAktif = $rekapStatus->sum();
            @endphp

            @forelse($statusList as $s)
                @php
                    $count = $rekapStatus->get($s['label'], 0);
                    $pct = $totalAktif > 0 ? round(($count / $totalAktif) * 100) : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm text-slate-600">{{ $s['label'] }}</span>
                        <span class="text-sm font-semibold text-slate-800">{{ $count }}</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="{{ $s['color'] }} h-full rounded-full transition-all"
                            style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 text-center py-4">Tidak ada transaksi aktif</p>
            @endforelse

            <div class="pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Total Aktif</span>
                    <span class="font-bold text-slate-800">{{ $totalAktif }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

