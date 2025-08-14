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
        // First, update existing data to match enum values
        \DB::table('games')->where('name', 'Qualification')->update(['name' => 'qualification']);
        \DB::table('games')->where('name', 'Semi Final')->update(['name' => 'semifinal']);
        \DB::table('games')->where('name', 'Final')->update(['name' => 'final']);
        
        Schema::table('games', function (Blueprint $table) {
            // Change the name field to enum
            $table->enum('name', ['qualification', 'quarterfinal', 'semifinal', 'final'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Change back to string
            $table->string('name')->change();
        });
    }
};
