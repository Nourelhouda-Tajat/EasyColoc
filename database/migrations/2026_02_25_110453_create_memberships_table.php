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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->enum('role',['owner', 'member'])->default('member');
            $table->dateTime('joined_at')->useCurrent();
            $table->dateTime('left_at')->nullable();
            $table->timestamps();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('coloc_id')->constrained('colocations')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
