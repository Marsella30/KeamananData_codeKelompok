<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Keranjang
 * 
 * @property int $id_keranjang
 * @property int $id_pembeli
 * 
 * @property Pembeli $pembeli
 * @property DetailKeranjang|null $detail_keranjang
 *
 * @package App\Models
 */
class Keranjang extends Model
{
	protected $table = 'keranjang';
	protected $primaryKey = 'id_keranjang';
	public $timestamps = false;

	protected $casts = [
		'id_pembeli' => 'int'
	];

	protected $fillable = [
		'id_pembeli'
	];

	public function pembeli()
	{
		return $this->belongsTo(Pembeli::class, 'id_pembeli');
	}

	public function detail_keranjang()
	{
		return $this->hasOne(DetailKeranjang::class, 'id_keranjang');
	}
}
