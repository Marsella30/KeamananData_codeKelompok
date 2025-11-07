<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AlamatPembeli
 * 
 * @property int $id_alamat_pembeli
 * @property int $id_pembeli
 * @property string $jalan
 * @property string $kelurahan
 * @property string $kecamatan
 * @property string $kota
 * @property string $provinsi
 * @property string $kode_pos
 * @property string|null $detail
 * 
 * @property Pembeli $pembeli
 *
 * @package App\Models
 */
class AlamatPembeli extends Model
{
	protected $table = 'alamat_pembeli';
	protected $primaryKey = 'id_alamat_pembeli';
	public $timestamps = false;

	protected $casts = [
		'id_pembeli' => 'int'
	];

	protected $fillable = [
		'id_pembeli',
		'jalan',
		'kelurahan',
		'kecamatan',
		'kota',
		'provinsi',
		'kode_pos',
		'detail'
	];

	public function pembeli()
	{
		return $this->belongsTo(Pembeli::class, 'id_pembeli');
	}
}
