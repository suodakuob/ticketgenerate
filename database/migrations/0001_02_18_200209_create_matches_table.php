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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); 
            $table->string('home_team');
            $table->string('away_team');
            $table->dateTime('match_date');
            $table->string('stadium');
            $table->decimal('ticket_price', 10, 2);
            $table->string('ticket_type')->default('standard'); 
            $table->integer('available_tickets');
            $table->text('description')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
