<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Komisi
 *
 * @property int $id_komisi
 * @property int|null $id_pegawai
 * @property int $id_transaksi
 * @property int $id_penitip
 * @property int $id_barang
 * @property float $komisi
 * @property float|null $komisi_hunter
 * @property float $komisi_penitip
 *
 * @property Pegawai|null $pegawai
 * @property Penitip $penitip
 * @property Transaksi $transaksi
 * @property BarangTitipan $barang
 *
 * @package App\Models
 */
class Komisi extends Model
{
    protected $table = 'komisi';
    protected $primaryKey = 'id_komisi';
    public $timestamps = false;

    protected $casts = [
        'id_pegawai' => 'int',
        'id_transaksi' => 'int',
        'id_penitip' => 'int',
        'id_barang' => 'int',
        'komisi' => 'float',
        'komisi_hunter' => 'float',
        'komisi_penitip' => 'float'
    ];

    protected $fillable = [
        'id_pegawai',
        'id_transaksi',
        'id_penitip',
        'id_barang',
        'komisi',
        'komisi_hunter',
        'komisi_penitip'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }

    public function barang()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang');
    }
}
