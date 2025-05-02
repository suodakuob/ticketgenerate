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
        if (!Schema::hasColumn('tickets', 'seat_number')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('seat_number')->nullable()->after('section_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tickets', 'seat_number')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('seat_number');
            });
        }
    }
};
