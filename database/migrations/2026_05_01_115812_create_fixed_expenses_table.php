<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // اسم المصروف (إيجار، إنترنت...)
            $table->string('category', 80)->nullable(); // مرتبط بـ Lookup expense_category
            $table->decimal('amount', 14, 2);
            $table->string('currency', 8)->default('ILS');
            $table->string('frequency', 20)->default('monthly'); // monthly | yearly | weekly
            $table->unsignedTinyInteger('day_of_period')->default(1); // اليوم من الشهر/الأسبوع
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('last_run_at')->nullable();
            $table->date('next_run_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_post')->default(false); // ينشئ Transaction تلقائياً
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'next_run_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_expenses');
    }
};
