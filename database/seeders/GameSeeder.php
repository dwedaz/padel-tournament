<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Group;
use App\Models\Team;
use App\Models\Field;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get only Group A
        $groupA = Group::where('name', 'Group A')->first();
        if (!$groupA) {
            $this->command->error('Group A not found. Please run the GroupSeeder first.');
            return;
        }

        // Get Group A teams (should be Adi, Budi, Citra, Dani)
        $teams = Team::where('group_id', $groupA->id)->orderBy('name')->get();
        if ($teams->count() < 2) {
            $this->command->error('Not enough teams in Group A to create games.');
            return;
        }

        // Get all available fields
        $fields = Field::all();
        if ($fields->isEmpty()) {
            $this->command->error('No fields found. Please run the FieldSeeder first.');
            return;
        }

        // Clear existing games for Group A teams
        Game::whereIn('team1_id', $teams->pluck('id'))
            ->orWhereIn('team2_id', $teams->pluck('id'))
            ->delete();

        $this->command->info("\n=== Creating Games for Group A ===" . ' (Teams: ' . $teams->pluck('name')->implode(', ') . ')');
        
        // Set base date to be in the past (using dates from 30 days ago)
        $baseDate = Carbon::now()->subDays(30)->startOfDay();
        $servingOptions = ['team1', 'team2', null];
        $totalGames = 0;
        $dayOffset = 0;

        // Generate all possible team combinations (round robin)
        // Team names should be: Adi, Budi, Citra, Dani
        $matchups = [];
        
        for ($i = 0; $i < $teams->count(); $i++) {
            for ($j = $i + 1; $j < $teams->count(); $j++) {
                $team1Name = $teams[$i]->name;
                $team2Name = $teams[$j]->name;
                
                // All games will be qualification type only
                $gameType = 'qualification';
                
                // Special case: Adi vs Budi should be tie break
                if (($team1Name === 'Adi' && $team2Name === 'Budi') || 
                    ($team1Name === 'Budi' && $team2Name === 'Adi')) {
                    $matchups[] = [
                        'team1_name' => $team1Name,
                        'team2_name' => $team2Name, 
                        'result_type' => 'tie_break',
                        'game_type' => $gameType,
                        'description' => "Tie Break Match (Qualification)"
                    ];
                } else {
                    $matchups[] = [
                        'team1_name' => $team1Name,
                        'team2_name' => $team2Name,
                        'result_type' => 'four_wins',
                        'game_type' => $gameType, 
                        'description' => "Four Wins Match (Qualification)"
                    ];
                }
            }
        }

        foreach ($matchups as $matchup) {
            $team1 = $teams->firstWhere('name', $matchup['team1_name']);
            $team2 = $teams->firstWhere('name', $matchup['team2_name']);
            
            if (!$team1 || !$team2) {
                $this->command->warn("Teams {$matchup['team1_name']} or {$matchup['team2_name']} not found. Skipping...");
                continue;
            }

            $this->command->info("\n--- {$team1->name} vs {$team2->name} ({$matchup['description']}) ---");
            
            $team1Wins = 0;
            $team2Wins = 0;
            $set = 1;
            
            if ($matchup['result_type'] === 'tie_break') {
                // Tie break: play exactly 6 sets to get 3-3
                while ($set <= 6) {
                    $scoreOptions = [0, 15, 30];
                    
                    // Alternate wins to ensure 3-3 tie
                    $winnerTeam = ($set % 2 === 1) ? 0 : 1;
                    
                    if ($winnerTeam === 0) {
                        $team1Score = 40;
                        $team2Score = $scoreOptions[array_rand($scoreOptions)];
                        $winnerId = $team1->id;
                        $team1Wins++;
                    } else {
                        $team1Score = $scoreOptions[array_rand($scoreOptions)];
                        $team2Score = 40;
                        $winnerId = $team2->id;
                        $team2Wins++;
                    }
                    
                    // Ensure the game date doesn't exceed today
                    $gameDate = $baseDate->copy()->addDays($dayOffset)->addHours(rand(9, 17))->addMinutes(rand(0, 59));
                    if ($gameDate->isAfter(Carbon::now())) {
                        $gameDate = Carbon::now()->subDays(rand(0, 5))->addHours(rand(9, 17))->addMinutes(rand(0, 59));
                    }
                    $randomField = $fields->random();
                    
                    Game::create([
                        'name' => $matchup['game_type'],
                        'team1_id' => $team1->id,
                        'team2_id' => $team2->id,
                        'field_id' => $randomField->id,
                        'team1_score' => $team1Score,
                        'team2_score' => $team2Score,
                        'game_set' => $set,
                        'set' => 1,
                        'status' => 'Completed',
                        'who_is_serving' => $servingOptions[array_rand($servingOptions)],
                        'winner_id' => $winnerId,
                        'created_at' => $gameDate,
                        'updated_at' => $gameDate,
                    ]);
                    
                    $winnerText = $winnerId === $team1->id ? $team1->name : $team2->name;
                    $this->command->line("  Set {$set}: {$team1Score}-{$team2Score} - Winner: {$winnerText} - Series: {$team1Wins}-{$team2Wins} - {$gameDate->format('M d, Y H:i')}");
                    
                    $set++;
                    $totalGames++;
                    $dayOffset++;
                }
                $this->command->info("  → MATCH RESULT: TIE BREAK - {$team1Wins}-{$team2Wins}");
                
            } else if ($matchup['result_type'] === 'four_wins') {
                // Four wins: play until one team reaches 4 wins
                while ($team1Wins < 4 && $team2Wins < 4) {
                    $scoreOptions = [0, 15, 30];
                    
                    // Bias towards team1 winning to ensure 4 wins faster
                    $winnerTeam = (rand(1, 10) <= 7) ? 0 : 1; // 70% chance team1 wins
                    
                    if ($winnerTeam === 0) {
                        $team1Score = 40;
                        $team2Score = $scoreOptions[array_rand($scoreOptions)];
                        $winnerId = $team1->id;
                        $team1Wins++;
                    } else {
                        $team1Score = $scoreOptions[array_rand($scoreOptions)];
                        $team2Score = 40;
                        $winnerId = $team2->id;
                        $team2Wins++;
                    }
                    
                    // Ensure the game date doesn't exceed today
                    $gameDate = $baseDate->copy()->addDays($dayOffset)->addHours(rand(9, 17))->addMinutes(rand(0, 59));
                    if ($gameDate->isAfter(Carbon::now())) {
                        $gameDate = Carbon::now()->subDays(rand(0, 5))->addHours(rand(9, 17))->addMinutes(rand(0, 59));
                    }
                    $randomField = $fields->random();
                    
                    Game::create([
                        'name' => $matchup['game_type'],
                        'team1_id' => $team1->id,
                        'team2_id' => $team2->id,
                        'field_id' => $randomField->id,
                        'team1_score' => $team1Score,
                        'team2_score' => $team2Score,
                        'game_set' => $set,
                        'set' => 1,
                        'status' => 'Completed',
                        'who_is_serving' => $servingOptions[array_rand($servingOptions)],
                        'winner_id' => $winnerId,
                        'created_at' => $gameDate,
                        'updated_at' => $gameDate,
                    ]);
                    
                    $winnerText = $winnerId === $team1->id ? $team1->name : $team2->name;
                    $this->command->line("  Set {$set}: {$team1Score}-{$team2Score} - Winner: {$winnerText} - Series: {$team1Wins}-{$team2Wins} - {$gameDate->format('M d, Y H:i')}");
                    
                    $set++;
                    $totalGames++;
                    $dayOffset++;
                }
                
                $matchWinner = $team1Wins === 4 ? $team1->name : $team2->name;
                $this->command->info("  → MATCH WINNER: {$matchWinner} wins {$team1Wins}-{$team2Wins}");
            }
        }

        $this->command->info("\n=== FINAL SUMMARY ===");
        $this->command->info("Successfully created {$totalGames} qualification games for Group A only!");
        $this->command->info('Round Robin Format: All teams play against each other');
        $this->command->info('Game Type: All games are QUALIFICATION matches');
        $this->command->info('- Adi vs Budi: Tie break match (3-3)');
        $this->command->info('- All other matches: Four wins format (4-X)');
        $this->command->info('Total matchups: ' . count($matchups));
        $this->command->info('All games scheduled in the past 30 days from ' . $baseDate->format('M d, Y') . ' onwards');
    }
}
