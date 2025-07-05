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
        Schema::create('book_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books','id')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users','id')->cascadeOnDelete();
            $table->enum('status',['read','unread'])->default('unread');
            $table->timestamp('added_at')->nullable();
            $table->unique(['user_id', 'book_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_users');
    }
};
