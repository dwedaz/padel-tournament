<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    protected $fillable = [
        'team1_id',
        'team2_id',
        'game_set',
        'set',
        'name',
        'status',
        'who_is_serving',
        'team1_score',
        'team2_score',
        'winner_id',
    ];

    /**
     * Boot method to handle automatic game_set increment
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($game) {
            // Always auto-assign game_set based on existing games
            // Find if this combination of teams and set already exists
            $existingGame = static::where(function ($query) use ($game) {
                $query->where('team1_id', $game->team1_id)
                      ->where('team2_id', $game->team2_id)
                      ->where('set', $game->set);
            })
            ->orWhere(function ($query) use ($game) {
                $query->where('team1_id', $game->team2_id)
                      ->where('team2_id', $game->team1_id)
                      ->where('set', $game->set);
            })
            ->first();
            
            if ($existingGame) {
                // If combination exists, find the next available game_set
                $maxGameSet = static::where(function ($query) use ($game) {
                    $query->where('team1_id', $game->team1_id)
                          ->where('team2_id', $game->team2_id)
                          ->where('set', $game->set);
                })
                ->orWhere(function ($query) use ($game) {
                    $query->where('team1_id', $game->team2_id)
                          ->where('team2_id', $game->team1_id)
                          ->where('set', $game->set);
                })
                ->max('game_set');
                
                $game->game_set = ($maxGameSet ?? 0) + 1;
            } else {
                // If combination doesn't exist, start with game_set = 1
                $game->game_set = 1;
            }
        });
    }

    public function team1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_id');
    }

    // Legacy method for backward compatibility
    public function winningTeam(): BelongsTo
    {
        return $this->winner();
    }

    /**
     * Get the team that is currently serving
     */
    public function servingTeam(): ?BelongsTo
    {
        if ($this->who_is_serving === 'team1') {
            return $this->team1();
        } elseif ($this->who_is_serving === 'team2') {
            return $this->team2();
        }
        return null;
    }

    /**
     * Check if the game is in tie break
     */
    public function isTieBreak(): bool
    {
        return strtolower($this->status ?? '') === 'tie break';
    }

    /**
     * Switch the serving team
     */
    public function switchServe(): void
    {
        $this->who_is_serving = $this->who_is_serving === 'team1' ? 'team2' : 'team1';
        $this->save();
    }

    /**
     * Get formatted name for display
     */
    public function getFormattedNameAttribute(): string
    {
        return match($this->name) {
            'qualification' => 'Qualification',
            'semi-final' => 'Semi-Final',
            'final' => 'Final',
            default => ucfirst($this->name)
        };
    }
}
