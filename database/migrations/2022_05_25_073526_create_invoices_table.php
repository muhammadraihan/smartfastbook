<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('no_invoice')->nullable();
            $table->string('perusahaan_uuid')->nullable();
            $table->string('tujuan')->nullable();
            $table->string('alamat')->nullable();
            $table->text('deskripsi')->nullable();
            $table->bigInteger('jumlah')->nullable();
            $table->bigInteger('harga_satuan')->nullable();
            $table->string('total')->nullable();
            $table->bigInteger('pajak')->nullable();
            $table->string('subtotal')->nullable();
            $table->text('catatan')->nullable();
            $table->date('tanggal_invoice')->nullable();
            $table->date('jatuh_tempo')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
