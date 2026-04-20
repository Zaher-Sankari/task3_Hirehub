<?php
namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $syria = Country::where('name', 'Syria')->first();
        $nz = Country::where('name', 'New Zealand')->first();

        City::insert([
            ['country_id' => $syria->id, 'name' => 'damascus'],
            ['country_id' => $syria->id, 'name' => 'latakia'],
            ['country_id' => $nz->id, 'name' => 'auckland'],
        ]);
    }
}