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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->string('section_id'); // ID used in the SVG (e.g., "L3", "G", etc.)
            $table->string('name');
            $table->integer('capacity');
            $table->integer('available_seats');
            $table->decimal('price', 10, 2);
            $table->enum('section_type', ['Standard', 'VIP', 'Premium'])->default('Standard');
            $table->string('view_360_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure section_id is unique per match
            $table->unique(['match_id', 'section_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
