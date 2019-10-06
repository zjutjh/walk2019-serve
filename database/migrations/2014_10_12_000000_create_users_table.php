<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid')->unique()->comment('微信openid');
            $table->string('uid')->nullable()->comment('学生学号');
            $table->integer('group_id')->nullable()->comment('队伍编号');
            $table->enum('identity', ['学生', '教职工', '校友', '其他'])->nullable()->comment('报名身份');
            $table->tinyInteger('state')->default(0)->comment('0 未报名 1已经报名未组队 2 正在申请队伍 3 有队伍（队长）4 有队伍（队员）5 未填写信息');
            $table->string('campus')->nullable()->comment('校区');

            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->enum('sex', ['男', '女'])->nullable();
            $table->enum('campus', ['屏峰', '朝晖'])->comment('校区');
            $table->string('phone')->nullable()->comment('电话号码');
            $table->string('id_card')->nullable()->comment('身份证');
            $table->string('qq_id')->nullable()->comment('联系qq');
            $table->string('wx_id')->nullable()->comment('联系微信');
            $table->integer('height')->nullable()->comment('身高');
            $table->string('birthday')->nullable()->comment('出生年月');
            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
