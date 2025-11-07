<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Penjadwalan
 * 
 * @property int $id_jadwal
 * @property int $id_transaksi
 * @property string $jenis_jadwal
 * @property Carbon|null $tanggal_jadwal
 * @property string $status_jadwal
 * 
 * @property Transaksi $transaksi
 * @property Collection|Pengiriman[] $pengirimen
 *
 * @package App\Models
 */
class Penjadwalan extends Model
{
	protected $table = 'penjadwalan';
	protected $primaryKey = 'id_jadwal';
	public $timestamps = false;

	protected $casts = [
		'id_transaksi' => 'int',
		'tanggal_jadwal' => 'datetime'
	];

	protected $fillable = [
		'id_transaksi',
		'jenis_jadwal',
		'tanggal_jadwal',
		'status_jadwal'
	];

	// public function transaksi()
	// {
	// 	return $this->belongsTo(Transaksi::class, 'id_transaksi');
	// }

	// public function pengirimen()
	// {
	// 	return $this->hasMany(Pengiriman::class, 'id_jadwal');
	// }

	public function transaksi()
	{
		return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
	}

	public function pengiriman()
	{
		return $this->hasOne(Pengiriman::class, 'id_jadwal', 'id_jadwal');
	}

	public function kurir()
	{
		// Relasi melalui Pengiriman, hanya jika id_pegawai ada
		return $this->hasOneThrough(
			Pegawai::class,  // Model tujuan
			Pengiriman::class, // Model perantara
			'id_jadwal',     // Foreign key di pengiriman
			'id_pegawai',    // Foreign key di pegawai
			'id_jadwal',     // Local key di penjadwalan
			'id_pegawai'     // Local key di pengiriman
		);
	}

}
