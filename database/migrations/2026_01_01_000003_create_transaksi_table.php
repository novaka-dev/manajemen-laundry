<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->string('no_transaksi', 30)->unique();
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan');
            $table->foreignId('id_user')->constrained('users', 'id_user');
            $table->dateTime('tgl_terima');
            $table->dateTime('estimasi_selesai')->nullable();
            $table->decimal('total_biaya', 12, 2)->default(0);
            $table->string('status_cucian', 20)->default('Diterima'); // Diterima/Diproses/Selesai/Diambil
            $table->string('status_bayar', 20)->default('Belum Lunas'); // Belum Lunas/Lunas
            $table->string('status_transaksi', 20)->default('Aktif'); // Aktif/Batal
            $table->text('alasan_batal')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};

