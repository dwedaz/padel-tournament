<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WasitController;
use App\Models\Game;
use App\Models\Group;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/matrix', function () {
    // Get group from URL parameter
    $groupName = request()->query('group', 'Group A');
    
    // Get the specified group with teams
    $group = \App\Models\Group::where('name', $groupName)->with('teams')->first();
    
    if (!$group || $group->teams->isEmpty()) {
        return view('group-view', [
            'group' => null,
            'teams' => collect(),
            'scores' => [],
            'groupName' => $groupName,
            'error' => 'Group not found or has no teams'
        ]);
    }
    
    $teams = $group->teams;
    
    // Calculate scores matrix using winner_id (proper logic)
    $scores = [];
    foreach ($teams as $index => $team) {
        $scores[$index] = [];
        
        foreach ($teams as $innerIndex => $innerTeam) {
            if ($team->id === $innerTeam->id) {
                $scores[$index][$innerIndex] = 'X'; // Replace self-match with 'X'
            } else {
                // Count wins for $team against $innerTeam using winner_id
                $totalWins = \App\Models\Game::where(function($query) use ($team, $innerTeam) {
                        $query->where(function($subQuery) use ($team, $innerTeam) {
                            $subQuery->where('team1_id', $team->id)
                                     ->where('team2_id', $innerTeam->id);
                        })
                        ->orWhere(function($subQuery) use ($team, $innerTeam) {
                            $subQuery->where('team1_id', $innerTeam->id)
                                     ->where('team2_id', $team->id);
                        });
                    })
                    ->where('name', 'qualification')
                    ->where('status', 'Completed')
                    ->where('winner_id', $team->id)
                    ->count();
                
                // Check for tie-break situation (both teams have 3 wins)
                $opponentWins = \App\Models\Game::where(function($query) use ($team, $innerTeam) {
                        $query->where(function($subQuery) use ($team, $innerTeam) {
                            $subQuery->where('team1_id', $team->id)
                                     ->where('team2_id', $innerTeam->id);
                        })
                        ->orWhere(function($subQuery) use ($team, $innerTeam) {
                            $subQuery->where('team1_id', $innerTeam->id)
                                     ->where('team2_id', $team->id);
                        });
                    })
                    ->where('name', 'qualification')
                    ->where('status', 'Completed')
                    ->where('winner_id', $innerTeam->id)
                    ->count();
                
                // If both teams have 3 wins, check for active tie-break game
                if ($totalWins == 3 && $opponentWins == 3) {
                    $activeGame = \App\Models\Game::where(function($query) use ($team, $innerTeam) {
                            $query->where(function($subQuery) use ($team, $innerTeam) {
                                $subQuery->where('team1_id', $team->id)
                                         ->where('team2_id', $innerTeam->id);
                            })
                            ->orWhere(function($subQuery) use ($team, $innerTeam) {
                                $subQuery->where('team1_id', $innerTeam->id)
                                         ->where('team2_id', $team->id);
                            });
                        })
                        ->where('name', 'qualification')
                        ->where('status', '!=', 'Completed')
                        ->first();
                    
                    if ($activeGame) {
                        // Get current tie-break score
                        $teamScore = ($activeGame->team1_id == $team->id) 
                            ? $activeGame->team1_score 
                            : $activeGame->team2_score;
                        
                        $scores[$index][$innerIndex] = '3/' . $teamScore;
                    } else {
                        $scores[$index][$innerIndex] = $totalWins;
                    }
                } else {
                    $scores[$index][$innerIndex] = $totalWins;
                }
            }
        }
    }
    
    // Calculate match statistics (wins, losses, total)
    foreach($scores as $i => $v) {
        $mWin = 0;
        $mLose = 0;
        $mTotal = 0;
        
        foreach($v as $j => $t) {
            if ($i == $j) continue;
            
            // Extract numeric values from scores (handle tie-break format "3/1")
            $scoreA = $scores[$i][$j];
            $scoreB = $scores[$j][$i];
            
            // Convert tie-break format to numeric value for comparison
            $numericA = is_numeric($scoreA) ? (int)$scoreA : 
                       (strpos($scoreA, '/') !== false ? (int)explode('/', $scoreA)[0] : 0);
            $numericB = is_numeric($scoreB) ? (int)$scoreB : 
                       (strpos($scoreB, '/') !== false ? (int)explode('/', $scoreB)[0] : 0);
            
            // For tie-break situations, don't count as win/loss since game is still ongoing
            if (strpos($scoreA, '/') !== false && strpos($scoreB, '/') !== false) {
                // Both are in tie-break - game is still active, don't count as win/loss yet
                // The tie-break game is ongoing, so no win/loss should be recorded
            } else if (strpos($scoreA, '/') !== false || strpos($scoreB, '/') !== false) {
                // One team is in tie-break, the other isn't - game is still active
                // Don't count as win/loss since tie-break is ongoing
            } else {
                // Regular comparison for completed games only
                if ($numericA > $numericB) {
                    $mWin++;
                } else if ($numericA < $numericB) {
                    $mLose++;
                }
            }
            
            // Add only the numeric part to total
            $mTotal += $numericA;
        }
        
        // Store statistics at the correct indexes
        $scores[$i][$teams->count()] = $mWin;       // Match wins
        $scores[$i][$teams->count() + 1] = $mLose;  // Match losses  
        $scores[$i][$teams->count() + 2] = $mTotal; // Total points
    }
    
    return view('group-view', [
        'group' => $group,
        'teams' => $teams,
        'scores' => $scores,
        'groupName' => $groupName,
        'error' => null
    ]);
})->name('group.view');

