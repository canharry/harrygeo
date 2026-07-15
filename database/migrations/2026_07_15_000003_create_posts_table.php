<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 创建文章表
 * 存储博客首页展示的所有文章卡片数据
 */
return new class extends Migration
{
    /**
     * 运行迁移
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();                            // 主键
            $table->foreignId('category_id')         // 所属分类
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('user_id')             // 作者
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('title');                 // 文章标题
            $table->string('slug')->unique();        // URL 友好标识
            $table->text('summary')->nullable();     // 文章摘要
            $table->longText('content')->nullable(); // 文章内容
            $table->string('cover_image')->nullable(); // 封面图地址
            $table->unsignedInteger('views')->default(0);  // 浏览量
            $table->unsignedInteger('likes')->default(0);  // 点赞数
            $table->boolean('is_published')->default(true); // 是否发布
            $table->timestamp('published_at')->nullable();  // 发布时间
            $table->timestamps();                    // 创建与更新时间
        });
    }

    /**
     * 回滚迁移
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
