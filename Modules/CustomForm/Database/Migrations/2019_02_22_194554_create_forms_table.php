<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'forms',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('form_name');
                $table->string('action');
                $table->enum('method', ['post', 'get']);
                $table->tinyInteger('file')->nullable();
                $table->string('form_class')->nullable();
                $table->string('form_id')->nullable();
                $table->string('send_emails')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('forms');
    }
}
