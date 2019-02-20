<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('商户名称');
            $table->string('phone')->comment('联系电话');
            $table->string('manager')->comment('联系人');
            $table->string('address')->comment('联系地址');
            $table->tinyInteger('level')->default(1)->comment('等级');
            $table->boolean('status')->default('1')->comment("状态{ 1: '未提交认证信息', 2: '认证审核中', 3: '认证未通', 6: '冻结', 9: '正常' }");
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
        Schema::dropIfExists('businesses');
    }
}
