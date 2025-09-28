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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('session')->nullable();
            $table->integer('price')->nullable();
            $table->decimal('discount', 5, 2)->nullable();
            $table->longText('description')->nullable();
            $table->json('benefit')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('subject_amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
