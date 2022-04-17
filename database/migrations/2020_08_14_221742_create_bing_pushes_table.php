<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bing_pushes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('site')->comment('要推送的站点');
            $table->string('url')->comment('要推送的Url');
            $table->tinyInteger('status')->default(0)->nullable()->index();
            $table->string('msg')->nullable();
            $table->unsignedInteger('failures')->nullable()->default(0)->comment('失败计数');
            $table->timestamp('push_at')->nullable()->comment('推送时间');
            $table->timestamp('created_at')->nullable()->comment('创建时间');

            $table->unique(['site', 'url']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bing_pushes');
    }
};
