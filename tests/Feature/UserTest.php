<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
                    'name' => 'test'
                ]
            ])
            ->assertJsonStructure([
                'data' => [
                    'email',
                    'name'
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
}
