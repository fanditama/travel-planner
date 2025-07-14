<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destination = Destination::query()->limit(1)->first();
        
        Tag::create([
            'destination_id' => $destination->id,
            'tag_name' => 'Makanan'
        ]);
    }
}
