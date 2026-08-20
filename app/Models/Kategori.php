<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    // Sesuaikan nama tabel dan primary key di database
    protected $table      = 'kategori';
    protected $primaryKey = 'id_kategori'; 

    protected $fillable   = ['nama_kategori'];
    public $timestamps    = true;

    // Relasi ke Produk
    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_kategori', 'id_kategori');
    }
}