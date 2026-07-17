<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 为评论表增加已读状态字段
 */
return new class extends Migration
{
    /**
     * 运行迁移
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('user_agent')->comment('是否已读');
            $table->index('is_read');
        });
    }

    /**
     * 回滚迁移
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
};
