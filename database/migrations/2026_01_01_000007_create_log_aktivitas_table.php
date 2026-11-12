<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id('id_log');
            $table->foreignId('id_user')->constrained('users', 'id_user');
            $table->foreignId('id_transaksi')->nullable()->constrained('transaksi', 'id_transaksi')->nullOnDelete();
            $table->string('aksi', 20); // edit / batal
            $table->text('keterangan')->nullable();
            $table->dateTime('waktu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};

