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
            // Rename 'win' column to 'winner_id'
            $table->renameColumn('win', 'winner_id');
            // Drop 'lose' column
            $table->dropColumn('lose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Rename back 'winner_id' to 'win'
            $table->renameColumn('winner_id', 'win');
            // Add back 'lose' column
            $table->integer('lose')->nullable();
        });
    }
};
