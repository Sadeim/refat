<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_representatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('name');                     // اسم الممثل
            $table->string('position')->nullable();      // المنصب / المسمى الوظيفي
            $table->string('phone')->nullable();
            $table->string('phone_alt')->nullable();     // هاتف بديل
            $table->string('email')->nullable();
            $table->string('national_id')->nullable();   // رقم الهوية
            $table->string('whatsapp')->nullable();
            $table->boolean('is_primary')->default(false); // الممثل الرئيسي
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_representatives');
    }
};
