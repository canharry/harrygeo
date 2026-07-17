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
        // 访问记录表：用于统计每日浏览量、文章阅读量以及世界地图分布
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->nullable()->comment('访问者 IP 地址');
            $table->string('country_code', 10)->default('CN')->comment('国家或地区代码');
            $table->string('country_name', 100)->default('China')->comment('国家或地区名称');
            $table->string('page_url', 500)->nullable()->comment('访问页面地址');
            $table->foreignId('post_id')->nullable()->constrained()->onDelete('set null')->comment('关联文章 ID，非文章页面为空');
            $table->timestamp('visited_at')->useCurrent()->comment('访问时间');
            $table->timestamps();

            // 常用查询索引
            $table->index(['visited_at', 'post_id'], 'idx_visited_post');
            $table->index(['country_code', 'visited_at'], 'idx_country_visited');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visits');
    }
};
