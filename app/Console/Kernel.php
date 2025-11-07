<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\AutoPembatalanTransaksi::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('transaksi:auto-pembatalan')
                ->everyMinute()
                ->appendOutputTo(storage_path('logs/schedule.log'));

        $schedule->command('notifikasi:masa-penitipan')->dailyAt('08:00');

        $schedule->call(function () {
            DB::table('barang_titipan')
                ->where('status_barang', '=', 'tersedia')
                ->whereDate('tanggal_berakhir', '<', now()->subDays(7))
                ->update(['status_barang' => 'barang untuk donasi']);
        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
