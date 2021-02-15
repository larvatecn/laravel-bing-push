<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBingPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bing_push', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url')->unique();
            $table->tinyInteger('status')->default(0)->nullable()->index();
            $table->string('msg')->nullable();
            $table->unsignedInteger('failures')->nullable()->default(0)->comment('失败计数');
            $table->timestamp('push_at', 0)->nullable()->comment('推送时间');
            $table->timestamp('created_at', 0)->nullable()->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bing_push');
    }
}
