<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesAndBreeds extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Dog' => [
                'Labrador Retriever',
                'German Shepherd',
                'Golden Retriever',
                'Bulldog',
                'Poodle',
                'Maltese',
            ],
            'Cat' => [
                'Persian',
                'Maine Coon',
                'British Shorthair',
                'Siamese',
                'Ragdoll',
            ],
            'Rabbit' => [
                'Netherland Dwarf',
                'Lop-Eared',
            ],
            'Horse' => [
                'Arabian',
                'Lipizzaner',
            ],
        ];

        foreach ($data as $speciesName => $breeds) {
            $species = Species::firstOrCreate(['name' => $speciesName]);

            foreach ($breeds as $breedName) {
                Breed::firstOrCreate([
                    'species_id' => $species->id,
                    'name' => $breedName,
                ]);
            }
        }
    }
}
