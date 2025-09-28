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
            $table->foreignId('class_id')->constrained('classes')->nullable();
            $table->foreignId('major_id')->constrained('majors')->nullable();
            $table->foreignId('curriculum_id')->constrained('curriculums')->nullable();
            $table->string('school')->nullable();
            $table->string('parent')->nullable();
            $table->string('parent_telephone_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
