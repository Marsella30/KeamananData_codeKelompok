<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BarangTitipan
 * 
 * @property int $id_barang
 * @property int $id_penitip
 * @property int $id_pegawai
 * @property int|null $id_hunter
 * @property int $id_kategori
 * @property Carbon $tanggal_masuk
 * @property Carbon|null $tanggal_keluar
 * @property bool $status_perpanjangan
 * @property string $nama_barang
 * @property float $harga_jual
 * @property string $deskripsi
 * @property string $foto_barang
 * @property string $status_barang
 * @property bool $garansi
 * @property Carbon|null $tanggal_garansi
 * @property bool $barang_hunter
 * @property float $berat
 * 
 * @property Pegawai $pegawai
 * @property Kategori $kategori
 * @property Penitip $penitip
 * @property DetailKeranjang|null $detail_keranjang
 * @property DetailTransaksi|null $detail_transaksi
 * @property Collection|DiskusiProduk[] $diskusi_produks
 * @property Donasi|null $donasi
 * @property Collection|Rating[] $ratings
 *
 * @package App\Models
 */
class BarangTitipan extends Model
{
	protected $table = 'barang_titipan';
	protected $primaryKey = 'id_barang';
	public $timestamps = false;

	protected $casts = [
		'id_penitip' => 'int',
		'id_pegawai' => 'int',
		'id_qc_pegawai' => 'int',
		'id_hunter' => 'int',
		'id_kategori' => 'int',
		'tanggal_masuk' => 'datetime',
		'tanggal_akhir' => 'datetime',
		'tanggal_keluar' => 'datetime',
		'status_perpanjangan' => 'bool',
		'harga_jual' => 'float',
		'garansi' => 'bool',
		'tanggal_garansi' => 'datetime',
		'barang_hunter' => 'bool',
		'berat' => 'float',
		'id_nota' => 'int'
	];

	protected $fillable = [
		'id_penitip',
		'id_pegawai',
		'id_qc_pegawai',
		'id_hunter',
		'id_kategori',
		'tanggal_masuk',
		'tanggal_akhir',
		'tanggal_keluar',
		'status_perpanjangan',
		'nama_barang',
		'harga_jual',
		'deskripsi',
		'foto_barang',
		'status_barang',
		'garansi',
		'tanggal_garansi',
		'barang_hunter',
		'berat',
		'id_nota'
	];

	public function pegawai()
	{
		return $this->belongsTo(Pegawai::class, 'id_pegawai');
	}

	public function pegawaiQc() {
		return $this->belongsTo(Pegawai::class, 'id_qc_pegawai');
	}

	public function hunter() {
		return $this->belongsTo(Pegawai::class, 'id_hunter');
	}

	public function kategori()
	{
		return $this->belongsTo(Kategori::class, 'id_kategori');
	}

	public function penitip()
	{
		return $this->belongsTo(Penitip::class, 'id_penitip');
	}

	public function detail_keranjang()
	{
		return $this->hasOne(DetailKeranjang::class, 'id_barang');
	}

	public function detail_transaksi()
	{
		return $this->hasOne(DetailTransaksi::class, 'id_barang');
	}

	public function diskusi_produks()
	{
		return $this->hasMany(DiskusiProduk::class, 'id_barang');
	}

	public function donasi()
	{
		return $this->hasOne(Donasi::class, 'id_barang');
	}

	public function ratings()
	{
		return $this->hasMany(Rating::class, 'id_barang');
	}

	public function ratingDetail()
    {
        return $this->hasOne(Rating::class, 'id_barang', 'id_barang')->where('id_pembeli', auth()->guard('pembeli')->id());
    }

	public function fotoBarang()
	{
    	return $this->hasMany(FotoBarang::class, 'id_barang', 'id_barang');
	}

	public function nota()
	{
		return $this->belongsTo(NotaPenitipan::class, 'id_nota');
	}

	public function transaksi()
{
    return $this->hasOneThrough(
        \App\Models\Transaksi::class,
        \App\Models\DetailTransaksi::class,
        'id_barang', // FK di DetailTransaksi
        'id_transaksi', // FK di Transaksi
        'id_barang', // PK di BarangTitipan
        'id_transaksi'  // PK di Transaksi
    );
}
}
