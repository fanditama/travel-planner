<?php

namespace Tests\Feature;

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
}
