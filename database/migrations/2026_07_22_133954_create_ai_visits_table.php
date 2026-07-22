<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->nullable()->constrained()->onDelete('set null')->comment('关联文章 ID');
            $table->string('ai_name', 50)->comment('AI 平台名称');
            $table->string('ip_address', 45)->nullable()->comment('访问者 IP 地址');
            $table->text('user_agent')->nullable()->comment('User-Agent');
            $table->string('page_url', 500)->nullable()->comment('访问页面地址');
            $table->timestamp('visited_at')->useCurrent()->comment('访问时间');
            $table->timestamps();

            $table->index(['ai_name', 'visited_at'], 'idx_ai_visited');
            $table->index(['post_id', 'visited_at'], 'idx_post_visited');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_visits');
    }
};
