<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('date')->default(\Carbon\Carbon::now());
            $table->timestamp('date_from')->nullable();
            $table->timestamp('date_to')->nullable();
            $table->double('discount', 4, 2)->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedTinyInteger('socials');
            $table->integer('sort')->unsigned();
            $table->string('type');
            $table->unsignedTinyInteger('no_show_home')->nullable();
            $table->timestamps();

            $table->index(['date_from', 'date_to']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
