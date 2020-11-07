<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->char('country_iso', 2);
            $table->char('slug', 3);
            $table->string('name');
            $table->string('image')->nullable();
            $table->boolean('active')->nullable();
            $table->unsignedTinyInteger('default')->nullable()->index();
            $table->unsignedTinyInteger('sort')->index();
            $table->timestamps();

            $table->index(['slug', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
}
