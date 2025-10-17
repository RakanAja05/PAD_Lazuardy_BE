<?php

use App\Enums\CourseMode;
use App\Enums\Rank;
use App\Enums\TutorStatus;
use App\Models\Tutor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        // Membuat data enum
        $statusses = TutorStatus::list();
        $ranks = Rank::list();
        $courseMode = CourseMode::list();

        Schema::create('tutors', function (Blueprint $table) use ($statusses, $ranks, $courseMode) {
            $table->foreignId('user_id')->constrained('users');
            $table->json('education')->nullable();
            $table->integer('salary')->default(0);
            $table->integer('price')->default(0);
            $table->longText('description')->nullable();
            $table->longText('learning_method')->nullable();
            $table->json('qualification')->nullable();
            $table->longText('experience')->nullable();
            $table->json('organization')->nullable();
            $table->enum('rank', $ranks)->nullable();
            $table->enum('course_mode', $courseMode)->nullable();
            $table->integer('sanction_amount')->default(0);
            $table->enum('status', $statusses)->nullable();
            $table->timestamps();

            $table->primary('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutors');
    }
};
