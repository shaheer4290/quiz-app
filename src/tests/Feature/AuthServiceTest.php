<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testRequiresEmailAndLogin()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ],
            ]);
    }

    public function testUserLoginsSuccessfully()
    {
        $user = User::factory()->create([
            'email' => 'test.user@user.com',
            'password' => bcrypt('123456'),
        ]);

        $payload = ['email' => 'test.user@user.com', 'password' => '123456'];

        $response = $this->postJson('/api/login', $payload);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'access_token',
                ],
            ]);
    }

    public function testsRegistersSuccessfully()
    {
        $payload = [
            'name' => 'John',
            'email' => 'john@gmail.com',
            'password' => '123456',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'User Successfully registered',
            ]);
    }

    public function testsRequiresPasswordEmailAndName()
    {
        $payload = [];
        $response = $this->postJson('/api/register', $payload);

        $response
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'],
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ],
            ]);
    }
}
