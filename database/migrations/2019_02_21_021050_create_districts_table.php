<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictsTable extends Migration
{
    /**
     * 区域模版表
     * Run the migrations.
     * todo 增值点。普通用户2个，以至5个，10个
     * @return void
     */
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('orgid')->comment('所属商户');
            $table->string('title')->comment('模版名称');
            $table->unsignedInteger('totles')->comment('小区数量');
            $table->string('note')->comment('备注');
            $table->unsignedTinyInteger('status')->comment('0:禁用， 9:正常');
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
        Schema::dropIfExists('districts');
    }
}
