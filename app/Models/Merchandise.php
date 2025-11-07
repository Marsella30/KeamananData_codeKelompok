<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Merchandise
 * 
 * @property int $id_merchandise
 * @property string $nama_merchandise
 * @property int $jumlah_poin
 * @property int $banyak_merchandise
 * 
 * @property Collection|Reward[] $rewards
 *
 * @package App\Models
 */
class Merchandise extends Model
{
	protected $table = 'merchandise';
	protected $primaryKey = 'id_merchandise';
	public $timestamps = false;

	protected $casts = [
		'jumlah_poin' => 'int',
		'banyak_merchandise' => 'int'
	];

	protected $fillable = [
		'nama_merchandise',
		'jumlah_poin',
		'banyak_merchandise'
	];

	public function rewards()
	{
		return $this->hasMany(Reward::class, 'id_merchandise');
	}
}
