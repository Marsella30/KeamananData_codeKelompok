<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\BarangTitipan;

class BarangDidonasikan extends Notification
{
    use Queueable;

    protected $barang;
    // protected $tanggal;

    // public function __construct($barang, $tanggal)
    // {
    //     $this->barang = $barang;
    //     $this->tanggal = $tanggal;
    // }
    public function __construct(BarangTitipan $barang)
    {
        $this->barang = $barang;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //         ->subject('Barang Anda Telah Didonasikan')
    //         ->greeting("Halo, {$notifiable->nama_penitip}")
    //         ->line("Barang '{$this->barang->nama_barang}' Anda telah didonasikan.")
    //         ->line("Tanggal donasi: {$this->tanggal}")
    //         ->line('Terima kasih atas partisipasi Anda di ReUseMart!');
    // }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Barang {$this->barang->nama_barang} telah didonasikan.",
            'barang_id' => $this->barang->id_barang,
            'tanggal_donasi' => now(),
        ];
    }

    // public function toArray($notifiable)
    // {
    //     return [
    //         'pesan' => "Barang '{$this->barang->nama_barang}' Anda telah didonasikan pada {$this->tanggal}."
    //     ];
    // }
}