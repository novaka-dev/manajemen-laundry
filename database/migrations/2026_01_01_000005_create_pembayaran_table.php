<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_transaksi')->constrained('transaksi', 'id_transaksi');
            $table->foreignId('id_user')->constrained('users', 'id_user'); // kasir yang mencatat
            $table->dateTime('tgl_bayar');
            $table->decimal('jumlah_bayar', 12, 2);
            $table->string('metode', 10); // tunai / qris
            $table->string('no_referensi', 50)->nullable(); // diisi untuk QRIS
            $table->string('jenis', 15); // DP / Pelunasan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};

