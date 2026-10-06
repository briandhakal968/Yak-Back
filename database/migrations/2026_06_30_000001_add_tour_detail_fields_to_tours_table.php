<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->string('trip_grade', 64)->default('Moderate');
            $table->string('country', 64)->default('Nepal');
            $table->string('activities', 255)->default('Trekking');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['trip_grade', 'country', 'activities']);
        });
    }
};
