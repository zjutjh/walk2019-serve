<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrizePool extends Migration
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
            $table->string('title')->comment('标题');
            $table->string('captain')->comment('抽奖标签');
            $table->string('content')->comment('中奖内容');
            $table->integer('capacity')->default(0)->comment('中奖最大数量');
            $table->integer('count')->default(0)->comment('已经抽中的数量');
            $table->integer('accept_count')->default(0)->comment('已领奖的数量');
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
