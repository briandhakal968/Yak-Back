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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('external_id', 64)->unique();
            $table->string('filename');
            $table->string('original_name')->nullable();
            $table->string('path');
            $table->string('mime_type', 64)->nullable();
            $table->string('folder', 32)->default('images');
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();

            $table->index('folder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
