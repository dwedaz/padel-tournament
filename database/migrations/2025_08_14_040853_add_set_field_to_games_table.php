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
            // Drop the current unique constraint
            $table->dropUnique('unique_game_set_teams');
            
            // Add the new set field
            $table->integer('set')->after('game_set')->default(1);
            
            // Add the new unique constraint with all four fields
            $table->unique(['team1_id', 'team2_id', 'game_set', 'set'], 'unique_game_set_teams');
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
            
            // Drop the set field
            $table->dropColumn('set');
            
            // Add back the old unique constraint (without set field)
            $table->unique(['team1_id', 'team2_id', 'game_set'], 'unique_game_set_teams');
        });
    }
};
