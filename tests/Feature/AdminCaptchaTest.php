<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCaptchaTest extends TestCase
{
    private function createAdmin()
    {
        return User::create(['role' => 'school', 'username' => 'admin_c', 'name' => '管理员', 'phone' => '13800000950', 'password' => Hash::make('password'), 'status' => 'active']);
    }

    public function test_admin_login_requires_captcha()
    {
        $this->createAdmin();

        $response = $this->postJson('/api/auth/login', [
            'account' => 'admin_c',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_login_fails_with_wrong_captcha()
    {
        $this->createAdmin();
        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/login', [
            'account' => 'admin_c',
            'password' => 'password',
            'captcha' => 'wrong',
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_login_succeeds_with_correct_captcha()
    {
        $this->createAdmin();
        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/login', [
            'account' => 'admin_c',
            'password' => 'password',
            'captcha' => 'abcd',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.role', 'school');
    }
}
