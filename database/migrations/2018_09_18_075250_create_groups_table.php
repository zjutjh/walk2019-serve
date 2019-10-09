<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('队伍名称');
            $table->string('logo',1000)->nullable()->comment('队伍Logo');
            $table->tinyInteger('capacity')->comment('队伍人数');
            $table->text('description')->comment('队伍简介');
            $table->integer('captain_id')->comment('队长id');
            $table->integer('route_id')->comment('毅行路线的id');
            $table->integer('walk_time_id')->comment('出发时间的id');
            $table->boolean('is_submit')->default(false)->comment('是否提交队伍');
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
        Schema::dropIfExists('groups');
    }
}
