<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index()
    {
        $layanan = Layanan::orderByDesc('is_aktif')->orderBy('nama_layanan')->get();
        return view('layanan.index', compact('layanan'));
    }

    public function create()
    {
        return view('layanan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => ['required', 'string', 'max:100'],
            'satuan'       => ['required', 'in:kg,pcs'],
            'tarif'        => ['required', 'numeric', 'min:0'],
        ], [
            'nama_layanan.required' => 'Nama layanan wajib diisi.',
            'satuan.in'             => 'Satuan harus kg atau pcs.',
            'tarif.required'        => 'Tarif wajib diisi.',
            'tarif.min'             => 'Tarif tidak boleh negatif.',
        ]);

        Layanan::create([
            'nama_layanan' => $request->nama_layanan,
            'satuan'       => $request->satuan,
            'tarif'        => $request->tarif,
            'is_aktif'     => true,
        ]);

        return redirect()->route('layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Layanan $layanan)
    {
        return view('layanan.edit', compact('layanan'));
    }

    public function update(Request $request, Layanan $layanan)
    {
        $request->validate([
            'nama_layanan' => ['required', 'string', 'max:100'],
            'satuan'       => ['required', 'in:kg,pcs'],
            'tarif'        => ['required', 'numeric', 'min:0'],
            'is_aktif'     => ['boolean'],
        ]);

        $layanan->update([
            'nama_layanan' => $request->nama_layanan,
            'satuan'       => $request->satuan,
            'tarif'        => $request->tarif,
            'is_aktif'     => $request->boolean('is_aktif'),
        ]);

        return redirect()->route('layanan.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function toggleAktif(Layanan $layanan)
    {
        // Nonaktif bukan hapus, sesuai spesifikasi
        $layanan->update(['is_aktif' => !$layanan->is_aktif]);

        $status = $layanan->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('layanan.index')
            ->with('success', "Layanan berhasil {$status}.");
    }
}
