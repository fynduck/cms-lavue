<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_trans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('banner_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('lang_id');
            $table->boolean('active');

            $table->index(['banner_id', 'lang_id']);

            $table->foreign('banner_id')->references('id')->on('banners')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('lang_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banner_trans');
    }
}
