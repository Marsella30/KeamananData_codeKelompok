<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Penjadwalan;
use App\Models\Transaksi;

class JadwalDikirim extends Mailable
{
    use Queueable, SerializesModels;

    public $jadwal;
    public $transaksi;

    public function __construct(Penjadwalan $jadwal, Transaksi $transaksi)
    {
        $this->jadwal = $jadwal;
        $this->transaksi = $transaksi;
    }

    public function build()
    {
        return $this->subject('Jadwal Pengiriman/Pengambilan Telah Dijadwalkan')
                    ->view('emails.jadwal_dikirim');
    }
}
