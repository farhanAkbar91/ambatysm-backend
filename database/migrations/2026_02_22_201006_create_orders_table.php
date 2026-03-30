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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['pending', 'waiting_confirmation', 'paid', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->enum('type', ['regular', 'custom'])->default('regular'); // Membedakan pesanan reguler dan custom
            $table->text('custom_notes')->nullable(); // Catatan untuk custom order
            $table->string('payment_method')->nullable();
            $table->string('payment_proof')->nullable(); // Gambar bukti transfer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
