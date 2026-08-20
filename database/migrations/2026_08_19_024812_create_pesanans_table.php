<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bikin tabel pesanans DULUAN!
        Schema::create('pesanans', function (Blueprint $table) {
            // PAKAI 'id' BIASA! Supaya cocok dengan foreign key bawaan
            $table->id(); 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->timestamps();
        });

        // 2. BARU bikin tabel detail_pesanan!
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            
            // Karena di atas $table->id(), maka di sini cukup standard seperti ini:
            $table->foreignId('id_pesanan')->constrained('pesanans')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('produks', 'id_produk')->onDelete('cascade');
            
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanans');
    }
};