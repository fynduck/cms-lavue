<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormSentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'form_sents',
            function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('form_id')->nullable();
                $table->longText('form_data');
                $table->timestamps();

                $table->foreign('form_id')->references('id')->on('forms')
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
        Schema::dropIfExists('form_sents');
    }
}
