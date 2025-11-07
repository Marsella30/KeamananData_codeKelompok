<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pembayaran
 * 
 * @property int $id_pembayaran
 * @property int $id_pembeli
 * @property int $id_pegawai
 * @property int $id_transaksi
 * @property string|null $bukti_transfer
 * @property bool $status_verifikasi
 * 
 * @property Pegawai $pegawai
 * @property Pembeli $pembeli
 * @property Transaksi $transaksi
 *
 * @package App\Models
 */
class Pembayaran extends Model
{
	protected $table = 'pembayaran';
	protected $primaryKey = 'id_pembayaran';
	public $timestamps = false;

	protected $casts = [
		'id_pembeli' => 'int',
		'id_pegawai' => 'int',
		'id_transaksi' => 'int',
		'status_verifikasi' => 'bool'
	];

	protected $fillable = [
		'id_pembeli',
		'id_pegawai',
		'id_transaksi',
		'bukti_transfer',
		'status_verifikasi'
	];

	public function pegawai()
	{
		return $this->belongsTo(Pegawai::class, 'id_pegawai');
	}

	public function pembeli()
	{
		return $this->belongsTo(Pembeli::class, 'id_pembeli');
	}

	public function transaksi()
	{
		return $this->belongsTo(Transaksi::class, 'id_transaksi');
	}
}
