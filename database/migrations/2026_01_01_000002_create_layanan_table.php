<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->id('id_layanan');
            $table->string('nama_layanan', 100);
            $table->string('satuan', 10); // kg / pcs
            $table->decimal('tarif', 10, 2);
            $table->boolean('is_aktif')->default(true); // nonaktif tidak dihapus
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};

