<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('distid')->comment('区域模版ID');
            $table->unsignedBigInteger('commid')->comment('小区id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('district_items');
    }
}
