<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $primaryKey = 'id_layanan';
    public $timestamps = false;

    protected $fillable = [
        'nama_layanan',
        'satuan',
        'tarif',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'tarif'    => 'decimal:2',
            'is_aktif' => 'boolean',
        ];
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_layanan', 'id_layanan');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}

