<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DetailKeranjang
 * 
 * @property int $id_keranjang
 * @property int $id_barang
 * 
 * @property BarangTitipan $barang_titipan
 * @property Keranjang $keranjang
 *
 * @package App\Models
 */
class DetailKeranjang extends Model
{
	protected $table = 'detail_keranjang';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_keranjang' => 'int',
		'id_barang' => 'int'
	];

	protected $fillable = [
		'id_keranjang',
		'id_barang'
	];

	public function barang_titipan()
	{
		return $this->belongsTo(BarangTitipan::class, 'id_barang');
	}

	public function keranjang()
	{
		return $this->belongsTo(Keranjang::class, 'id_keranjang');
	}
}
