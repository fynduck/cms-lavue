<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'group_permissions',
            function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('group_id');
                $table->unsignedInteger('permission_id');
                $table->timestamps();

                $table->foreign('group_id')->references('id')->on('user_groups')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('permissions')
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
        Schema::dropIfExists('group_permissions');
    }
}
