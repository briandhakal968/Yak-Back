<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('exercise', 32)->nullable()->after('accommodation');
            $table->dropColumn(['budget_type', 'min_budget', 'max_budget']);
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('budget_type', 32)->default('budget');
            $table->unsignedInteger('min_budget')->nullable();
            $table->unsignedInteger('max_budget')->nullable();
            $table->dropColumn('exercise');
        });
    }
};