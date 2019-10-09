<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalkPathTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('walk_path', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('路线的名称，选项中显示的就是这个名称');
            $table->string('limit_campus')->comment('此条线路限制的校区，all表示没有限制，其他以校区名来进行限制');
            $table->string('campus_from')->comment('出发校区');
            $table->integer('capacity')->comment('此条线路限制的队伍数，注意，此字段的限制优先于walk_time的相应配置');
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
        Schema::dropIfExists('walk_path');
    }
}
