<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RequestDonasi
 * 
 * @property int $id_request
 * @property int $id_organisasi
 * @property string $barang_dibutuhkan
 * @property string $status_request
 * 
 * @property Organisasi $organisasi
 * @property Donasi|null $donasi
 *
 * @package App\Models
 */
class RequestDonasi extends Model
{
	protected $table = 'request_donasi';
	protected $primaryKey = 'id_request';
	public $timestamps = false;

	protected $casts = [
		'id_organisasi' => 'int'
	];

	protected $fillable = [
		'id_organisasi',
		'barang_dibutuhkan',
		'status_request'
	];

	public function organisasi()
	{
		return $this->belongsTo(Organisasi::class, 'id_organisasi');
	}

	public function donasi()
	{
		return $this->hasOne(Donasi::class, 'id_request');
	}
}
