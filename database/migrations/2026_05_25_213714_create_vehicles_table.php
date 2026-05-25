<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();        // رقم اللوحة
            $table->string('model')->nullable();              // الموديل (سنتافيه...)
            $table->string('make')->nullable();               // الشركة (هيونداي...)
            $table->unsignedSmallInteger('year')->nullable(); // سنة الصنع
            $table->string('color')->nullable();              // اللون
            $table->string('vin')->nullable();                // رقم الشاسيه
            $table->decimal('current_odometer', 12, 2)->default(0); // عداد حالي
            $table->foreignId('default_driver_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('insurance_expiry')->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('status')->default('active');     // active | maintenance | retired
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
