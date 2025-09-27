<?php

use App\Enums\Rank;
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        // Membuat data enum
        $statuses = array_column(Status::cases(), 'value');
        $ranks = array_column(Rank::cases(), 'value');

        Schema::create('tutors', function (Blueprint $table) use ($statuses, $ranks) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('education');
            $table->integer('salary');
            $table->integer('price');
            $table->longText('description');
            $table->longText('learning_method');
            $table->json('qualification');
            $table->enum('status', $statuses);
            $table->enum('rank', $ranks);
            $table->integer('sanction_amount')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutors');
    }
};