Route::get('/group-score', function () {
    // Get first 6 groups with teams for 3x2 grid display
    $groups = \App\Models\Group::with('teams')->limit(6)->get();
    
    // Calculate wins for each team from qualification games
    foreach ($groups as $group) {
        foreach ($group->teams as $team) {
            $totalWins = 0;
            
            // Count wins where team is team1 and won
            $team1Wins = Game::where('team1_id', $team->id)
                ->where('name', 'qualification')
                ->where('status', 'Completed')
                ->where('winner_id', $team->id)
                ->count();
            
            // Count wins where team is team2 and won
            $team2Wins = Game::where('team2_id', $team->id)
                ->where('name', 'qualification')
                ->where('status', 'Completed')
                ->where('winner_id', $team->id)
                ->count();
            
            $team->qualification_wins = $team1Wins + $team2Wins;
            
            // Also calculate losses
            $team1Losses = Game::where('team1_id', $team->id)
                ->where('name', 'qualification')
                ->where('status', 'Completed')
                ->where('winner_id', '!=', $team->id)
                ->whereNotNull('winner_id')
                ->count();
            
            $team2Losses = Game::where('team2_id', $team->id)
                ->where('name', 'qualification')
                ->where('status', 'Completed')
                ->where('winner_id', '!=', $team->id)
                ->whereNotNull('winner_id')
                ->count();
            
            $team->qualification_losses = $team1Losses + $team2Losses;
            
            // Calculate total games played
            $totalGames = Game::where(function($query) use ($team) {
                    $query->where('team1_id', $team->id)
                          ->orWhere('team2_id', $team->id);
                })
                ->where('name', 'qualification')
                ->where('status', 'Completed')
                ->count();
            
            $team->qualification_games = $totalGames;
        }
    }
    
    return view('team-view', compact('groups'));
})->name('team.view');

Route::get('/field-view', function () {
    // Get ALL fields and their latest games
    $fields = \App\Models\Field::orderBy('name')->get();
    
    foreach ($fields as $field) {
        // Get the latest game for this field (prioritize incomplete games, then most recent)
        $latestGame = \App\Models\Game::where('field_id', $field->id)
            ->with(['team1', 'team2'])
            ->orderBy('created_at', 'desc')
            ->first();
            
        $field->latestGame = $latestGame;
    }
    
    return view('field-view', compact('fields'));
})->name('field.view');

