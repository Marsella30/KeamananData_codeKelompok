<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $primaryKey = 'id_jabatan';
    public $timestamps = false;

    protected $fillable = [
        'nama_jabatan',
    ];

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'id_jabatan');
    }

    public function pegawais()
	{
		return $this->hasMany(Pegawai::class, 'id_jabatan');
	}

}
