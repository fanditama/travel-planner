<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
