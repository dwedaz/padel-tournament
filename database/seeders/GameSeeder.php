<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Group;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Group A teams
        $groupA = Group::where('name', 'Group A')->first();
        if (!$groupA) {
            $this->command->error('Group A not found. Please run the GroupSeeder and TeamSeeder first.');
            return;
        }

        $teams = Team::where('group_id', $groupA->id)->get();
        if ($teams->count() < 2) {
            $this->command->error('Not enough teams in Group A to create games.');
            return;
        }

        // Clear existing games for Group A teams
        $existingGamesCount = Game::whereIn('team1_id', $teams->pluck('id'))
            ->orWhereIn('team2_id', $teams->pluck('id'))
            ->count();
        
        if ($existingGamesCount > 0) {
            $this->command->warn("Found {$existingGamesCount} existing games. Clearing them first...");
            Game::whereIn('team1_id', $teams->pluck('id'))
                ->orWhereIn('team2_id', $teams->pluck('id'))
                ->delete();
            $this->command->info('Existing games cleared.');
        }

        $this->command->info('Creating games for Group A teams...');
        $this->command->info('Teams found: ' . $teams->pluck('name')->implode(', '));

        $statuses = ['Completed']; // All games completed
        $servingOptions = ['team1', 'team2', null];
        
        $gameCount = 0;
        $setCount = 0;
        $baseDate = now()->subDays(30); // Start 30 days ago

        // Generate all possible team combinations
        $teamPairIndex = 0;
        for ($i = 0; $i < $teams->count(); $i++) {
            for ($j = $i + 1; $j < $teams->count(); $j++) {
                $team1 = $teams[$i];
                $team2 = $teams[$j];
                
                // Play sets until one team gets 4 wins (best of 7 format)
                $team1Wins = 0;
                $team2Wins = 0;
                $set = 1;
                
                while ($team1Wins < 4 && $team2Wins < 4) {
                    // Tennis/Padel scoring: 0, 15, 30, 40 (40 is always the winning score)
                    $scoreOptions = [0, 15, 30];
                    
                    // Randomly decide who wins this set
                    $winnerTeam = rand(0, 1); // 0 = team1, 1 = team2
                    
                    if ($winnerTeam === 0) {
                        // Team 1 wins with 40
                        $team1Score = 40;
                        $team2Score = $scoreOptions[array_rand($scoreOptions)];
                        $winnerId = $team1->id;
                        $team1Wins++;
                    } else {
                        // Team 2 wins with 40
                        $team1Score = $scoreOptions[array_rand($scoreOptions)];
                        $team2Score = 40;
                        $winnerId = $team2->id;
                        $team2Wins++;
                    }

                    // Proper date ordering: consecutive days for each set
                    $daysFromBase = ($teamPairIndex * 14) + ($set - 1); // 14 days between team pairs to allow for longer matches
                    $gameDate = $baseDate->copy()->addDays($daysFromBase)->addHours(rand(8, 20))->addMinutes(rand(0, 59));

                    $game = Game::create([
                        'name' => 'qualification',
                        'team1_id' => $team1->id,
                        'team2_id' => $team2->id,
                        'team1_score' => $team1Score,
                        'team2_score' => $team2Score,
                        'game_set' => $set, // Each set becomes a separate match
                        'set' => 1, // All games are set 1
                        'status' => 'Completed', // All games completed
                        'who_is_serving' => $servingOptions[array_rand($servingOptions)],
                        'winner_id' => $winnerId,
                        'created_at' => $gameDate,
                        'updated_at' => $gameDate,
                    ]);

                    $gameCount++;
                    
                    $winnerText = $winnerId ? 
                        ($winnerId === $team1->id ? $team1->name : $team2->name) : 
                        'Undecided';
                    
                    $this->command->line(
                        "Game {$gameCount}: {$team1->name} vs {$team2->name} " .
                        "(Set {$set}) - Score: {$team1Score}-{$team2Score} - Winner: {$winnerText} - " .
                        "Series: {$team1Wins}-{$team2Wins} - Date: {$gameDate->format('M d, Y H:i')}"
                    );
                    
                    $set++;
                }
                
                // Show final match result
                $matchWinner = $team1Wins === 4 ? $team1->name : $team2->name;
                $this->command->info("  → MATCH WINNER: {$matchWinner} wins {$team1Wins}-{$team2Wins}");
                $this->command->line('');
                
                $setCount++;
                $teamPairIndex++; // Move to next team pair
            }
        }

        $this->command->info("\nSuccessfully created {$gameCount} games across {$setCount} team matchups!");
        $this->command->info('Each match played until one team reached 4 wins (best-of-7 format).');
    }
}
