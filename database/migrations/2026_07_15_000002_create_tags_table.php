<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 创建标签表
 * 用于存储博客标签云中的标签
 */
return new class extends Migration
{
    /**
     * 运行迁移
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();                      // 主键
            $table->string('name');            // 标签名称
            $table->string('slug')->unique();  // URL 友好标识
            $table->string('color')->nullable(); // 标签颜色
            $table->timestamps();              // 创建与更新时间
        });
    }

    /**
     * 回滚迁移
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
};
