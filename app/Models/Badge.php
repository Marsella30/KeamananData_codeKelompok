<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Badge
 * 
 * @property int $id_badge
 * @property int $id_penitip
 * @property string $nama_badge
 * @property Carbon $periode_pemberian
 * 
 * @property Penitip $penitip
 *
 * @package App\Models
 */
class Badge extends Model
{
	protected $table = 'badge';
	protected $primaryKey = 'id_badge';
	public $timestamps = false;

	protected $casts = [
		'id_penitip' => 'int',
		'periode_pemberian' => 'datetime'
	];

	protected $fillable = [
		'id_penitip',
		'nama_badge',
		'periode_pemberian',
		'bonus',
		'total_penjualan',
	];

	public function penitip()
	{
		return $this->belongsTo(Penitip::class, 'id_penitip');
	}
}
