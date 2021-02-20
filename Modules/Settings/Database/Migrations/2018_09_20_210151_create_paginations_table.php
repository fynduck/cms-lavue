<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaginationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'paginations',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('on')->index();
                $table->string('for')->index();
                $table->tinyInteger('value');
                $table->unsignedInteger('user_id')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')
                    ->onUpdate('set null')->onDelete('set null');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paginations');
    }
}
