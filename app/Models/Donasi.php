<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Donasi
 * 
 * @property int $id_request
 * @property int $id_barang
 * @property Carbon $tanggal_donasi
 * @property string $penerima
 * 
 * @property BarangTitipan $barang_titipan
 * @property RequestDonasi $request_donasi
 *
 * @package App\Models
 */
class Donasi extends Model
{
	protected $table = 'donasi';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_request' => 'int',
		'id_barang' => 'int',
		'tanggal_donasi' => 'datetime'
	];

	protected $fillable = [
		'id_request',
		'id_barang',
		'tanggal_donasi',
		'penerima'
	];

	public function barang_titipan()
	{
		return $this->belongsTo(BarangTitipan::class, 'id_barang');
	}

	public function request_donasi()
	{
		return $this->belongsTo(RequestDonasi::class, 'id_request');
	}
}
