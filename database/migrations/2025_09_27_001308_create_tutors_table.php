<?php

use App\Enums\CourseLocation;
use App\Enums\Rank;
use App\Enums\TutorStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        // Membuat data enum
        $statusses = array_column(TutorStatus::cases(), 'value');
        $ranks = array_column(Rank::cases(), 'value');
        $courseLocation = array_column(CourseLocation::cases(), 'value');

        Schema::create('tutors', function (Blueprint $table) use ($statusses, $ranks, $courseLocation) {
            $table->id();
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
            $table->enum('course_location', $courseLocation)->nullable();
            $table->integer('sanction_amount')->default(0);
            $table->enum('status', $statusses)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutors');
    }
};
