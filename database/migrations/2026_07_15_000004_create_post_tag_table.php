<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 创建文章与标签的多对多关联表
 */
return new class extends Migration
{
    /**
     * 运行迁移
     */
    public function up()
    {
        Schema::create('post_tag', function (Blueprint $table) {
            $table->id();                       // 主键
            $table->foreignId('post_id')        // 文章 ID
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('tag_id')         // 标签 ID
                  ->constrained()
                  ->onDelete('cascade');
            $table->timestamps();               // 创建与更新时间

            // 同一篇文章不能重复绑定同一个标签
            $table->unique(['post_id', 'tag_id']);
        });
    }

    /**
     * 回滚迁移
     */
    public function down()
    {
        Schema::dropIfExists('post_tag');
    }
};
