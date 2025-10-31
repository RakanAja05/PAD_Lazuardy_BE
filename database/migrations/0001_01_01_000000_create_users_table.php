<?php

use App\Enums\Gender;
use App\Enums\GenderEnum;
use App\Enums\Religion;
use App\Enums\ReligionEnum;
use App\Enums\Role;
use App\Enums\RoleEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        // membuat data enum
        $roles = RoleEnum::list();
        $genders = GenderEnum::list();
        $religions = ReligionEnum::list();

        Schema::create('users', function (Blueprint $table) use ($roles, $genders, $religions) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->enum('role', $roles)->nullable();
            $table->string('telephone_number', 15)->nullable();
            $table->timestamp('telephone_verified_at')->nullable();
            $table->string('profile_photo_url')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', $genders)->nullable();
            $table->enum('religion', $religions)->nullable();
            $table->json('home_address')->nullable();
            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
