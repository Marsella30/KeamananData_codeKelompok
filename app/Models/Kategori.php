<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Kategori
 * 
 * @property int $id_kategori
 * @property string $nama_kategori
 * 
 * @property Collection|BarangTitipan[] $barang_titipans
 *
 * @package App\Models
 */
class Kategori extends Model
{
	protected $table = 'kategori';
	protected $primaryKey = 'id_kategori';
	public $timestamps = false;

	protected $fillable = [
		'nama_kategori'
	];

	public function barang_titipans()
	{
		return $this->hasMany(BarangTitipan::class, 'id_kategori');
	}
}
