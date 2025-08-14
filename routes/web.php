<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProfileController;
use App\Models\Game;
use App\Models\Group;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/group-view', function () {
    return view('group-view');
})->name('group.view');



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
            // get team last game score
            
              $totalWins = 0;
         
            //looking for total winner from games where value of field team1_score is > team2_score
            $totalGames = Game::where('team1_id', $team->id)
                ->where('team2_id', $innerTeam->id)
                ->get();
           
            if ($totalGames->isEmpty()) {
                 $totalGames = Game::where('team1_id', $innerTeam->id)
                ->where('team2_id', $team->id)
                ->get();

                foreach ($totalGames as $game) {
                if ($game->team2_score > $game->team1_score) {
                    $totalWins++;
                    
                }else{
                }
            }

            }else{
                foreach ($totalGames as $game) {
                    if ($game->team1_score > $game->team2_score) {
                        $totalWins++;
                         
                    }else{
                         
                    } 
                }
            }

            
           
           
            $scores[$index][$innerIndex] = $totalWins;
        }
        
       
       

        // Total Lost users
     
    }
 

      
       
   
    
}

 
    foreach($scores as $i => $v){
         $mWin=0;
         $mLose=0;
        foreach($v as $j => $t){
            if ($i==$j) continue;
            if ($scores[$i][$j]> $scores[$j][$i]){
               $mWin++;
            }else{
                $mLose++;
            }
        }
        $scores[$i][] = $mWin;
        $scores[$i][] = $mLose;
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
    
    // Groups resource routes
    Route::resource('groups', GroupController::class);
    
    // Teams resource routes
    Route::resource('teams', TeamController::class);
    
    // Games resource routes
    Route::resource('games', GameController::class);
});

require __DIR__.'/auth.php';
