<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'form_fields',
            function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('form_id')->index();
                $table->enum('type', ['text', 'number', 'email', 'checkbox', 'radio', 'select', 'range', 'textarea', 'file']);
                $table->string('block_class')->nullable();
                $table->string('name');
                $table->string('label')->nullable();
                $table->string('placeholder')->nullable();
                $table->string('field_class')->nullable();
                $table->string('field_id')->nullable();
                $table->string('validate')->nullable();
                $table->unsignedInteger('priority')->nullable()->default(0);
                $table->timestamps();

                $table->foreign('form_id')->references('id')->on('forms')
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
        Schema::dropIfExists('form_fields');
    }
}
