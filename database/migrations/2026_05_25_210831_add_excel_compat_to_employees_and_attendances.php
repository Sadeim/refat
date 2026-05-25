<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('work_location')->nullable()->after('department');  // مكان العمل
            $table->string('job_nature')->nullable()->after('work_location');   // طبيعة العمل (أمن/نظافة...)
            $table->decimal('hourly_rate', 8, 2)->default(0)->after('allowances'); // سعر ساعة العمل
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->string('period')->nullable()->after('check_out');          // صباحية / مسائية / ليلية
            $table->string('work_location')->nullable()->after('period');      // مكان العمل
            $table->decimal('hourly_rate', 8, 2)->default(0)->after('hours');  // سعر الساعة وقت الحركة
            $table->decimal('daily_total', 12, 2)->default(0)->after('hourly_rate'); // الإجمالي اليومي
            $table->text('supervisor_notes')->nullable()->after('notes');      // ملاحظات المشرف
            $table->foreignId('supervisor_id')->nullable()->after('supervisor_notes')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['period', 'work_location', 'hourly_rate', 'daily_total', 'supervisor_notes', 'supervisor_id']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['work_location', 'job_nature', 'hourly_rate']);
        });
    }
};
