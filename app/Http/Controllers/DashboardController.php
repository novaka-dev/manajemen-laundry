<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // Statistik hari ini
        $transaksiHariIni = Transaksi::whereDate('tgl_terima', $today)
            ->where('status_transaksi', 'Aktif')
            ->count();

        $pendapatanHariIni = Pembayaran::whereDate('tgl_bayar', $today)
            ->sum('jumlah_bayar');

        $cucianBelumSelesai = Transaksi::whereIn('status_cucian', ['Diterima', 'Diproses'])
            ->where('status_transaksi', 'Aktif')
            ->count();

        $belumLunas = Transaksi::where('status_bayar', 'Belum Lunas')
            ->where('status_transaksi', 'Aktif')
            ->count();

        // Transaksi terbaru (10 terakhir)
        $transaksiTerbaru = Transaksi::with(['pelanggan', 'user'])
            ->orderByDesc('tgl_terima')
            ->limit(10)
            ->get();

        // Rekap status cucian
        $rekapStatus = Transaksi::where('status_transaksi', 'Aktif')
            ->select('status_cucian', DB::raw('count(*) as total'))
            ->groupBy('status_cucian')
            ->pluck('total', 'status_cucian');

        return view('dashboard', compact(
            'transaksiHariIni',
            'pendapatanHariIni',
            'cucianBelumSelesai',
            'belumLunas',
            'transaksiTerbaru',
            'rekapStatus',
        ));
    }
}

