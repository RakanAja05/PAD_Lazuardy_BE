<?php

use App\Enums\PaymentMethod;
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
        $payment_methods = array_column(PaymentMethod::cases(), 'value');
        Schema::create('payments', function (Blueprint $table) use ($payment_methods) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->integer('total_amount')->nullable();
            $table->string('proof_image_url')->nullable();
            $table->date('date')->nullable();
            $table->enum('payment_method', $payment_methods)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
