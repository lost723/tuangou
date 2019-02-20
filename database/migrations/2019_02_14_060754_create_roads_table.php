<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parentid')->comment('上级id');
            $table->char('leveltype')->comment('1:省 2:市 3:区 4:街道');
            $table->string('name')->comment('名称');
//            $table->string('path')->default('')->comment('保存路径,例 1,2,3,4');
            $table->string('province')->comment('省名称');
            $table->string('city')->default('')->comment('市名称');
            $table->string('district')->default('')->comment('区名称');
            $table->char('abbr')->comment('大写首字母');
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
        Schema::dropIfExists('roads');
    }
}
