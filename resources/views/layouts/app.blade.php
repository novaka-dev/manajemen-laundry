<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — LaundryKu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">

<div class="flex h-full">
    {{-- Sidebar --}}
    <aside class="w-64 bg-blue-900 text-white flex flex-col shrink-0">
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-blue-800">
            <div class="w-9 h-9 bg-blue-400 rounded-lg flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm leading-tight">LaundryKu</p>
                <p class="text-xs text-blue-300">Sistem Manajemen</p>
            </div>
        </div>

        {{-- User Info --}}
        <div class="px-6 py-4 border-b border-blue-800">
            <p class="text-xs text-blue-300 uppercase tracking-wider mb-1">Login sebagai</p>
            <p class="font-semibold text-sm">{{ auth()->user()->nama }}</p>
            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                {{ auth()->user()->role === 'admin' ? 'bg-amber-500 text-amber-950' : 'bg-blue-500 text-white' }}">
                {{ ucfirst(auth()->user()->role) }}
            </span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            {{-- Semua Role --}}
            <x-nav-item route="dashboard" icon="home">Dashboard</x-nav-item>

            {{-- Kasir / Pegawai --}}
            @if(auth()->user()->role === 'pegawai' || auth()->user()->role === 'admin')
                <div class="pt-3 pb-1">
                    <p class="px-3 text-xs font-semibold text-blue-400 uppercase tracking-wider">Operasional</p>
                </div>
                {{-- Transaksi (akan diaktifkan nanti) --}}
                <x-nav-item route="dashboard" icon="receipt">Transaksi Baru</x-nav-item>
                <x-nav-item route="dashboard" icon="list">Daftar Cucian</x-nav-item>
                <x-nav-item route="dashboard" icon="users">Pelanggan</x-nav-item>
            @endif

            {{-- Admin Only --}}
            @if(auth()->user()->role === 'admin')
                <div class="pt-3 pb-1">
                    <p class="px-3 text-xs font-semibold text-blue-400 uppercase tracking-wider">Manajemen</p>
                </div>
                <x-nav-item route="dashboard" icon="users-gear">Kelola Pegawai</x-nav-item>
                <x-nav-item route="dashboard" icon="tag">Layanan & Tarif</x-nav-item>
                <x-nav-item route="dashboard" icon="chart">Laporan</x-nav-item>
            @endif
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-blue-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-blue-300 hover:bg-blue-800 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
        {{-- Top Bar --}}
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between shrink-0">
            <div>
                <h1 class="text-xl font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                @hasSection('page-subtitle')
                    <p class="text-sm text-slate-500 mt-0.5">@yield('page-subtitle')</p>
                @endif
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="px-8 pt-4">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto px-8 py-4 pb-8">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>

