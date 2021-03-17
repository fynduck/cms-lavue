<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerShow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_show', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('banner_id')->index();
            $table->unsignedInteger('page_id')->index();
            $table->string('type_page')->index();

            $table->foreign('banner_id')->references('id')->on('banners')
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
        Schema::dropIfExists('banner_show');
    }
}
