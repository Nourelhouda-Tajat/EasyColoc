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
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coloc_id')->constrained('colocations')->onDelete('cascade');
            $table->foreignId('debtor_id')->constrained('users')->onDelete('cascade'); // Celui qui devait l'argent
            $table->foreignId('creditor_id')->constrained('users')->onDelete('cascade'); // Celui qui a reçu l'argent
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->enum('status', ['pending', 'paid'])->default('paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
