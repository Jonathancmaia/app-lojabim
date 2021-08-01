<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncomendasTable extends Migration
{
    public function up()
    {
        Schema::create('encomendas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('valor')->default(0);
            $table->string('transaction_code');
            $table->integer('cliente_id')->unsigned();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('encomendas');
    }
}
