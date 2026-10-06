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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('travel_group', 32);
            $table->string('number_of_people', 32)->nullable();
            $table->string('date_type', 32);
            $table->date('arrival_date')->nullable();
            $table->date('departure_date')->nullable();
            $table->string('travel_month', 32)->nullable();
            $table->string('destination', 128)->nullable();
            $table->string('activity', 64)->nullable();
            $table->json('packages')->nullable();
            $table->string('accommodation', 32);
            $table->string('budget_type', 32)->default('budget');
            $table->unsignedInteger('min_budget')->nullable();
            $table->unsignedInteger('max_budget')->nullable();
            $table->string('full_name');
            $table->string('country', 128);
            $table->string('email');
            $table->string('phone', 64);
            $table->text('message')->nullable();
            $table->string('status', 32)->default('new');
            $table->timestamps();

            $table->index('status');
            $table->index('activity');
            $table->index('destination');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
