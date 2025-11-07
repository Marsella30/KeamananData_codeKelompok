<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\BarangTitipanController;

class KirimNotifikasiMasaPenitipan extends Command
{
    protected $signature = 'notifikasi:masa-penitipan';

    protected $description = 'Kirim notifikasi H-3 dan Hari H masa penitipan barang';

    public function handle()
    {
        $controller = new BarangController();
        $response = $controller->kirimNotifikasiMasaPenitipan();
        $this->info($response->getData()->message);
    }
}
