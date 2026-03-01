<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Loyer',
            'Électricité',
            'Gaz',
            'Internet',
            'Courses Alimentaires',
            'Produits d\'entretien',
            'Sorties',
            'Loisirs',
            'Divers'
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat,
                'coloc_id' => null,
            ]);
        }
    }
}
