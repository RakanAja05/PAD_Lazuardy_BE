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
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taken_schedule_id')->constrained('taken_schedules');
            $table->foreignId('tutor_id')->constrained('tutors');
            $table->foreignId('student_id')->constrained('students');
            $table->longText('evaluation')->nullable();
            $table->integer('report')->nullable();
            $table->string('pbm_image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
