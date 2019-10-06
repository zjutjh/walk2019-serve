<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeriodRegisterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('period_register', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('begin')->comment('开始报名的时间');
            $table->timestamp('end')->comment('结束报名的时间');
            $table->integer('limit_group_count')->comment('此阶段限制报名的组数');
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
        Schema::dropIfExists('period_register');
    }
}
