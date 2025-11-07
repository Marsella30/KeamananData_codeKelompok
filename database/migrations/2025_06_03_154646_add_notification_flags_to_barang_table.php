<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('barang_titipan', function (Blueprint $table) {
            $table->boolean('notifikasi_h3_terkirim')->default(false);
            $table->boolean('notifikasi_hari_h_terkirim')->default(false);
        });
    }

    public function down()
    {
        Schema::table('barang_titipan', function (Blueprint $table) {
            $table->dropColumn(['notifikasi_h3_terkirim', 'notifikasi_hari_h_terkirim']);
        });
    }

};
