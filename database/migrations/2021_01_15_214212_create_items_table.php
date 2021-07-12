<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome', 100);
            $table->string('desc', 500);
            $table->string('preco');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}
