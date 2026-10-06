<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Sub-category for parents that group several child types
            // (e.g. category = "tibet" + sub_category = "tibet-trip" | "tibet-mountain-biking").
            $table->string('sub_category', 64)->nullable()->after('category');
            $table->index('sub_category');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropIndex(['sub_category']);
            $table->dropColumn('sub_category');
        });
    }
};
