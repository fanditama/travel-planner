<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Destination::create([
            'name' => 'Test Destination',
            'location' => 'Jakarta',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'category' => 'Kuliner',
            'average_rating' => 4.5,
            'image_url' => 'https://example.com/image.jpg',
            'approx_price_range' => 'Terjangkau',
            'best_time_to_visit' => 'April hingga Oktober',
        ]);
    }
}
