<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->string('route_map_image')->nullable()->after('gallery');
            $table->string('altitude_chart_image')->nullable()->after('route_map_image');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['route_map_image', 'altitude_chart_image']);
        });
    }
};
