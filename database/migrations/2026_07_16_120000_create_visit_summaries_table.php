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
        // 访问汇总表：按天预聚合浏览量、文章阅读量和点赞量，便于仪表盘快速查询
        Schema::create('visit_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('summary_date')->unique()->comment('汇总日期');
            $table->unsignedInteger('page_views')->default(0)->comment('当日总浏览量');
            $table->unsignedInteger('post_reads')->default(0)->comment('当日文章阅读总量');
            $table->unsignedInteger('likes_count')->default(0)->comment('当日点赞总量');
            $table->unsignedInteger('unique_visitors')->default(0)->comment('当日独立访客数（按 IP 去重）');
            $table->timestamps();

            $table->index('summary_date', 'idx_summary_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visit_summaries');
    }
};
