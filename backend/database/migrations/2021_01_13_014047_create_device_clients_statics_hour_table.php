<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceClientsStaticsHourTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("device_clients_statics_hour", function (Blueprint $table) {
            $table->increments("id");
            $table->string("mac",50)->comment("设备mac");
            $table->integer("onlines")->comment("在线终端数");
            $table->dateTime("hours")->comment("时间");
            $table->unsignedBigInteger("created_at")->nullable()->comment("创建时间");
            $table->unsignedBigInteger("updated_at")->nullable()->comment("修改时间");
            $table->unique(["mac","hours"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("device_clients_statics_hour");
    }
}