// Wasit (Referee) routes
Route::get('/wasit', [WasitController::class, 'index'])->name('wasit');
Route::get('/wasit/matches', [WasitController::class, 'getMatches'])->name('wasit.matches');
Route::post('/wasit/create-match', [WasitController::class, 'createMatch'])->name('wasit.create-match');
Route::delete('/wasit/delete-match/{game}', [WasitController::class, 'deleteMatch'])->name('wasit.delete-match');
Route::get('/wasit/referee/{game}', [WasitController::class, 'referee'])->name('wasit.referee');
Route::post('/wasit/referee/{game}/update-serving', [WasitController::class, 'updateServing'])->name('wasit.update-serving');
Route::post('/wasit/referee/{game}/update-score', [WasitController::class, 'updateScore'])->name('wasit.update-score');
Route::post('/wasit/referee/{game}/update-tiebreak-score', [WasitController::class, 'updateTieBreakScore'])->name('wasit.update-tiebreak-score');
Route::post('/wasit/referee/{game}/end-game', [WasitController::class, 'endGame'])->name('wasit.end-game');



Route::get('/getscore', function () {

    // getscore by group from URL
    $group = request()->query('group', 'default');
    if (!$group) {
        return response()->json(['error' => 'Group not specified'], 400);
    }
    
    // get teams on group form model
    $group = Group::where('name', $group)->firstOrFail();
    $teams = $group->teams;
 
  
$scores = [];
foreach ($teams as $index => $team) {
    $scores[$index] = [];
        
    foreach ($teams as $innerIndex => $innerTeam) {
        if ($team->id === $innerTeam->id) {
            $scores[$index][$innerIndex] = 'X'; // Replace self-match with 'X'
        } else {
            // Count wins for $team against $innerTeam using winner_id (consistent with group-view)
            $totalWins = \App\Models\Game::where(function($query) use ($team, $innerTeam) {
                    $query->where(function($subQuery) use ($team, $innerTeam) {
                        $subQuery->where('team1_id', $team->id)
                                 ->where('team2_id', $innerTeam->id);
                    })
                    ->orWhere(function($subQuery) use ($team, $innerTeam) {
                        $subQuery->where('team1_id', $innerTeam->id)
                                 ->where('team2_id', $team->id);
                    });
                })
                ->where('name', 'qualification')
                ->where('status', 'Completed')
                ->where('winner_id', $team->id)
                ->count();
            
            // Check for tie-break situation (both teams have 3 wins)
            $opponentWins = \App\Models\Game::where(function($query) use ($team, $innerTeam) {
                    $query->where(function($subQuery) use ($team, $innerTeam) {
                        $subQuery->where('team1_id', $team->id)
                                 ->where('team2_id', $innerTeam->id);
                    })
                    ->orWhere(function($subQuery) use ($team, $innerTeam) {
                        $subQuery->where('team1_id', $innerTeam->id)
                                 ->where('team2_id', $team->id);
                    });
                })
                ->where('name', 'qualification')
                ->where('status', 'Completed')
                ->where('winner_id', $innerTeam->id)
                ->count();
            
            // If both teams have 3 wins, check for active tie-break game
            if ($totalWins == 3 && $opponentWins == 3) {
                $activeGame = \App\Models\Game::where(function($query) use ($team, $innerTeam) {
                        $query->where(function($subQuery) use ($team, $innerTeam) {
                            $subQuery->where('team1_id', $team->id)
                                     ->where('team2_id', $innerTeam->id);
                        })
                        ->orWhere(function($subQuery) use ($team, $innerTeam) {
                            $subQuery->where('team1_id', $innerTeam->id)
                                     ->where('team2_id', $team->id);
                        });
                    })
                    ->where('name', 'qualification')
                    ->where('status', '!=', 'Completed')
                    ->first();
                
                if ($activeGame) {
                    // Get current tie-break score
                    $teamScore = ($activeGame->team1_id == $team->id) 
                        ? $activeGame->team1_score 
                        : $activeGame->team2_score;
                    
                    $scores[$index][$innerIndex] = '3/' . $teamScore;
                } else {
                    $scores[$index][$innerIndex] = $totalWins;
                }
            } else {
                $scores[$index][$innerIndex] = $totalWins;
            }
        }
    }
 

      
       
   
    
}

    
    foreach($scores as $i => $v){
         $mWin=0;
         $mLose=0;
         $mTotal = 0;
        foreach($v as $j => $t){

            if ($i==$j) continue;
            
            // Extract numeric values from scores (handle tie-break format "3/1")
            $scoreA = $scores[$i][$j];
            $scoreB = $scores[$j][$i];
            
            // Convert tie-break format to numeric value for comparison
            $numericA = is_numeric($scoreA) ? (int)$scoreA : 
                       (strpos($scoreA, '/') !== false ? (int)explode('/', $scoreA)[0] : 0);
            $numericB = is_numeric($scoreB) ? (int)$scoreB : 
                       (strpos($scoreB, '/') !== false ? (int)explode('/', $scoreB)[0] : 0);
            
            // For tie-break situations, check if one team is winning the tie-break
            if (strpos($scoreA, '/') !== false && strpos($scoreB, '/') !== false) {
                // Both are in tie-break, compare tie-break scores
                $tieScoreA = (int)explode('/', $scoreA)[1];
                $tieScoreB = (int)explode('/', $scoreB)[1];
                
                if ($tieScoreA > $tieScoreB) {
                    $mWin++;
                } else if ($tieScoreA < $tieScoreB) {
                    $mLose++;
                }
                // If tie-break scores are equal, no win/lose counted yet
            } else if (strpos($scoreA, '/') !== false || strpos($scoreB, '/') !== false) {
                // One team is in tie-break, the other isn't (shouldn't happen, but handle it)
                // Compare main scores first, then tie-break status
                if ($numericA > $numericB) {
                    $mWin++;
                } else if ($numericA < $numericB) {
                    $mLose++;
                } else {
                    // Same main score, check tie-break progress
                    if (strpos($scoreA, '/') !== false) {
                        $tieScoreA = (int)explode('/', $scoreA)[1];
                        // If opponent has no tie-break, assume they're at 0
                        if ($tieScoreA > 0) {
                            $mWin++;
                        } else {
                            $mLose++;
                        }
                    }
                }
            } else {
                // Regular comparison
                if ($numericA > $numericB) {
                    $mWin++;
                } else if ($numericA < $numericB) {
                    $mLose++;
                }
            }
            
            // Add only the numeric part to total
            $mTotal += $numericA;
        }

        // Store statistics at the correct indexes (consistent with group-view)
        $scores[$i][$teams->count()] = $mWin;       // Match wins
        $scores[$i][$teams->count() + 1] = $mLose;  // Match losses  
        $scores[$i][$teams->count() + 2] = $mTotal; // Total points
    }
   
    return response()->json([
        'teams' => $teams->pluck('name')->toArray(),
        'scores' => $scores
    ]);
})->name('getscore');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Fields resource routes
    Route::resource('fields', FieldController::class);
    
    // Groups resource routes
    Route::resource('groups', GroupController::class);
    
    // Teams resource routes
    Route::resource('teams', TeamController::class);
    
    // Games resource routes
    Route::resource('games', GameController::class);
    
    // Database operations routes
    Route::get('/database/confirm', [\App\Http\Controllers\DatabaseController::class, 'confirm'])->name('database.confirm');
    Route::post('/database/reset', [\App\Http\Controllers\DatabaseController::class, 'reset'])->name('database.reset');
    Route::post('/database/reseed', [\App\Http\Controllers\DatabaseController::class, 'reseed'])->name('database.reseed');
    Route::post('/database/clear-matches', [\App\Http\Controllers\DatabaseController::class, 'clearMatches'])->name('database.clear-matches');
});

require __DIR__.'/auth.php';
