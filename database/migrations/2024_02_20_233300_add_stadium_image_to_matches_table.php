<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->string('stadium_image')->nullable()->after('stadium');
            $table->enum('ticket_type', ['Standard', 'VIP', 'Premium'])->default('Standard')->change();
        });
    }

    public function down()
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn('stadium_image');
            $table->string('ticket_type')->change();
        });
    }
};
