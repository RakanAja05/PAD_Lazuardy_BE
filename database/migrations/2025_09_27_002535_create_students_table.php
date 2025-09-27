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

        Schema::create('students', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('major_id')->constrained('majors');
            $table->foreignId('curriculum_id')->constrained('curriculums');
            $table->string('school');
            $table->string('parent');
            $table->string('parent_telephone_number');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
