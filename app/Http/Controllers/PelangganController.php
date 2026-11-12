<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $pelanggan = Pelanggan::when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('pelanggan.index', compact('pelanggan', 'search'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'   => ['required', 'string', 'max:100'],
            'no_hp'  => ['nullable', 'string', 'max:20', 'unique:pelanggan,no_hp'],
            'alamat' => ['nullable', 'string'],
        ], [
            'nama.required'  => 'Nama pelanggan wajib diisi.',
            'no_hp.unique'   => 'Nomor HP sudah terdaftar untuk pelanggan lain.',
        ]);

        Pelanggan::create($request->only('nama', 'no_hp', 'alamat'));

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'nama'   => ['required', 'string', 'max:100'],
            'no_hp'  => ['nullable', 'string', 'max:20', 'unique:pelanggan,no_hp,' . $pelanggan->id_pelanggan . ',id_pelanggan'],
            'alamat' => ['nullable', 'string'],
        ], [
            'nama.required' => 'Nama pelanggan wajib diisi.',
            'no_hp.unique'  => 'Nomor HP sudah terdaftar untuk pelanggan lain.',
        ]);

        $pelanggan->update($request->only('nama', 'no_hp', 'alamat'));

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        // Hanya admin (sudah dijaga di route)
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    /**
     * API endpoint: cari pelanggan by no_hp (untuk form transaksi)
     */
    public function cariByHp(Request $request)
    {
        $noHp = $request->input('no_hp');
        $pelanggan = Pelanggan::where('no_hp', $noHp)->first();

        if ($pelanggan) {
            return response()->json(['found' => true, 'pelanggan' => $pelanggan]);
        }

        return response()->json(['found' => false]);
    }
}
