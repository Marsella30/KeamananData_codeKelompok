<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Transaksi;
use App\Models\Penitip;
use Illuminate\Support\Collection;

class transaksiDisiapkan extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaksi;
    protected $penitip;
    protected $barangList; // Koleksi nama barang

    /**
     * @param Transaksi $transaksi
     * @param Penitip $penitip
     * @param Collection|string[] $barangList List nama barang penitip
     */
    public function __construct(Transaksi $transaksi, Penitip $penitip, Collection $barangList)
    {
        $this->transaksi = $transaksi;
        $this->penitip = $penitip;
        $this->barangList = $barangList;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Buat string daftar nama barang, dipisah koma
        $daftarBarang = $this->barangList->implode(', ');

        return (new MailMessage)
            ->subject('Barang Anda telah terjual')
            ->greeting('Halo ' . ($this->penitip->nama_penitip ?? 'Penitip') . ',')
            ->line('Barang Anda berikut telah terjual dan telah dibayar:')
            ->line($daftarBarang)
            ->line('Terima kasih telah mempercayakan barang Anda di ReUseMart!')
            ->line('Jika ada pertanyaan, jangan ragu menghubungi kami.');
    }
}