<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $games = Game::with(['team1', 'team2', 'winner'])
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('games.index', compact('games'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $teams = Team::with('group')->orderBy('name')->get();
        return view('games.create', compact('teams'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request): RedirectResponse
    {
        $gameData = $request->validated();
        
        // The game_set will be automatically set by the Game model's boot method
        $game = Game::create($gameData);

        return redirect()->route('games.index')
            ->with('success', "Game created successfully. Assigned to Match {$game->game_set} - Set {$game->set}");
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game): View
    {
        $game->load(['team1.group', 'team2.group', 'winner']);
        
        // Get other games between the same teams
        $otherGames = Game::where(function ($query) use ($game) {
            $query->where('team1_id', $game->team1_id)
                  ->where('team2_id', $game->team2_id);
        })
        ->orWhere(function ($query) use ($game) {
            $query->where('team1_id', $game->team2_id)
                  ->where('team2_id', $game->team1_id);
        })
        ->where('id', '!=', $game->id)
        ->with(['team1', 'team2'])
        ->orderBy('game_set')
        ->get();
        
        return view('games.show', compact('game', 'otherGames'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game): View
    {
        $teams = Team::with('group')->orderBy('name')->get();
        return view('games.edit', compact('game', 'teams'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameRequest $request, Game $game): RedirectResponse
    {
        $game->update($request->validated());

        return redirect()->route('games.index')
            ->with('success', 'Game updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game): RedirectResponse
    {
        $game->delete();

        return redirect()->route('games.index')
            ->with('success', 'Game deleted successfully.');
    }
}
