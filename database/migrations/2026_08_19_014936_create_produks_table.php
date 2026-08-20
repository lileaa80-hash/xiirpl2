<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id('id_produk');
            $table->unsignedBigInteger('id_kategori');
            $table->string('nama_produk');
            $table->integer('harga');
            $table->integer('stok');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // Relasi foreign key ke tabel kategori
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};