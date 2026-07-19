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
        Schema::create('friendship_links', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('链接名称');
            $table->string('url')->comment('链接地址');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序，数字越小越靠前');
            $table->boolean('is_show')->default(true)->comment('是否展示');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendship_links');
    }
};
