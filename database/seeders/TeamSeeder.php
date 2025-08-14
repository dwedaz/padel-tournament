<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fixed team names - these will always be the same when reseeding
        $fixedTeamNames = [
            // Group A teams (4 teams)
            'Adi', 'Budi', 'Citra', 'Dani',
            // Group B teams (4 teams)  
            'Eka', 'Fitri', 'Gita', 'Hani',
            // Group C teams (4 teams)
            'Indra', 'Joko', 'Kania', 'Lina',
            // Group D teams (4 teams)
            'Maya', 'Nina', 'Oscar', 'Prita',
            // Group E teams (4 teams)
            'Qori', 'Rina', 'Sari', 'Tari',
            // Group F teams (4 teams)
            'Umi', 'Vera', 'Wati', 'Yuni'
        ];

        $groups = Group::orderBy('name')->get(); // Order by name to ensure consistent assignment
        $teamNameIndex = 0;
        
        foreach ($groups as $group) {
            $teamsCount = 0;
            
            // Determine team count based on group
            switch ($group->name) {
                case 'Group A':
                case 'Group B':
                case 'Group C':
                case 'Group D':
                case 'Group E':
                case 'Group F':
                    $teamsCount = 4;
                    break;
                case 'Group G':
                case 'Group H':
                    $teamsCount = 1; // Groups G and H get 1 BYE team each
                    break;
            }
            
            for ($i = 0; $i < $teamsCount; $i++) {
                // For Groups G and H, use 'BYE', otherwise use fixed names
                if (in_array($group->name, ['Group G', 'Group H'])) {
                    $currentTeamName = 'BYE';
                } else {
                    $currentTeamName = $fixedTeamNames[$teamNameIndex];
                    $teamNameIndex++;
                }
                
                Team::create([
                    'name' => $currentTeamName,
                    'group_id' => $group->id
                ]);
            }
        }
    }
}
