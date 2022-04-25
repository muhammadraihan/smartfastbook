<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('no_ref')->nullable();
            $table->string('customer')->nullable();
            $table->string('bank_tujuan')->nullable();
            $table->string('no_rek')->nullable();
            $table->string('bank_uuid')->nullable();
            $table->biginteger('biaya_admin')->nullable();
            $table->biginteger('admin_bank')->nullable();
            $table->string('keterangan')->nullable();
            $table->biginteger('nominal')->nullable();
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
        Schema::dropIfExists('transaksis');
    }
}
