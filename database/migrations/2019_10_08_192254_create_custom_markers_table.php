<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_markers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 150)->nullable();
            $table->string('image', 100)->nullable();
            $table->string('pattern', 100)->nullable();
            $table->string('thumb', 100)->nullable();
            $table->integer('user_id', false, true);

            // foreign key
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_markers');
    }
}
