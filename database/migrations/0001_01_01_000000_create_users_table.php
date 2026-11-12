<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama', 100);
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('role', 20); // admin / pegawai
            $table->boolean('is_aktif')->default(true);
        });

        // Seed akun admin default
        DB::table('users')->insert([
            'nama'     => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
            'is_aktif' => true,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
