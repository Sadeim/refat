<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wage_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month'); // 1-12
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->unsignedSmallInteger('work_days')->default(0);
            $table->unsignedSmallInteger('absence_days')->default(0);
            $table->unsignedSmallInteger('leave_days')->default(0);
            $table->date('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'year', 'month']);
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wage_payments');
    }
};
