<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CountryCitySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Egypt'],
            ['name' => 'Saudi Arabia'],
            ['name' => 'United Arab Emirates'],
            ['name' => 'Jordan'],
            ['name' => 'Lebanon'],
            ['name' => 'Morocco'],
            ['name' => 'Tunisia'],
            ['name' => 'Algeria'],
            ['name' => 'Palestine'],
            ['name' => 'Iraq'],
        ];
        
        foreach ($countries as $country) {
            Country::create($country);
        }
        
        $cities = [
            ['name' => 'Cairo', 'country_id' => 1],
            ['name' => 'Alexandria', 'country_id' => 1],
            ['name' => 'Giza', 'country_id' => 1],
            ['name' => 'Luxor', 'country_id' => 1],
            ['name' => 'Riyadh', 'country_id' => 2],
            ['name' => 'Jeddah', 'country_id' => 2],
            ['name' => 'Mecca', 'country_id' => 2],
            ['name' => 'Medina', 'country_id' => 2],
            ['name' => 'Dammam', 'country_id' => 2],
            ['name' => 'Dubai', 'country_id' => 3],
            ['name' => 'Abu Dhabi', 'country_id' => 3],
            ['name' => 'Sharjah', 'country_id' => 3],
            ['name' => 'Amman', 'country_id' => 4],
            ['name' => 'Zarqa', 'country_id' => 4],
            ['name' => 'Beirut', 'country_id' => 5],
            ['name' => 'Tripoli', 'country_id' => 5],
            ['name' => 'Casablanca', 'country_id' => 6],
            ['name' => 'Rabat', 'country_id' => 6],
            ['name' => 'Marrakech', 'country_id' => 6],
            ['name' => 'Tunis', 'country_id' => 7],
            ['name' => 'Sfax', 'country_id' => 7],
            ['name' => 'Algiers', 'country_id' => 8],
            ['name' => 'Oran', 'country_id' => 8],
            ['name' => 'Ramallah', 'country_id' => 9],
            ['name' => 'Gaza', 'country_id' => 9],
            ['name' => 'Baghdad', 'country_id' => 10],
            ['name' => 'Basra', 'country_id' => 10],
        ];
        
        foreach ($cities as $city) {
            City::create($city);
        }
    }
}