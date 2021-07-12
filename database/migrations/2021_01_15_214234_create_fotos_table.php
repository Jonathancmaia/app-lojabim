<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFotosTable extends Migration
{
    public function up()
    {
        Schema::create('fotos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('img');
            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('items')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fotos');
    }
}
