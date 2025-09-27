<?php

use App\Enums\FileType;
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
        $fileTypes = array_column(FileType::cases(), 'value');

        Schema::create('files', function (Blueprint $table) use ($fileTypes) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('tutors')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', $fileTypes);
            $table->string('path_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
