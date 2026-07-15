<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 创建文章分类表
 * 用于存储博客左侧边栏展示的个人分类
 */
return new class extends Migration
{
    /**
     * 运行迁移
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                         // 主键
            $table->string('name');               // 分类名称
            $table->string('slug')->unique();     // URL 友好标识
            $table->string('description')->nullable(); // 分类描述
            $table->string('icon')->nullable();   // 图标类名
            $table->string('color')->nullable();  // 颜色标识
            $table->integer('sort_order')->default(0); // 排序权重
            $table->boolean('is_show')->default(true); // 是否显示
            $table->timestamps();                 // 创建与更新时间
        });
    }

    /**
     * 回滚迁移
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
