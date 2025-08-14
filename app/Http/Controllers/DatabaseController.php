<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Models\Game;

class DatabaseController extends Controller
{
    /**
     * Reset the database (fresh migration)
     */
    public function reset(): RedirectResponse
    {
        try {
            // Store current user to restore later
            $currentUser = Auth::user();
            
            // Run migrate:fresh to drop all tables and re-run migrations
            Artisan::call('migrate:fresh', ['--force' => true]);
            
            // If there was a current user, recreate admin user
            if ($currentUser) {
                Artisan::call('db:seed', [
                    '--class' => 'AdminUserSeeder',
                    '--force' => true
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Database reset successfully! All tournament data has been cleared and tables recreated.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reset database: ' . $e->getMessage() . '. Please check your database connection and permissions.');
        }
    }
    
    /**
     * Reseed the database (fresh migration + seed tournament data only)
     */
    public function reseed(): RedirectResponse
    {
        try {
            // Store current user to restore later
            $currentUser = Auth::user();
            
            // Run migrate:fresh to drop all tables and re-run migrations
            Artisan::call('migrate:fresh', ['--force' => true]);
            
            // Restore admin user if there was one
            if ($currentUser) {
                Artisan::call('db:seed', [
                    '--class' => 'AdminUserSeeder',
                    '--force' => true
                ]);
            }
            
            // Run only tournament-related seeders (exclude user seeders)
            Artisan::call('db:seed', [
                '--class' => 'GroupSeeder',
                '--force' => true
            ]);
            
            Artisan::call('db:seed', [
                '--class' => 'TeamSeeder',
                '--force' => true
            ]);
            
            Artisan::call('db:seed', [
                '--class' => 'FieldSeeder',
                '--force' => true
            ]);
            
            Artisan::call('db:seed', [
                '--class' => 'GameSeeder',
                '--force' => true
            ]);
            
            return redirect()->back()
                ->with('success', 'Database reseeded successfully! Tournament data has been recreated with fresh groups, teams, fields, and games. User accounts were preserved.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reseed database: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear all matches/games only (keep teams, groups, fields)
     */
    public function clearMatches(): RedirectResponse
    {
        try {
            // Count games before deletion for feedback
            $gameCount = Game::count();
            
            // Delete all games/matches
            Game::truncate();
            
            return redirect()->back()
                ->with('success', "Matches cleared successfully! Deleted {$gameCount} games. Teams, groups, and fields are preserved.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to clear matches: ' . $e->getMessage());
        }
    }
    
    /**
     * Show confirmation page for database operations
     */
    public function confirm(Request $request)
    {
        $action = $request->query('action', 'reset');
        
        if (!in_array($action, ['reset', 'reseed', 'clear-matches'])) {
            abort(404);
        }
        
        return view('database.confirm', compact('action'));
    }
}
