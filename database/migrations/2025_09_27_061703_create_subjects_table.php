<?php

use App\Enums\SubjectEnum;
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
        $subjects = SubjectEnum::list();

        Schema::create('subjects', function (Blueprint $table) use ($subjects) {
            $table->id();
            $table->enum('name', $subjects);
            $table->foreignId('curriculum_id')->constrained('curriculums');
            $table->foreignId('class_id')->constrained('classes');
            $table->string('icon_image_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
