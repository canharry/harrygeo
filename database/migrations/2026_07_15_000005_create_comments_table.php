<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 创建评论表
 * 存储文章评论及嵌套回复
 */
return new class extends Migration
{
    /**
     * 运行迁移
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();                            // 主键
            $table->foreignId('post_id')             // 所属文章
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('user_id')             // 评论用户
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('parent_id')           // 父评论 ID，支持嵌套
                  ->nullable()
                  ->constrained('comments')
                  ->onDelete('cascade');
            $table->text('content');                 // 评论内容
            $table->timestamps();                    // 创建与更新时间
        });
    }

    /**
     * 回滚迁移
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
