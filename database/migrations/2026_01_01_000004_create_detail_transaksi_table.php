<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_transaksi')->constrained('transaksi', 'id_transaksi');
            $table->foreignId('id_layanan')->constrained('layanan', 'id_layanan');
            $table->decimal('jumlah', 8, 2); // kg (bisa pecahan) atau pcs (bulat)
            $table->decimal('tarif_saat_itu', 10, 2); // snapshot tarif saat transaksi dibuat
            $table->decimal('subtotal', 12, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};

