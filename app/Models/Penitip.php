<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Services\FirebaseService;
/**
 * Class Penitip
 * 
 * @property int $id_penitip
 * @property string $no_ktp
 * @property string $nama_penitip
 * @property string $username
 * @property string $password
 * @property int $poin
 * @property string $alamat
 * @property string $email
 * @property float $saldo_penitip
 * @property bool $status_aktif
 * 
 * @property Collection|Badge[] $badges
 * @property Collection|BarangTitipan[] $barang_titipans
 * @property Collection|Komisi[] $komisis
 * @property Collection|Rating[] $ratings
 *
 * @package App\Models
 */
class Penitip extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable;

	protected $table = 'penitip';
	protected $primaryKey = 'id_penitip';
	public $timestamps = false;

	protected $casts = [
		'poin' => 'int',
		'saldo_penitip' => 'float',
        'nominal_tarik' => 'float',
		'status_aktif' => 'bool'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
        'no_ktp',
        'foto_ktp', 
        'nama_penitip',
        'username',
        'password',
        'poin',
        'alamat',
        'email',
        'saldo_penitip',
        'nominal_tarik',
        'status_aktif',
		'fcm_token'
    ];

	public function badges()
	{
		return $this->hasMany(Badge::class, 'id_penitip');
	}

	public function barang_titipans()
	{
		return $this->hasMany(BarangTitipan::class, 'id_penitip');
	}

	public function komisis()
	{
		return $this->hasMany(Komisi::class, 'id_penitip');
	}

	public function ratings()
	{
		return $this->hasMany(Rating::class, 'id_penitip');
	}

	public function getAuthPassword()
    {
        return $this->password;
    }

	public function kirimNotifikasiMasaPenitipan($jenisNotifikasi, $namaBarang, $kodeBarang, $sudahPerpanjang = false)
    {
        $fcmToken = $this->fcm_token;
        if (!$fcmToken) {
            \Log::info("Tidak ada FCM token untuk user {$this->nama_penitip}");
            return;
        }

        $firebase = new FirebaseService();

        if ($jenisNotifikasi === 'h-3') {
            $title = "Pengingat: Masa Penitipan Barang Segera Berakhir (H-3)";
            $body = !$sudahPerpanjang
                ? "3 Hari lagi. masa penitipan barang '{$namaBarang}' dengan kode {$kodeBarang} akan segera berakhir. Anda masih mempunyai kesempatan untuk memperpanjang masa penitipan, segera konfirmasi di web Kami ReUseMart."
                : "3 hari lagi, masa penitipan barang '{$namaBarang}' dengan kode {$kodeBarang} akan segera berakhir. Kesempatan Anda sudah habis (perpanjangan sudah pernah dipakai), segera ambil barang Anda.";
        } elseif ($jenisNotifikasi === 'hari-h') {
            $title = !$sudahPerpanjang
                ? "Hari Ini Masa Penitipan Berakhir"
                : "Hari Ini Masa Perpanjangan Penitipan Berakhir";
            $body = !$sudahPerpanjang
                ? "Hari ini, masa penitipan barang '{$namaBarang}' dengan kode {$kodeBarang} akan segera berakhir. Anda masih memiliki waktu 7 hari untuk mengambilnya."
                : "Hari ini, masa perpanjangan barang '{$namaBarang}' dengan kode {$kodeBarang} akan segera berakhir. Anda masih memiliki waktu 7 hari untuk mengambilnya.";
        } else {
            \Log::info("Jenis notifikasi tidak dikenal: $jenisNotifikasi");
            return;
        }

        $firebase->sendMessage($fcmToken, $title, $body);
        
        \Log::info("Notifikasi dikirim ke {$this->nama_penitip} dengan judul: $title");
    }

    public function transaksi()
    {
        return $this->hasManyThrough(Transaksi::class, BarangTitipan::class, 'id_penitip', 'id_barang', 'id_penitip', 'id_barang');
    }

}
