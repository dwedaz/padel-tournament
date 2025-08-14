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
        
        // Get paginated games ordered by datetime (most recent first)
        $homeGames = $team->homeGames()->with(['team2.group', 'winner'])->get();
        $awayGames = $team->awayGames()->with(['team1.group', 'winner'])->get();
        
        // Merge and sort all games by created_at descending
        $allGames = $homeGames->merge($awayGames)->sortByDesc('created_at');
        
        // Convert to paginator
        $currentPage = request()->get('page', 1);
        $perPage = 10;
        $currentItems = $allGames->forPage($currentPage, $perPage)->values();
        
        $paginatedGames = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $allGames->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page'
            ]
        );
        $paginatedGames->appends(request()->query());
        
        return view('teams.show', compact('team', 'paginatedGames'));
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
