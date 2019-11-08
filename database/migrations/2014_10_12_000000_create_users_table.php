<?php

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
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->enum('sex', ['男', '女'])->nullable();
            $table->string('logo',1000)->nullable()->comment('Logo');
            $table->string('campus')->comment('校区')->nullable();
            $table->string('phone')->nullable()->comment('电话号码');
            $table->string('id_card')->nullable()->comment('身份证');
            $table->string('openid',50)->unique()->comment('微信openid');
            $table->string('qq')->nullable()->comment('联系qq');
            $table->string('wx_id')->nullable()->comment('联系微信');
            $table->enum('identity', ['学生', '教职工', '校友', '其他'])->nullable()->comment('报名身份');
            $table->integer('height')->nullable()->comment('身高');
            $table->string('birthday')->nullable()->comment('出生年月');
            $table->string('sid')->nullable()->comment('学生学号');
            $table->string('school')->nullable()->comment('学生学院');
            $table->string('pe_class')->nullable()->comment('学生体育课');
            $table->integer('group_id')->nullable()->comment('队伍编号');
            $table->tinyInteger('state')->default(0)->comment('0 未报名 1已经报名未组队 2 正在申请队伍 3 有队伍（队长）4 有队伍（队员）5 未填写信息');
            $table->tinyInteger('verify_code')->default(0)->comment('刷卡状态 0未刷卡 1出发 2正常完成 3未完成');
            $table->timestamp('start_at')->nullable()->comment('开始刷卡时间');
            $table->timestamp('end_at')->nullable()->comment('结束刷卡时间');
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
