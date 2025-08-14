<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'group_id',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function homeGames(): HasMany
    {
        return $this->hasMany(Game::class, 'team1_id');
    }

    public function awayGames(): HasMany
    {
        return $this->hasMany(Game::class, 'team2_id');
    }

    public function games()
    {
        return $this->homeGames()->union($this->awayGames());
    }

    /**
     * Get total wins against another team by ID
     * 
     * @param int $opponentId The ID of the opponent team
     * @return int Total number of wins against the specified team
     */
    public function getTotalWinsAgainst(int $opponentId): int
    {
        return Game::where('winner_id', $this->id)
            ->where(function ($query) use ($opponentId) {
                $query->where(function ($subQuery) use ($opponentId) {
                    // This team is team1, opponent is team2
                    $subQuery->where('team1_id', $this->id)
                             ->where('team2_id', $opponentId);
                })
                ->orWhere(function ($subQuery) use ($opponentId) {
                    // This team is team2, opponent is team1
                    $subQuery->where('team1_id', $opponentId)
                             ->where('team2_id', $this->id);
                });
            })
            ->count();
    }
}
