<?php

use App\Enums\Day;
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
        $days = array_column(Day::cases(), 'value');

        Schema::create('schedule_tutors', function (Blueprint $table) use ($days) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('day', $days);
            $table->time('time');

            $table->unique(['user_id', 'day', 'time']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_tutors');
    }
};
