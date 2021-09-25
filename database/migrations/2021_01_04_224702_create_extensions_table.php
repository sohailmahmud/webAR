<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extensions', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('type', 50);
            $table->text('props');
            $table->integer('scene_id', false, true);

            //foreign key
            $table->foreign('scene_id')->references('id')->on('scenes');

            //configuration
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4'; 	
            $table->collation = 'utf8mb4_unicode_ci';
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extensions');
    }
}
