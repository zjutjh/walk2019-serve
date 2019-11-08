<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePrizePool extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prize_pool', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('route_id')->comment('抽奖适用的线路');
            $table->string('captain')->comment('抽奖标签');
            $table->string('content')->comment('中奖内容');
            $table->integer('capacity')->comment('中奖最大数量');
            $table->integer('count')->comment('已经抽中的数量');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prize_pool');
    }
}
