<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('leaders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customerid')->comment('团长关联消费用户id');
            $table->unsignedInteger('commid')->comment('团长关联小区id');
            $table->string('leaderno')->comment('团长编号');
            # 小区名称
            $table->string('commtitle')->comment('小区名称');
            # 团长别名
            $table->string('alias')->default('')->comment('团长别名');
            $table->string('name', 32)->comment('团长真实姓名');
            $table->string('mobile',20)->comment('团长手机号码');
            $table->string('idcard',20)->comment('团长身份证件号码');
            $table->string('idcard_front_url')->comment('身份证件正面照信息');
            $table->string('idcard_back_url')->comment('身份证件反面照信息');
            $table->string('address', 50)->comment('团长真实地址');
            $table->decimal('commission',10,2)->default(0)->comment('团长佣金');
            $table->char('status')->comment('团长审核状态 0:团长冻结 1:审核拒绝  2:审核中 3:审核通过');
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
        //
        Schema::dropIfExists('leaders');
    }
}
