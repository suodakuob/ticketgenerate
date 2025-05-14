<?php // Make sure this is the very first line

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
        Schema::create('football_matches', function (Blueprint $table) {
            $table->id(); // Creates an auto-incrementing primary key 'id'
            $table->string('home_team');
            $table->string('away_team');
            $table->dateTime('match_time'); // Or just date/time depending on your needs
            $table->decimal('ticket_price', 8, 2); // Price with up to 8 total digits, 2 after decimal
            // Add other columns your match might need (e.g., stadium_id, status)
            $table->timestamps(); // Creates 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_matches');
    }
};