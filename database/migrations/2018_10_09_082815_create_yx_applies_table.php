<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYxAppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yx_applies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('apply_id')->comment('申请者id');
            $table->integer('apply_team_id')->comment('申请队伍id');
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
        Schema::dropIfExists('yx_applies');
    }
}
