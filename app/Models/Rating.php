<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Rating
 * 
 * @property int $id_rating
 * @property int $id_penitip
 * @property int $id_barang
 * @property int $id_pembeli
 * @property int $rating
 * 
 * @property BarangTitipan $barang_titipan
 * @property Pembeli $pembeli
 * @property Penitip $penitip
 *
 * @package App\Models
 */
class Rating extends Model
{
	protected $table = 'rating';
	protected $primaryKey = 'id_rating';
	public $timestamps = false;

	protected $casts = [
		'id_penitip' => 'int',
		'id_barang' => 'int',
		'id_pembeli' => 'int',
		'rating' => 'int'
	];

	protected $fillable = [
		'id_penitip',
		'id_barang',
		'id_pembeli',
		'rating'
	];

	public function barang_titipan()
	{
		return $this->belongsTo(BarangTitipan::class, 'id_barang');
	}

	public function pembeli()
	{
		return $this->belongsTo(Pembeli::class, 'id_pembeli');
	}

	public function penitip()
	{
		return $this->belongsTo(Penitip::class, 'id_penitip');
	}
}
