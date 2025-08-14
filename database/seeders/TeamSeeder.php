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
        $firstNames = [
            'Alex', 'Ben', 'Carl', 'Dan', 'Eva', 'Finn', 'Gina', 'Hope', 
            'Ian', 'Joy', 'Kate', 'Leo', 'Max', 'Nora', 'Owen', 'Pia', 
            'Quinn', 'Ray', 'Sam', 'Tara', 'Uma', 'Val', 'Wade', 'Zoe'
        ];

        $groups = Group::all();
        
        foreach ($groups as $group) {
            // Create 5 teams per group
            for ($i = 0; $i < 5; $i++) {
                // Pick two random different names
                $name1 = $firstNames[array_rand($firstNames)];
                do {
                    $name2 = $firstNames[array_rand($firstNames)];
                } while ($name1 === $name2);
                
                // Create team name in format "Name1 X Name2" with max 10 chars
                $teamName = $name1 . ' X ' . $name2;
                
                // If longer than 10 chars, use shorter names or abbreviate
                if (strlen($teamName) > 10) {
                    // Try with 3-char names
                    $short1 = substr($name1, 0, 3);
                    $short2 = substr($name2, 0, 3);
                    $teamName = $short1 . ' X ' . $short2;
                }
                
                // Make sure team name is unique
                $originalName = $teamName;
                $counter = 1;
                while (Team::where('name', $teamName)->exists()) {
                    $teamName = $originalName . $counter;
                    if (strlen($teamName) > 10) {
                        $teamName = substr($originalName, 0, 9) . $counter;
                    }
                    $counter++;
                }
                
                Team::create([
                    'name' => $teamName,
                    'group_id' => $group->id
                ]);
            }
        }
    }
}
