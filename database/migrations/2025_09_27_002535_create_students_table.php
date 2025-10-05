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

            $table->bigInteger('class_id')->unsigned()->nullable(); 
            $table->bigInteger('major_id')->unsigned()->nullable();
            $table->bigInteger('curriculum_id')->unsigned()->nullable();

            $table->foreign('class_id')->references('id')->on('classes');
            $table->foreign('major_id')->references('id')->on('majors');
            $table->foreign('curriculum_id')->references('id')->on('curriculums');

            $table->string('school')->nullable();
            $table->string('parent')->nullable();
            $table->string('parent_telephone_number', 15)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
