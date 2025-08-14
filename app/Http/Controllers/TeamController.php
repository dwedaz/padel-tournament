<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Group;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $teams = Team::with('group')->get();
        return view('teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $groups = Group::all();
        return view('teams.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request): RedirectResponse
    {
        $group = Group::find($request->group_id);
        
        // Double check the limit before creating
        if ($group && $group->teams()->count() >= 5) {
            return redirect()->route('teams.create')
                ->with('error', 'This group already has maximum 5 teams.')
                ->withInput();
        }

        Team::create($request->validated());

        return redirect()->route('teams.index')
            ->with('success', 'Team created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team): View
    {
        $team->load(['group']);
        
        // Get all games with related models
        $homeGames = $team->homeGames()->with(['team2.group', 'field', 'winnerTeam'])->get();
        $awayGames = $team->awayGames()->with(['team1.group', 'field', 'winnerTeam'])->get();
        
        // Merge all games
        $allGames = $homeGames->merge($awayGames);
        
        // Group games by opponent and set
        $groupedMatches = [];
        foreach ($allGames as $game) {
            $opponentId = $game->team1_id == $team->id ? $game->team2_id : $game->team1_id;
            $opponentName = $game->team1_id == $team->id ? $game->team2->name : $game->team1->name;
            $opponentGroup = $game->team1_id == $team->id ? $game->team2->group->name : $game->team1->group->name;
            $set = $game->set;
            $key = $opponentId . '_' . $set;
            
            if (!isset($groupedMatches[$key])) {
                $groupedMatches[$key] = [
                    'opponent_id' => $opponentId,
                    'opponent_name' => $opponentName,
                    'opponent_group' => $opponentGroup,
                    'set' => $set,
                    'games' => [],
                    'team_wins' => 0,
                    'opponent_wins' => 0,
                    'match_winner' => null,
                    'match_type' => $game->formatted_name,
                    'latest_date' => $game->created_at,
                ];
            }
            
            $groupedMatches[$key]['games'][] = $game;
            $groupedMatches[$key]['latest_date'] = max($groupedMatches[$key]['latest_date'], $game->created_at);
            
            // Count wins for this set
            if ($game->winner_id == $team->id) {
                $groupedMatches[$key]['team_wins']++;
            } elseif ($game->winner_id) {
                $groupedMatches[$key]['opponent_wins']++;
            }
        }
        
        // Determine match winners and sort by date
        foreach ($groupedMatches as &$match) {
            if ($match['team_wins'] >= 4) {
                $match['match_winner'] = 'team';
                $match['result_class'] = 'bg-green-100 text-green-800';
                $match['result_text'] = 'Won';
            } elseif ($match['opponent_wins'] >= 4) {
                $match['match_winner'] = 'opponent';
                $match['result_class'] = 'bg-red-100 text-red-800';
                $match['result_text'] = 'Lost';
            } else {
                $match['match_winner'] = 'ongoing';
                $match['result_class'] = 'bg-yellow-100 text-yellow-800';
                $match['result_text'] = 'Ongoing';
            }
        }
        
        // Sort by latest date descending
        uasort($groupedMatches, function($a, $b) {
            return $b['latest_date'] <=> $a['latest_date'];
        });
        
        // Convert to paginator
        $currentPage = request()->get('page', 1);
        $perPage = 10;
        $matchesCollection = collect(array_values($groupedMatches));
        $currentItems = $matchesCollection->forPage($currentPage, $perPage);
        
        $paginatedMatches = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $matchesCollection->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page'
            ]
        );
        $paginatedMatches->appends(request()->query());
        
        return view('teams.show', compact('team', 'paginatedMatches'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team): View
    {
        $groups = Group::all();
        return view('teams.edit', compact('team', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamRequest $request, Team $team): RedirectResponse
    {
        $team->update($request->validated());

        return redirect()->route('teams.index')
            ->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team): RedirectResponse
    {
        // Check if team has games
        $gamesCount = $team->homeGames()->count() + $team->awayGames()->count();
        if ($gamesCount > 0) {
            return redirect()->route('teams.index')
                ->with('error', 'Cannot delete team that has games.');
        }

        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully.');
    }
}
