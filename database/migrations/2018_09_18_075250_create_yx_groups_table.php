<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYxGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yx_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('队伍名称');
            $table->tinyInteger('num')->comment('队伍人数');
            $table->enum('start_campus', ['屏峰', '朝晖'])->comment('出发校区');
            $table->text('description')->comment('队伍简介');
            $table->integer('captain_id')->comment('队长id');
            $table->enum('select_route', ['屏峰小和山半程毅行', '屏峰小和山全程毅行', '朝晖京杭大运河毅行'])->default('屏峰小和山半程毅行')->comment('参加路线');
            $table->boolean('is_lock')->default(false)->comment('是否锁定队伍');
            $table->timestamp('up_to_standard')->nullable()->default(null)->comment('达到参加要求的时间');
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
        Schema::dropIfExists('yx_groups');
    }
}
