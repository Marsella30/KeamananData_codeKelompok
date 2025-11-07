<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoBarang extends Model
{
    use HasFactory;

    protected $table = 'foto_barang';
    protected $primaryKey = 'id_foto';

    public $timestamps = false;

    protected $fillable = [
        'id_barang',
        'nama_file',
        'urutan',
    ];

    // Relasi: banyak foto dimiliki satu barang
    public function barang()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang', 'id_barang');
    }
}
