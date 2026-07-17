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
        Schema::table('visits', function (Blueprint $table) {
            $table->string('region_code', 10)->nullable()->after('country_code')->comment('国家内部行政区划代码，如中国省份代码 CN-11');

            $table->index(['country_code', 'region_code', 'visited_at'], 'idx_country_region_visited');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex('idx_country_region_visited');
            $table->dropColumn('region_code');
        });
    }
};
