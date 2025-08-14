<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            'Area Padel 1',
            'Area Padel 2',
            'Area Padel 3',
            'Area Padel 4',
            'Area Padel 5',
        ];

        foreach ($fields as $fieldName) {
            Field::create([
                'name' => $fieldName
            ]);
        }
    }
}
