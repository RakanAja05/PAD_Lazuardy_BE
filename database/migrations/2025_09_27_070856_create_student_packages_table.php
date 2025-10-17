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
        Schema::create('student_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages');
            $table->foreignId('student_user_id')->constrained('users', 'id');
            $table->foreignId('subject_id')->constrained('subjects')->nullable();
            $table->foreignId('tutor_user_id')->constrained('users', 'id')->nullable();
            $table->integer('remaining_session')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_packages');
    }
};
