<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'pages',
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->unsigned()->nullable();
                $table->string('method')->nullable();
                $table->string('module')->nullable();
                $table->string('sql_products')->nullable();
                $table->unsignedTinyInteger('socials')->nullable();
                $table->timestamps();

                $table->foreign('parent_id')->references('id')->on('pages')
                    ->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('pages');
    }
}
