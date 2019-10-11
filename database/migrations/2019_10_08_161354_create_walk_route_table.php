<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;


class CreateWalkRouteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('walk_route', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',100)->unique()->comment('路线的名称，选项中显示的就是这个名称');
            $table->string('type',100)->unique()->comment('路线的名称,显示在编号');
            $table->integer('capacity')->comment('capacity');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('walk_route');
    }
}
