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
        Schema::create('otps', function (Blueprint $table) {
            $table->id(); // کلید اصلی
            $table->string('mobile'); // شماره موبایل
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // شناسه کاربر (اختیاری)
            $table->string('otp', 6); // کد OTP (6 رقم)
            $table->timestamp('expire_at'); // زمان انقضا
            $table->timestamps(); // زمان ایجاد و به‌روزرسانی
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
