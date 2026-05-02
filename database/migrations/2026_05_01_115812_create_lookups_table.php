<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lookups', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60);
            $table->string('key', 80);
            $table->string('label_ar');
            $table->string('label_en')->nullable();
            $table->string('color', 30)->nullable();
            $table->string('icon', 80)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['type', 'key']);
            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lookups');
    }
};
