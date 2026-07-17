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
        // 文章点赞记录表：用于统计每日点赞量
        Schema::create('post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade')->comment('关联文章 ID');
            $table->string('ip_address', 45)->nullable()->comment('点赞者 IP 地址');
            $table->timestamp('liked_at')->useCurrent()->comment('点赞时间');
            $table->timestamps();

            $table->index(['liked_at', 'post_id'], 'idx_liked_post');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_likes');
    }
};
