<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotaPenitipan extends Model
{
    use HasFactory;

    protected $table = 'nota_penitipan';
    protected $primaryKey = 'id_nota';

    public $timestamps = false;

    protected $fillable = [
        'no_nota',
        'tanggal_penitipan',
        'masa_berakhir',
        'id_penitip',
        'id_qc_pegawai',
    ];

    // === RELASI ===

    // Relasi ke penitip (penitip satu)
    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip');
    }

    // Relasi ke pegawai QC
    public function pegawaiQc()
    {
        return $this->belongsTo(Pegawai::class, 'id_qc_pegawai');
    }

    // Relasi ke daftar barang titipan
    public function barangTitipan()
    {
        return $this->hasMany(BarangTitipan::class, 'id_nota');
    }
}
