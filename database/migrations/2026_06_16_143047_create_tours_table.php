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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('external_id', 64)->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('overview');
            $table->json('highlights');
            $table->json('itinerary');
            $table->json('inclusions');
            $table->json('exclusions');
            $table->json('gallery');
            $table->string('video')->nullable();
            $table->json('faqs');
            $table->json('route_info');
            $table->unsignedTinyInteger('duration_value');
            $table->string('duration_unit', 32);
            $table->json('departure_info');
            $table->unsignedInteger('price_amount');
            $table->string('price_currency', 8);
            $table->string('price_label')->nullable();
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->string('max_altitude', 32);
            $table->string('best_season', 64);
            $table->string('group_size', 64);
            $table->string('region', 64)->nullable();
            $table->boolean('is_popular')->default(false);
            $table->string('status', 16)->default('active');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('region');
            $table->index('is_popular');
            $table->index('status');
            $table->index('price_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
