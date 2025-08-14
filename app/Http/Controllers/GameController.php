<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use App\Models\Field;
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
        $query = Game::with(['team1.group', 'team2.group', 'field']);
        
        // Apply filters
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('team1', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('team2', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('game_set', 'like', "%{$search}%")
                ->orWhere('set', 'like', "%{$search}%");
            });
        }
        
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        if (request('match_type')) {
            $query->where('name', request('match_type'));
        }
        
        if (request('field_id')) {
            $query->where('field_id', request('field_id'));
        }
        
        if (request('team_id')) {
            $teamId = request('team_id');
            $query->where(function($q) use ($teamId) {
                $q->where('team1_id', $teamId)
                  ->orWhere('team2_id', $teamId);
            });
        }
        
        // Apply sorting
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Paginate results
        $perPage = request('per_page', 15);
        $games = $query->paginate($perPage)->appends(request()->query());
        
        // Get filter options
        $teams = Team::with('group')->orderBy('name')->get();
        $fields = Field::orderBy('name')->get();
        $statuses = Game::distinct()->pluck('status')->filter()->sort();
        $matchTypes = Game::distinct()->pluck('name')->filter()->sort();
        
        return view('games.index', compact('games', 'teams', 'fields', 'statuses', 'matchTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $teams = Team::with('group')->orderBy('name')->get();
        $fields = Field::orderBy('name')->get();
        return view('games.create', compact('teams', 'fields'));
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
        $game->load(['team1.group', 'team2.group', 'field']);
        
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
        $fields = Field::orderBy('name')->get();
        return view('games.edit', compact('game', 'teams', 'fields'));
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
