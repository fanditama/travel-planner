<?php

namespace Tests\Feature;

use App\Models\User;
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

    /**
     * Update User Test
     */
    public function testUpdatePasswordSuccess() 
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('email', 'test@example.com')->first();

        // Login terlebih dahulu untuk mendapatkan token
        $loginResponse = $this->post('/api/users/login', [
            'email' => 'test@example.com',
            'password' => 'test'
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');

        $this->put('/api/users/current', 
            [
                'password' => 'baru'
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@example.com',
                    'name' => 'test',
                    'preferred_activity' => 'hiking',
                    'preferred_travel_style' => 'backpacking',
                    'home_location' => 'Jakarta',
                ]
            ]);

        $newUser = User::where('email', 'test@example.com')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess() 
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('email', 'test@example.com')->first();

        // Login terlebih dahulu untuk mendapatkan token
        $loginResponse = $this->post('/api/users/login', [
            'email' => 'test@example.com',
            'password' => 'test'
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');

        $this->put('/api/users/current', 
            [
                'name' => 'baru'
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@example.com',
                    'name' => 'baru',
                    'preferred_activity' => 'hiking',
                    'preferred_travel_style' => 'backpacking',
                    'home_location' => 'Jakarta',
                ]
            ]);

        $newUser = User::where('email', 'test@example.com')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdatePreferredActivitySuccess() 
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('email', 'test@example.com')->first();

        // Login terlebih dahulu untuk mendapatkan token
        $loginResponse = $this->post('/api/users/login', [
            'email' => 'test@example.com',
            'password' => 'test'
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');

        $this->put('/api/users/current', 
            [
                'preferred_activity' => 'baru'
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@example.com',
                    'name' => 'test',
                    'preferred_activity' => 'baru',
                    'preferred_travel_style' => 'backpacking',
                    'home_location' => 'Jakarta',
                ]
            ]);

        $newUser = User::where('email', 'test@example.com')->first();
        self::assertNotEquals($oldUser->preferred_activity, $newUser->preferred_activity);
    }

    public function testUpdatePreferredTravelStyleSuccess() 
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('email', 'test@example.com')->first();

        // Login terlebih dahulu untuk mendapatkan token
        $loginResponse = $this->post('/api/users/login', [
            'email' => 'test@example.com',
            'password' => 'test'
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');

        $this->put('/api/users/current', 
            [
                'preferred_travel_style' => 'baru'
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@example.com',
                    'name' => 'test',
                    'preferred_activity' => 'hiking',
                    'preferred_travel_style' => 'baru',
                    'home_location' => 'Jakarta',
                ]
            ]);

        $newUser = User::where('email', 'test@example.com')->first();
        self::assertNotEquals($oldUser->preferred_travel_style, $newUser->preferred_travel_style);
    }

    public function testUpdateHomeLocationSuccess() 
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('email', 'test@example.com')->first();

        // Login terlebih dahulu untuk mendapatkan token
        $loginResponse = $this->post('/api/users/login', [
            'email' => 'test@example.com',
            'password' => 'test'
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');

        $this->put('/api/users/current', 
            [
                'home_location' => 'baru'
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@example.com',
                    'name' => 'test',
                    'preferred_activity' => 'hiking',
                    'preferred_travel_style' => 'backpacking',
                    'home_location' => 'baru',
                ]
            ]);

        $newUser = User::where('email', 'test@example.com')->first();
        self::assertNotEquals($oldUser->home_location, $newUser->home_location);
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);

        // Login terlebih dahulu untuk mendapatkan token
        $loginResponse = $this->post('/api/users/login', [
            'email' => 'test@example.com',
            'password' => 'test'
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);

        // Login terlebih dahulu untuk mendapatkan token
        $loginResponse = $this->post('/api/users/login', [
            'email' => 'test@example.com',
            'password' => 'test'
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'salah' . $token
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    "message" => [
                        'unauthorized'
                    ]
                ]
            ]);
    }
}
