<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'field_options',
            function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('field_id')->index();
                $table->string('value');
                $table->string('title');
                $table->string('option_class')->nullable();
                $table->string('option_id')->nullable();
                $table->unsignedInteger('priority')->nullable()->default(0);
                $table->timestamps();

                $table->foreign('field_id')->references('id')->on('form_fields')
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
        Schema::dropIfExists('field_options');
    }
}
