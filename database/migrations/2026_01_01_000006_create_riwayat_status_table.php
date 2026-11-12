<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->foreignId('id_transaksi')->constrained('transaksi', 'id_transaksi');
            $table->foreignId('id_user')->constrained('users', 'id_user');
            $table->string('status', 20); // Diterima/Diproses/Selesai/Diambil
            $table->dateTime('waktu');
            $table->text('catatan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
    }
};

