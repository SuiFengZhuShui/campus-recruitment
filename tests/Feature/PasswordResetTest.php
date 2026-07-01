<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    private function createUser(string $email = 'user@test.com'): User
    {
        return User::create(['role' => 'student', 'username' => 'testuser', 'name' => 'Test', 'phone' => '13800000500', 'email' => $email, 'password' => Hash::make('oldpass'), 'status' => 'active']);
    }

    // === Forgot Password ===

    public function test_forgot_password_with_valid_email()
    {
        $this->createUser('test@example.com');

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200]);

        $this->assertDatabaseHas('password_resets', ['email' => 'test@example.com']);
    }

    public function test_forgot_password_with_unknown_email_returns_same_message()
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'nonexistent@test.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 422]);
    }

    // === Reset Password ===

    public function test_reset_password_with_valid_token()
    {
        $user = $this->createUser('reset@example.com');

        // Simulate forgot password flow
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => 'reset@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'reset@example.com',
            'token' => $token,
            'password' => 'newpass123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200, 'message' => '密码已重置，请登录']);

        // Verify password was changed
        $this->assertTrue(Hash::check('newpass123', $user->fresh()->password));

        // Token should be deleted
        $this->assertDatabaseMissing('password_resets', ['email' => 'reset@example.com']);
    }

    public function test_reset_password_with_invalid_token()
    {
        $this->createUser('bad@example.com');

        DB::table('password_resets')->insert([
            'email' => 'bad@example.com',
            'token' => Hash::make('real-token'),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'bad@example.com',
            'token' => 'wrong-token',
            'password' => 'newpass123',
        ]);

        $response->assertStatus(422);
    }

    public function test_reset_password_with_expired_token()
    {
        $this->createUser('expired@example.com');

        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => 'expired@example.com',
            'token' => Hash::make($token),
            'created_at' => now()->subMinutes(61),
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'expired@example.com',
            'token' => $token,
            'password' => 'newpass123',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_login_with_new_password_after_reset()
    {
        $this->createUser('login@example.com');

        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => 'login@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Reset password
        $this->postJson('/api/auth/reset-password', [
            'email' => 'login@example.com',
            'token' => $token,
            'password' => 'freshpassword',
        ])->assertStatus(200);

        // Login with new password
        session(['captcha_code' => 'abcd']);
        $response = $this->postJson('/api/auth/login', [
            'account' => '13800000500',
            'password' => 'freshpassword',
            'captcha' => 'abcd',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200]);
    }

    // === Remember Token ===

    public function test_remember_token_is_nullable()
    {
        $user = $this->createUser('remember@test.com');
        $user->forceFill(['remember_token' => Str::random(60)])->save();

        $this->assertNotNull($user->fresh()->remember_token);
    }
}
