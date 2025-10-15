<?php

use App\Enums\OtpStatus;
use App\Enums\OtpType;
use App\Enums\VerificationType;
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
        $verificationTypes = array_column(VerificationType::cases(), 'value');
        $otpTypes = array_column(OtpType::cases(), 'value');

        Schema::create('otps', function (Blueprint $table) use ($verificationTypes, $otpTypes) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('identifier')->index(); // Identifier seperti email atau nomor telepon yang mau dicek
            $table->enum('identifier_type', $otpTypes); // contoh: email, nomor telepon
            $table->string('code', 60); // Kode OTP yang di-hash
            $table->enum('verification_type', $verificationTypes); // contoh: registrasi, reset_password
            $table->integer('attempts')->default(0);
            $table->boolean('is_used')->default(false);
            $table->timestamp('expired_at');
            $table->timestamps();
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
