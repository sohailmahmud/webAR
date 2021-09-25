<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 150)->nullable();
            $table->text('description')->nullable();
            $table->char('type', 1)->default('s')->comment('s - single, b - bundle');
            $table->boolean('status')->default(0)->comment('0 - draft, 1 - published, 2 - archived');
            $table->boolean('editable')->default(1)->comment('0 - no, 1 - yes; if not editable only admin can edit.');
            $table->text('params')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->integer('user_id', false, true);

            //indexes
            $table->index('title');
            $table->index('type');
            $table->index('status');
            $table->index('published_at');
            $table->index('created_at');

            //foreign key
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
        Schema::dropIfExists('scenes');
    }
}
