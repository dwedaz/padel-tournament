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
        Schema::table('games', function (Blueprint $table) {
            $table->integer('match_set')->after('team2_id');
            $table->string('name')->after('match_set'); //   etc
            $table->string('status')->nullable()->after('name'); // tie break, nullable
            $table->enum('who_is_serving', ['team1', 'team2'])->nullable()->after('status');
            
            // Add unique constraint for team1_id, team2_id, and match_set
            $table->unique(['team1_id', 'team2_id', 'match_set'], 'unique_match_set_teams');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropUnique('unique_match_set_teams');
            $table->dropColumn(['match_set', 'name', 'status', 'who_is_serving']);
        });
    }
};
