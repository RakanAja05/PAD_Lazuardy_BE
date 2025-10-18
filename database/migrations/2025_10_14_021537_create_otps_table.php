<?php

use App\Enums\OtpTypeEnum;
use App\Enums\VerificationTypeEnum;
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
        $verificationTypes = VerificationTypeEnum::list();
        $otpTypes = OtpTypeEnum::list();

        Schema::create('otps', function (Blueprint $table) use ($verificationTypes, $otpTypes) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('identifier')->index(); 
            $table->enum('identifier_type', $otpTypes);
            $table->string('code', 60); 
            $table->enum('verification_type', $verificationTypes); 
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
