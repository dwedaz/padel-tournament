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
            // Drop the foreign key constraint first
            $table->dropForeign(['winner_id']);
            // Drop the winner_id column
            $table->dropColumn('winner_id');
            // Add the new winner enum column
            $table->enum('winner', ['team1', 'team2'])->nullable()->after('team2_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Drop the winner enum column
            $table->dropColumn('winner');
            // Add back the winner_id column with foreign key
            $table->foreignId('winner_id')->nullable()->constrained('teams')->after('team2_score');
        });
    }
};
