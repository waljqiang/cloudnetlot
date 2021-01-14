<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceClientsNumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("device_clients_nums", function (Blueprint $table) {
            $table->increments("id");
            $table->string("mac",50)->comment("设备mac");
            $table->integer("onlines")->comment("在线终端数");
            $table->tinyInteger("status")->default(1)->comment("统计处理状态,1:未处理,2:锁定,3:处理失败,4:处理成功");
            $table->tinyInteger("retry")->default(0)->comment("尝试次数");
            $table->unsignedBigInteger("created_at")->nullable()->comment("创建时间");
            $table->unsignedBigInteger("updated_at")->nullable()->comment("修改时间");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("device_clients_nums");
    }
}
