<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Register User Test
     */
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'email' => 'test@example.com',
            'password' => 'rahasia',
            'name' => 'Test Register'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'email' => 'test@example.com',
                    'name' => 'Test Register'
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'email' => '',
            'password' => '',
            'name' => ''
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'The email field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ],
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    public function testRegisterEmailAlreadyExists()
    {
        $this->testRegisterSuccess();

        $this->post('/api/users', [
            'email' => 'test@example.com',
            'password' => 'rahasia',
            'name' => 'Test User'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'email already registered'
                    ]
                ]
            ]);
    }

    /**
     * Login User Test
     */
    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            'email' => 'test@example.com',
            'password' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@example.com',
                    'name' => 'test',
                    'preferred_activity' => 'hiking',
                    'preferred_travel_style' => 'backpacking',
                    'home_location' => 'Jakarta'
                ]
            ])
            ->assertJsonStructure([
                'data' => [
                    'email',
                    'name',
                    'preferred_activity',
                    'preferred_travel_style',
                    'home_location'
                ],
                'token'
            ]);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            'email' => 'test',
            'password' => 'test'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'email or password wrong'
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            'email' => 'test@example.com',
            'password' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'email or password wrong'
                    ]
                ]
            ]);
    }

    /**
     * Get User Test
     */
    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);

        // Login terlebih dahulu untuk mendapatkan token
        $loginResponse = $this->post('/api/users/login', [
            'email' => 'test@example.com',
            'password' => 'test'
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');

        // Gunakan token untuk mengakses endpoint yang dilindungi
        $this->get('/api/users/current', [
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@example.com',
                    'name' => 'test',
                    'preferred_activity' => 'hiking',
                    'preferred_travel_style' => 'backpacking',
                    'home_location' => 'Jakarta'
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'Bearer'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }
}
