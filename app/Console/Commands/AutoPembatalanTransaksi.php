<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PengirimanController;

class AutoPembatalanTransaksi extends Command
{
    protected $signature = 'transaksi:auto-pembatalan';
    protected $description = 'Menjalankan auto pembatalan transaksi yang sudah lewat 2 hari';

    public function handle()
    {
        \Log::info('Command auto-pembatalan berjalan pada: ' . now());
        (new PengirimanController)->autoPembatalanTransaksi();
        $this->info('Auto pembatalan transaksi dijalankan.');
    }
}
