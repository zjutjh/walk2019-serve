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
            $table->string('name')->unique()->comment('路线的名称，选项中显示的就是这个名称');
            $table->integer('capacity')->comment('此条线路限制的队伍数');
            $table->integer('time_id')->comment('报名时间的id');
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
