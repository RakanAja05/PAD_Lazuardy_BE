<?php

use App\Enums\Status;
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
        $statuses = array_column(Status::cases(), 'value'); 
        Schema::create('taken_schedules', function (Blueprint $table) use ($statuses) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('schedule_tutor_id')->constrained('schedule_tutors');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->date('date');
            $table->enum('status', $statuses);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taken_schedules');
    }
};
