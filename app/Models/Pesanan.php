<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Many-to-Many ke Produk melalui tabel pivot detail_pesanan
    public function produks()
    {
        return $this->belongsToMany(Produk::class, 'detail_pesanan', 'id_pesanan', 'id_produk')
                    ->withPivot('jumlah')
                    ->withTimestamps();
    }
}