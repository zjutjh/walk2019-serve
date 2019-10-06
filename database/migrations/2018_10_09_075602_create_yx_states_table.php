<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYxStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yx_states', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('state')->default(0)->comment('0 开启报名 1 关闭报名');
            $table->integer('max_team_num')->default(1200)->comment('成功报名组数');
            $table->timestamp('finish_time')->nullable()->comment('结束报名时间');
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
        Schema::dropIfExists('yx_states');
    }
}
