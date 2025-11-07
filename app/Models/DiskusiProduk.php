<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DiskusiProduk
 * 
 * @property int $id_diskusi
 * @property int $id_pembeli
 * @property int $id_pegawai
 * @property int $id_barang
 * @property string $pertanyaan
 * @property string $jawaban
 * @property Carbon $tanggal_tanya
 * @property Carbon $tanggal_jawab
 * 
 * @property BarangTitipan $barang_titipan
 * @property Pegawai $pegawai
 * @property Pembeli $pembeli
 *
 * @package App\Models
 */
class DiskusiProduk extends Model
{
	protected $table = 'diskusi_produk';
	protected $primaryKey = 'id_diskusi';
	public $timestamps = false;

	protected $casts = [
		'id_pembeli' => 'int',
		'id_pegawai' => 'int',
		'id_barang' => 'int',
		'tanggal_tanya' => 'datetime',
		'tanggal_jawab' => 'datetime'
	];

	protected $fillable = [
		'id_pembeli',
		'id_pegawai',
		'id_barang',
		'pertanyaan',
		'jawaban',
		'tanggal_tanya',
		'tanggal_jawab'
	];

	public function barang_titipan()
	{
		return $this->belongsTo(BarangTitipan::class, 'id_barang');
	}

	public function pegawai()
	{
		return $this->belongsTo(Pegawai::class, 'id_pegawai');
	}

	public function pembeli()
	{
		return $this->belongsTo(Pembeli::class, 'id_pembeli');
	}
}
