<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Menu\Entities\Menu;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->enum('position', array_keys(Menu::positions()))->index();
            $table->string('type_page')->index();
            $table->unsignedInteger('page_id')->index();
            $table->string('attributes')->nullable();
            $table->string('target');
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedTinyInteger('sort');
            $table->unsignedTinyInteger('nofollow');
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('menus')
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
        Schema::dropIfExists('menus');
    }
}
