<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_investasis', function (Blueprint $table) {
            $table->id();
            $table->string('id_user')->nullable();
            $table->string('jumlah_investasi')->nullable();
            $table->string('total_depo')->nullable();
            $table->string('total_wd')->nullable();
            $table->string('total_bonus')->nullable();
            $table->string('tanggal_penarikan')->nullable();
            $table->string('status_investasi')->nullable()->comment('1 berjalan 2 selesai');
            $table->string('tipe_investasi')->nullable();
            $table->string('status')->nullable()->comment('1 belum tercapai 2 tercapai');
            $table->string('status_wd')->nullable()->comment('1 belum wd 2 wd');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengajuan_investasis');
    }
};
