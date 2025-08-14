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
            // Drop the old unique constraint
            $table->dropUnique('unique_match_set_teams');
            
            // Rename the column
            $table->renameColumn('match_set', 'game_set');
            
            // Add the new unique constraint
            $table->unique(['team1_id', 'team2_id', 'game_set'], 'unique_game_set_teams');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('unique_game_set_teams');
            
            // Rename the column back
            $table->renameColumn('game_set', 'match_set');
            
            // Add the old unique constraint back
            $table->unique(['team1_id', 'team2_id', 'match_set'], 'unique_match_set_teams');
        });
    }
};
