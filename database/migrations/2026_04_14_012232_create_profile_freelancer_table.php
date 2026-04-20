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
        Schema::create('profile_freelancer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('profile_picture')->nullable();
            $table->text('bio')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->enum('availability', ['available', 'busy', 'not available']);
            $table->json('portfolio_links')->nullable();
            $table->json('skills_summary')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_freelancer');
    }
};
