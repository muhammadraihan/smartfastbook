<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kass', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('bank_uuid')->nullable();
            $table->string('name')->nullable();
            $table->string('no_rek')->nullable();
            $table->string('nama_rek')->nullable();
            $table->string('saldo')->nullable();
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
        Schema::dropIfExists('kass');
    }
}
