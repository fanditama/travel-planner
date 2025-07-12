<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\User;
use Database\Seeders\DestinationSearchSeeder;
use Database\Seeders\DestinationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DestinationTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->post('/api/destinations',
            [
                'name' => 'Test Destination',
                'location' => 'Jakarta',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'category' => 'Kuliner',
                'average_rating' => 4.5,
                'image_url' => 'https://example.com/image.jpg',
                'approx_price_range' => 'Terjangkau',
                'best_time_to_visit' => 'April hingga Oktober',
            ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'Test Destination',
                    'location' => 'Jakarta',
                    'latitude' => -6.2088,
                    'longitude' => 106.8456,
                    'category' => 'Kuliner',
                    'average_rating' => 4.5,
                    'image_url' => 'https://example.com/image.jpg',
                    'approx_price_range' => 'Terjangkau',
                    'best_time_to_visit' => 'April hingga Oktober',
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->post('/api/destinations',
            [
                'name' => '',
                'location' => '',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'category' => 'Kuliner',
                'average_rating' => 4.5,
                'image_url' => 'https://example.com/image.jpg',
                'approx_price_range' => 'Terjangkau',
                'best_time_to_visit' => 'April hingga Oktober',
            ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ],
                    'location' => [
                        'The location field is required.'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $destination = Destination::create([
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

        $this->get('/api/destinations/' . $destination->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Test Destination',
                    'location' => 'Jakarta',
                    'latitude' => -6.2088,
                    'longitude' => 106.8456,
                    'category' => 'Kuliner',
                    'average_rating' => 4.5,
                    'image_url' => 'https://example.com/image.jpg',
                    'approx_price_range' => 'Terjangkau',
                    'best_time_to_visit' => 'April hingga Oktober',
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->get('/api/destinations/99999')
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([DestinationSeeder::class]);

        $destination = Destination::query()->limit(1)->first();

        $this->put('/api/destinations/' . $destination->id, [
            'name' => 'Test Destination 2',
            'location' => 'Surabaya',
            'latitude' => -7.2574,
            'longitude' => 112.7521,
            'category' => 'Santai',
            'average_rating' => 4.2,
            'image_url' => 'https://example.com/image2.jpg',
            'approx_price_range' => 'Premium',
            'best_time_to_visit' => 'November hingga Februari',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Test Destination 2',
                    'location' => 'Surabaya',
                    'latitude' => -7.2574,
                    'longitude' => 112.7521,
                    'category' => 'Santai',
                    'average_rating' => 4.2,
                    'image_url' => 'https://example.com/image2.jpg',
                    'approx_price_range' => 'Premium',
                    'best_time_to_visit' => 'November hingga Februari',
                ]
            ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([DestinationSeeder::class]);

        $destination = Destination::query()->limit(1)->first();

        $this->put('/api/destinations/' . $destination->id, [
            'name' => '',
            'location' => 'Surabaya',
            'latitude' => -7.2574,
            'longitude' => 112.7521,
            'category' => 'Santai',
            'average_rating' => 4.2,
            'image_url' => 'https://example.com/image2.jpg',
            'approx_price_range' => 'Premium',
            'best_time_to_visit' => 'November hingga Februari',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    public function testDeleteSucces() {
        $this->seed([DestinationSeeder::class]);
        
        $destination = Destination::query()->limit(1)->first();

        $this->delete('/api/destinations/' . $destination->id, [])
            ->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([DestinationSeeder::class]);

        $this->delete('/api/destinations/99999')
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }

    public function testSearchByDestinationName()
    {
        $this->seed([DestinationSearchSeeder::class]);

        $response = $this->get('/api/destinations?name=destination_name')
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);

    }

    public function testSearchByDestinationCategory()
    {
        $this->seed([DestinationSearchSeeder::class]);

        $response = $this->get('/api/destinations?category=kuliner')
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);

    }
}
