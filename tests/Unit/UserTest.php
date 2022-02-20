<?php

namespace Tests\Unit;

use App\Models\User;
use App\Http\Controllers\AuthController;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;



class UserTest extends TestCase
{
    /**
     * Test User Registration
     */
    public function testSuccessfulRegistration()
    {
        $userData = [
            "name" => "John Doe",
            "email" => "email@example.com",
            "password" => "test@123",
            "password_confirmation" => "test@123"
        ];
        $response = $this->post('/api/register', $userData);

        $response->assertJsonStructure([
            "status"
        ]);
    }

    public function testEmailAndPasswordError()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(422)
            ->assertJson([
                "status" => "Error",
                "message" => [
                    'email' => ["The email field is required."],
                    'password' => ["The password field is required."],
                ]
            ]);
    }
    public function testSuccessfulLogin()
    {

        $user = User::factory()->make();
        $this->json('POST', 'api/login', (array)$user, ['Accept' => 'application/json'])
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [],
            ]);
    }

    public function testApplyLoan()
    {

        $data = ['amount' => 450000, 'tenure' => 10];
        $this->json('POST', 'api/apply', $data, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "status",
            ]);
    }

    public function testPayment()
    {

        $data = ['amount' => 450000, 'lan_number' => "LAN57A7959A"];
        $this->json('POST', 'api/paynow', $data, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "status"
            ]);
    }

    public function testApprove()
    {

        $data = ['status' => 1, 'lan_number' => "LAN57A7959A"];
        $this->json('POST', 'api/loanStatus', $data, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "status"
            ]);
    }
}
