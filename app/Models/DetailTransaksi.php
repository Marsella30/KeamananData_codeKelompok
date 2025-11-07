<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DetailTransaksi
 * 
 * @property int $id_transaksi
 * @property int $id_barang
 * @property float $sub_total
 * 
 * @property BarangTitipan $barang_titipan
 * @property Transaksi $transaksi
 *
 * @package App\Models
 */
class DetailTransaksi extends Model
{
	protected $table = 'detail_transaksi';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_transaksi' => 'int',
		'id_barang' => 'int',
		'sub_total' => 'float'
	];

	protected $fillable = [
		'id_transaksi',
		'id_barang',
		'sub_total'
	];

	public function barang_titipan()
	{
		return $this->belongsTo(BarangTitipan::class, 'id_barang');
	}

	public function barang()
	{
		return $this->belongsTo(\App\Models\BarangTitipan::class, 'id_barang');
	}

	public function transaksi()
	{
		return $this->belongsTo(Transaksi::class, 'id_transaksi');
	}
}
