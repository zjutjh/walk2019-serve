<?php

use Illuminate\Support\Facades\Schema;
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
            $table->tinyInteger('capacity')->comment('队伍人数');
            $table->text('description')->comment('队伍简介');
            $table->integer('captain_id')->comment('队长id');
            $table->enum('route', ['屏峰小和山半程毅行', '屏峰小和山全程毅行', '朝晖京杭大运河毅行'])->default('屏峰小和山半程毅行')->comment('参加路线');
            $table->boolean('is_lock')->default(false)->comment('是否锁定队伍');
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
