<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pegawai extends Authenticatable
{
	use HasApiTokens, Notifiable;

	protected $table = 'pegawai';
	protected $primaryKey = 'id_pegawai';
	public $timestamps = false;

	protected $casts = [
		'id_jabatan' => 'int',
		'tanggal_lahir' => 'datetime',
		'status_aktif' => 'bool'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'id_jabatan',
		'nama_pegawai',
		'email',
		'notelp',
		'tanggal_lahir',
		'username',
		'password',
		'status_aktif'
	];

	public function jabatan()
	{
		return $this->belongsTo(Jabatan::class, 'id_jabatan');
	}

	public function barang_titipans()
	{
		return $this->hasMany(BarangTitipan::class, 'id_pegawai');
	}

	public function diskusi_produks()
	{
		return $this->hasMany(DiskusiProduk::class, 'id_pegawai');
	}

	public function komisis()
	{
		return $this->hasMany(Komisi::class, 'id_pegawai');
	}

	public function pembayarans()
	{
		return $this->hasMany(Pembayaran::class, 'id_pegawai');
	}

	public function pengirimen()
	{
		return $this->hasMany(Pengiriman::class, 'Id_pegawai');
	}

	public function getAuthPassword()
    {
        return $this->password;
    }
}
