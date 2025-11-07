<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Reward
 * 
 * @property int $id_reward
 * @property int|null $id_merchandise
 * @property int $id_pembeli
 * @property string $jenis_reward
 * @property int $jumlah_tukar_poin
 * @property Carbon $tanggal_penukaran
 * @property bool $status_penukaran
 * 
 * @property Pembeli $pembeli
 * @property Merchandise|null $merchandise
 *
 * @package App\Models
 */
class Reward extends Model
{
	protected $table = 'reward';
	protected $primaryKey = 'id_reward';
	public $timestamps = false;

	protected $casts = [
		'id_merchandise' => 'int',
		'id_pembeli' => 'int',
		'jumlah_tukar_poin' => 'int',
		'tanggal_klaim' => 'date',
		'tanggal_ambil' => 'date',
		'status_penukaran' => 'bool'
	];

	protected $fillable = [
		'id_merchandise',
		'id_pembeli',
		'jenis_reward',
		'jumlah_tukar_poin',
		'tanggal_klaim',
		'tanggal_ambil',
		'status_penukaran'
	];

	public function pembeli()
	{
		return $this->belongsTo(Pembeli::class, 'id_pembeli');
	}

	public function merchandise()
	{
		return $this->belongsTo(Merchandise::class, 'id_merchandise');
	}
}
