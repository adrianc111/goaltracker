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
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key for user
            $table->string('name'); // Name of the habit
            $table->integer('frequency')->nullable(); // Frequency (e.g., how many times per week)
            $table->integer('order')->default(0); // Order for sorting habits
            $table->enum('group', ['morning', 'day', 'evening', 'uncategorised'])->default('day'); // Group for morning, day, or evening
            $table->timestamp('archived')->nullable(); // Timestamp for when the habit is archived
            $table->date('start_date')->nullable(); // Start date for the habit
            $table->date('end_date')->nullable(); // End date for the habit
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
