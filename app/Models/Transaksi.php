<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    public $timestamps = false;

    protected $fillable = [
        'no_transaksi',
        'id_pelanggan',
        'id_user',
        'tgl_terima',
        'estimasi_selesai',
        'total_biaya',
        'status_cucian',
        'status_bayar',
        'status_transaksi',
        'alasan_batal',
    ];

    protected function casts(): array
    {
        return [
            'tgl_terima'       => 'datetime',
            'estimasi_selesai' => 'datetime',
            'total_biaya'      => 'decimal:2',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_transaksi', 'id_transaksi');
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class, 'id_transaksi', 'id_transaksi');
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class, 'id_transaksi', 'id_transaksi');
    }

    public function isAktif(): bool
    {
        return $this->status_transaksi === 'Aktif';
    }

    public function isLunas(): bool
    {
        return $this->status_bayar === 'Lunas';
    }
}

