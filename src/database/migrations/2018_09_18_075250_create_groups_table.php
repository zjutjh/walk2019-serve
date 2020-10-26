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
            $table->string('No')->comment('编号')->nullable();
            $table->string('logo',1000)->nullable()->comment('队伍Logo');
            $table->tinyInteger('capacity')->comment('队伍人数');
            $table->text('description')->comment('队伍简介');
            $table->integer('captain_id')->comment('队长id');
            $table->integer('route_id')->comment('毅行路线');
            //applies
            $table->integer('prize_id')->nullable()->comment('获得的奖');
            $table->boolean('prize_get')->default(false)->comment('是否已领奖');

            $table->boolean('is_submit')->default(false)->comment('是否提交队伍');
            $table->boolean('is_super')->default(false)->comment('是否是超级队伍');

            $table->boolean('allow_matching')->default(false)->comment('是否允许匹配');

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
