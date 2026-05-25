<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('driver_name')->nullable(); // إذا السائق ليس موظف مسجَّل (مثل "أبو الحسن")
            $table->date('trip_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('odometer_start', 12, 2)->nullable();
            $table->decimal('odometer_end', 12, 2)->nullable();
            $table->decimal('distance_km', 10, 2)->default(0); // محسوب تلقائياً
            $table->text('purpose')->nullable();         // طبيعة المهمة
            $table->string('destination')->nullable();   // الوجهة
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete(); // ربط بعميل (اختياري)
            $table->decimal('fuel_liters', 8, 2)->nullable();
            $table->decimal('fuel_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['vehicle_id', 'trip_date']);
            $table->index('driver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_trips');
    }
};
