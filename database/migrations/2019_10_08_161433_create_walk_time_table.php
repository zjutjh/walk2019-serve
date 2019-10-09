<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalkTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('walk_time', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('begin')->comment('出发时间');
            $table->timestamp('end')->comment('结束出发时间');
            $table->string('capacity_array')->comment('每个路线的队伍数限制');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('walk_time');
    }
}
