<?php

namespace App\Mail;

use App\Models\Penjadwalan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KonfirmasiPengiriman extends Mailable
{
    use Queueable, SerializesModels;

    public $jadwal;
    public $statusBaru;

    public function __construct(Penjadwalan $jadwal, $statusBaru)
    {
        $this->jadwal = $jadwal;
        $this->statusBaru = $statusBaru;
    }

    public function build()
    {
        return $this->subject('Konfirmasi Pengiriman/Pengambilan Barang')
                    ->view('emails.konfirmasi_pengiriman');
    }
}
