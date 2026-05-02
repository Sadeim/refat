<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custodies', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->string('asset_name');
            $table->string('asset_type');
            $table->string('serial_no')->nullable();
            $table->decimal('value', 12, 2)->default(0);
            $table->string('assigned_to_type');
            $table->unsignedBigInteger('assigned_to_id');
            $table->date('delivered_at')->nullable();
            $table->date('returned_at')->nullable();
            $table->string('condition_on_delivery')->nullable();
            $table->string('condition_on_return')->nullable();
            $table->string('status')->default('delivered');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['assigned_to_type', 'assigned_to_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custodies');
    }
};
