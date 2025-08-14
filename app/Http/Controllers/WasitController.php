<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WasitController extends Controller
{
    /**
     * Show the match selection page
     */
    public function index()
    {
        // Sort teams by group name (ascending), then by team name (ascending)
        $teams = Team::with('group')
            ->join('groups', 'teams.group_id', '=', 'groups.id')
            ->orderBy('groups.name', 'asc')
            ->orderBy('teams.name', 'asc')
            ->select('teams.*')
            ->get()
            ->load('group'); // Re-load the group relationship after join
            
        $games = Game::with(['team1.group', 'team2.group'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $fields = Field::orderBy('name')->get();
        
        return view('wasit-selection', compact('teams', 'games', 'fields'));
    }

    /**
     * Get matches based on filters
     */
    public function getMatches(Request $request): JsonResponse
    {
        $query = Game::with(['team1.group', 'team2.group']);
        
        if ($request->match_type) {
            $query->where('name', $request->match_type);
        }
        
        if ($request->team1_id && $request->team2_id) {
            $query->where(function ($q) use ($request) {
                $q->where(function ($subq) use ($request) {
                    $subq->where('team1_id', $request->team1_id)
                         ->where('team2_id', $request->team2_id);
                })->orWhere(function ($subq) use ($request) {
                    $subq->where('team1_id', $request->team2_id)
                         ->where('team2_id', $request->team1_id);
                });
            });
        } elseif ($request->team1_id || $request->team2_id) {
            $teamId = $request->team1_id ?: $request->team2_id;
            $query->where(function ($q) use ($teamId) {
                $q->where('team1_id', $teamId)->orWhere('team2_id', $teamId);
            });
        }
        
        $games = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'games' => $games->map(function ($game) {
                return [
                    'id' => $game->id,
                    'name' => $game->name,
                    'formatted_name' => ucfirst(str_replace('-', ' ', $game->name)),
                    'team1_name' => $game->team1->name,
                    'team2_name' => $game->team2->name,
                    'team1_group' => $game->team1->group->name,
                    'team2_group' => $game->team2->group->name,
                    'game_set' => $game->game_set,
                    'set' => $game->set,
                    'team1_score' => $game->team1_score,
                    'team2_score' => $game->team2_score,
                    'status' => $game->status,
                    'winner' => $game->winner,
                    'winner_name' => $game->winner ? 
                        ($game->winner === 'team1' ? $game->team1->name : $game->team2->name) : null,
                    'who_is_serving' => $game->who_is_serving,
                    'serving_team_name' => $game->who_is_serving ? 
                        ($game->who_is_serving === 'team1' ? $game->team1->name : $game->team2->name) : null,
                ];
            })
        ]);
    }

    /**
     * Create a new match
     */
    public function createMatch(Request $request): JsonResponse
    {
        $request->validate([
            'match_type' => 'required|in:qualification,semifinal,final,Quarter-Final',
            'team1_id' => 'required|exists:teams,id',
            'team2_id' => 'required|exists:teams,id|different:team1_id',
            'field_id' => 'required|exists:fields,id',
            'set' => 'required|integer|min:1',
        ]);

        // Check if the field has any incomplete games
        $lastIncompleteGame = Game::where('field_id', $request->field_id)
            ->where('status', '!=', 'Completed')
            ->orderBy('created_at', 'desc')
            ->first();
            
        if ($lastIncompleteGame) {
            $lastIncompleteGame->load(['team1.group', 'team2.group']);
            
            return response()->json([
                'success' => false,
                'field_busy' => true,
                'message' => 'Cannot create match because the last match on this field is not finished.',
                'incomplete_game' => [
                    'id' => $lastIncompleteGame->id,
                    'name' => $lastIncompleteGame->name,
                    'formatted_name' => ucfirst(str_replace('-', ' ', $lastIncompleteGame->name)),
                    'team1_name' => $lastIncompleteGame->team1->name,
                    'team2_name' => $lastIncompleteGame->team2->name,
                    'team1_group' => $lastIncompleteGame->team1->group->name,
                    'team2_group' => $lastIncompleteGame->team2->group->name,
                    'game_set' => $lastIncompleteGame->game_set,
                    'set' => $lastIncompleteGame->set,
                    'team1_score' => $lastIncompleteGame->team1_score,
                    'team2_score' => $lastIncompleteGame->team2_score,
                    'status' => $lastIncompleteGame->status,
                    'referee_url' => route('wasit.referee', $lastIncompleteGame->id),
                ],
            ], 400);
        }

        $game = Game::create([
            'name' => $request->match_type,
            'team1_id' => $request->team1_id,
            'team2_id' => $request->team2_id,
            'field_id' => $request->field_id,
            // game_set will be auto-incremented by the Game model's boot method
            'set' => $request->set,
            'team1_score' => 0,
            'team2_score' => 0,
            'status' => 'scheduled',
            'winner' => null,
            'who_is_serving' => null,
        ]);

        $game->load(['team1.group', 'team2.group']);

        return response()->json([
            'success' => true,
            'message' => 'Match created successfully!',
            'game' => [
                'id' => $game->id,
                'name' => $game->name,
                'formatted_name' => ucfirst(str_replace('-', ' ', $game->name)),
                'team1_name' => $game->team1->name,
                'team2_name' => $game->team2->name,
                'team1_group' => $game->team1->group->name,
                'team2_group' => $game->team2->group->name,
                'game_set' => $game->game_set,
                'set' => $game->set,
                'team1_score' => $game->team1_score,
                'team2_score' => $game->team2_score,
                'status' => $game->status,
            ]
        ]);
    }

    /**
     * Show the referee panel for a specific game
     */
    public function referee(Game $game)
    {
        $game->load(['team1.group', 'team2.group']);
        
        // Check if this is a tie-break situation (3-3)
        $isTieBreak = $this->checkTieBreakStatus($game);
        
        // Calculate total wins for each team in head-to-head matches - ALWAYS show qualification wins for display
        $team1TotalWins = Game::where(function($query) use ($game) {
                $query->where(function($subQuery) use ($game) {
                    $subQuery->where('team1_id', $game->team1_id)
                             ->where('team2_id', $game->team2_id);
                })
                ->orWhere(function($subQuery) use ($game) {
                    $subQuery->where('team1_id', $game->team2_id)
                             ->where('team2_id', $game->team1_id);
                });
            })
            ->where('name', $game->name) // Always show qualification wins for total display
            ->where('status', 'Completed')
            ->where('winner_id', $game->team1_id)
            ->count();
            
        $team2TotalWins = Game::where(function($query) use ($game) {
                $query->where(function($subQuery) use ($game) {
                    $subQuery->where('team1_id', $game->team1_id)
                             ->where('team2_id', $game->team2_id);
                })
                ->orWhere(function($subQuery) use ($game) {
                    $subQuery->where('team1_id', $game->team2_id)
                             ->where('team2_id', $game->team1_id);
                });
            })
            ->where('name', $game->name) // Always show qualification wins for total display
            ->where('status', 'Completed')
            ->where('winner_id', $game->team2_id)
            ->count();
        
        return view('wasit-referee', compact('game', 'isTieBreak', 'team1TotalWins', 'team2TotalWins'));
    }
    
    /**
     * Check if the current match is in tie-break status (3-3)
     * Always checks qualification wins regardless of current game type
     */
    private function checkTieBreakStatus(Game $game): bool
    {
        // Get all completed QUALIFICATION games for this team pairing (regardless of current game type)
        $team1Wins = Game::where(function($query) use ($game) {
                $query->where(function($subQuery) use ($game) {
                    $subQuery->where('team1_id', $game->team1_id)
                             ->where('team2_id', $game->team2_id);
                })
                ->orWhere(function($subQuery) use ($game) {
                    $subQuery->where('team1_id', $game->team2_id)
                             ->where('team2_id', $game->team1_id);
                });
            })
            ->where('name', $game->name) // Always check qualification wins for tie break determination
            ->where('status', 'Completed')
            ->where('winner_id', $game->team1_id)
            ->count();
            
        $team2Wins = Game::where(function($query) use ($game) {
                $query->where(function($subQuery) use ($game) {
                    $subQuery->where('team1_id', $game->team1_id)
                             ->where('team2_id', $game->team2_id);
                })
                ->orWhere(function($subQuery) use ($game) {
                    $subQuery->where('team1_id', $game->team2_id)
                             ->where('team2_id', $game->team1_id);
                });
            })
            ->where('name', $game->name) // Always check qualification wins for tie break determination
            ->where('status', 'Completed')
            ->where('winner_id', $game->team2_id)
            ->count();
            
        return $team1Wins == 3 && $team2Wins == 3;
    }

    /**
     * Update who is serving for a specific game
     */
    public function updateServing(Request $request, Game $game): JsonResponse
    {
        $request->validate([
            'who_is_serving' => 'required|in:team1,team2',
        ]);

        try {
            $game->update([
                'who_is_serving' => $request->who_is_serving,
            ]);

            $game->load(['team1.group', 'team2.group']);

            $servingTeamName = $request->who_is_serving === 'team1' 
                ? $game->team1->name 
                : $game->team2->name;

            return response()->json([
                'success' => true,
                'message' => 'Serving team updated successfully!',
                'who_is_serving' => $game->who_is_serving,
                'serving_team_name' => $servingTeamName,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update serving team: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update score for a specific game
     */
    public function updateScore(Request $request, Game $game): JsonResponse
    {
        $request->validate([
            'team1_score' => 'sometimes|string|max:10',
            'team2_score' => 'sometimes|string|max:10',
        ]);

        try {
            $updateData = [];
            
            if ($request->has('team1_score')) {
                $updateData['team1_score'] = $request->team1_score;
            }
            
            if ($request->has('team2_score')) {
                $updateData['team2_score'] = $request->team2_score;
            }

            $game->update($updateData);
            $game->load(['team1.group', 'team2.group']);

            return response()->json([
                'success' => true,
                'message' => 'Score updated successfully!',
                'team1_score' => $game->team1_score,
                'team2_score' => $game->team2_score,
                'updated_fields' => array_keys($updateData),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update score: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Update tie-break score with increment/decrement
     */
    public function updateTieBreakScore(Request $request, Game $game): JsonResponse
    {
        $request->validate([
            'team' => 'required|in:team1,team2',
            'action' => 'required|in:increment,decrement',
        ]);

        try {
            $team = $request->team;
            $action = $request->action;
            $scoreField = $team . '_score';
            $currentScore = (int) $game->$scoreField;
            
            if ($action === 'increment') {
                $newScore = $currentScore + 1;
            } else {
                // Prevent negative scores
                $newScore = max(0, $currentScore - 1);
            }
            
            $game->update([
                $scoreField => (string) $newScore
            ]);
            
            $game->load(['team1.group', 'team2.group']);

            return response()->json([
                'success' => true,
                'message' => 'Tie-break score updated successfully!',
                'team1_score' => $game->team1_score,
                'team2_score' => $game->team2_score,
                'updated_team' => $team,
                'new_score' => $newScore,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tie-break score: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * End a specific game with validation
     */
    public function endGame(Request $request, Game $game): JsonResponse
    {
        try {
            // Check tie-break status first
            $isTieBreak = $this->checkTieBreakStatus($game );
            
            // Check if manual winner was provided
            $hasManualWinner = $request->has('manual_winner') && !empty($request->manual_winner);
            
            // For manual winner selection, validate the team ID
            if ($hasManualWinner) {
                $request->validate([
                    'manual_winner' => 'required|integer|in:' . $game->team1_id . ',' . $game->team2_id
                ]);
                
                $manualWinnerId = (int) $request->manual_winner;
                $winner = $manualWinnerId === $game->team1_id ? 'team1' : 'team2';
                $winner_id = $manualWinnerId;
                
                // Update game status to completed with manual winner
                $game->update([
                    'status' => 'Completed',
                    'winner_id' => $winner_id,
                ]);
                
                $game->load(['team1.group', 'team2.group']);
                
                $winnerName = $winner === 'team1' ? $game->team1->name : $game->team2->name;
                
                return response()->json([
                    'success' => true,
                    'message' => 'Game ended successfully with manual winner selection!',
                    'winner' => $winner,
                    'winner_name' => $winnerName,
                    'final_scores' => [
                        'team1' => $game->team1_score,
                        'team2' => $game->team2_score,
                    ],
                    'status' => $game->status,
                    'was_manual_winner' => true,
                ]);
            }
            
            // Check for 40-40 special case (requires manual winner)
            $is40vs40 = !$isTieBreak && $game->team1_score == '40' && $game->team2_score == '40';
            
            if ($isTieBreak) {
                // Tie-break game validation - only requires different scores
                if ($game->team1_score == $game->team2_score) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tie-break game cannot be ended with same scores! Scores must be different to determine a winner.',
                        'current_scores' => [
                            'team1' => $game->team1_score,
                            'team2' => $game->team2_score,
                        ],
                    ], 400);
                }
            } else {
                // Regular game validation
                if ($game->team1_score != '40' && $game->team2_score != '40') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Game cannot be ended! One team must reach 40 points first.',
                        'current_scores' => [
                            'team1' => $game->team1_score,
                            'team2' => $game->team2_score,
                        ],
                    ], 400);
                }
                
                // Special case: 40-40 requires manual winner selection
                if ($is40vs40) {
                    return response()->json([
                        'success' => false,
                        'requires_manual_winner' => true,
                        'message' => 'Score is 40-40. Please select which team wins using the WINNER section.',
                        'teams' => [
                            'team1' => [
                                'id' => $game->team1_id,
                                'name' => $game->team1->name,
                                'group' => $game->team1->group->name
                            ],
                            'team2' => [
                                'id' => $game->team2_id,
                                'name' => $game->team2->name,
                                'group' => $game->team2->group->name
                            ]
                        ],
                        'current_scores' => [
                            'team1' => $game->team1_score,
                            'team2' => $game->team2_score,
                        ],
                    ], 400);
                }
                
                // Regular case: winner is the team with 40 points
                if ($game->team1_score == '40') {
                    $winner = 'team1';
                    $winner_id = $game->team1_id;
                } else {
                    $winner = 'team2';
                    $winner_id = $game->team2_id;
                }
            }

            // Determine winner for tie-break games
            if ($isTieBreak) {
                $team1Score = is_numeric($game->team1_score) ? (int)$game->team1_score : 0;
                $team2Score = is_numeric($game->team2_score) ? (int)$game->team2_score : 0;
                
                if ($team1Score > $team2Score) {
                    $winner = 'team1';
                    $winner_id = $game->team1_id;
                } else {
                    $winner = 'team2';
                    $winner_id = $game->team2_id;
                }
            }

            // Update game status to completed
            $game->update([
                'status' => 'Completed',
                'winner_id' => $winner_id,
            ]);

            $game->load(['team1.group', 'team2.group']);
            
            $winnerName = $winner === 'team1' ? $game->team1->name : $game->team2->name;

            return response()->json([
                'success' => true,
                'message' => 'Game ended successfully!',
                'winner' => $winner,
                'winner_name' => $winnerName,
                'final_scores' => [
                    'team1' => $game->team1_score,
                    'team2' => $game->team2_score,
                ],
                'status' => $game->status,
                'was_manual_winner' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to end game: ' . $e->getMessage(),
            ], 500);
        }
    }
}
