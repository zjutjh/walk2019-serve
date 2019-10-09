<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_time', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('begin')->comment('开始报名时间');
            $table->timestamp('end')->comment('结束报名时间');
            $table->integer('capacity')->comment('限制报名的队伍数(累计算法)');
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
        Schema::dropIfExists('register_time');
    }
}
