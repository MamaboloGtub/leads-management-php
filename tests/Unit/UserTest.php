<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_implements_jwt_subject(): void
    {
        $user = new User();
        
        $this->assertInstanceOf(\Tymon\JWTAuth\Contracts\JWTSubject::class, $user);
    }

    public function test_user_can_get_jwt_identifier(): void
    {
        $user = User::factory()->create();
        
        $this->assertEquals($user->id, $user->getJWTIdentifier());
    }

    public function test_user_can_get_jwt_custom_claims(): void
    {
        $user = new User();
        
        $this->assertIsArray($user->getJWTCustomClaims());
        $this->assertEmpty($user->getJWTCustomClaims());
    }

    public function test_user_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'password' => 'plaintext-password'
        ]);

        $this->assertNotEquals('plaintext-password', $user->password);
        $this->assertTrue(\Hash::check('plaintext-password', $user->password));
    }

    public function test_user_has_fillable_attributes(): void
    {
        $user = new User();
        $expectedFillable = ['name', 'email', 'password'];

        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    public function test_user_hides_sensitive_attributes(): void
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    public function test_user_email_verification_timestamp_is_cast_to_datetime(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $user->email_verified_at);
    }
}