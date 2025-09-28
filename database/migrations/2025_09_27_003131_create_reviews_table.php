<?php

use App\Enums\RatingOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Membuat enum
        $options = array_column(RatingOption::cases(), 'value');

        Schema::create('reviews', function (Blueprint $table) use ($options) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('tutors');
            $table->foreignId('student_id')->constrained('students');
            $table->decimal('rate', 4, 2);
            $table->enum('quality', $options)->nullable();
            $table->enum('delivery', $options)->nullable();
            $table->enum('attitude', $options)->nullable();
            $table->enum('benefit', $options)->nullable();
            $table->string('review')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
