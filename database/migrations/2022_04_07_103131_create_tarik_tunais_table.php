<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarikTunaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarik_tunais', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('no_ref')->nullable();
            $table->string('customer')->nullable();
            $table->string('no_kartu')->nullable();
            $table->string('bank_uuid')->nullable();
            $table->biginteger('biaya_admin')->nullable();
            $table->biginteger('admin_bank')->nullable();
            $table->string('keterangan')->nullable();
            $table->biginteger('nominal')->nullable();
            $table->string('created_by')->nullable();
            $table->string('edited_by')->nullable();
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
        Schema::dropIfExists('tarik_tunais');
    }
}
