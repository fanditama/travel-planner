<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
