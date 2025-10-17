<?php

use App\Enums\Status;
use App\Enums\TutorStatus;
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
        $statuses = TutorStatus::list();
        
        Schema::create('tutor_confirms', function (Blueprint $table) use ($statuses) 
        {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users', 'id')->onDelete('cascade');
            $table->foreignId('tutor_user_id')->constrained('users', 'id')->onDelete('cascade');
            $table->string('reason')->nullable();
            $table->enum('status', $statuses)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_confirms');
    }
};
