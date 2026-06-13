<?php

namespace Tests\Feature;

use App\Models\College;
use App\Models\School;
use App\Models\StudentIdRule;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // === Login ===

    public function test_login_with_phone()
    {
        User::create(['role' => 'student', 'username' => 'testuser', 'name' => 'Test', 'phone' => '13800000001', 'email' => 'test@test.com', 'password' => Hash::make('password'), 'status' => 'active']);

        // Set captcha in session
        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/login', [
            'account' => '13800000001',
            'password' => 'password',
            'captcha' => 'abcd',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.role', 'student');
    }

    public function test_login_with_username()
    {
        User::create(['role' => 'student', 'username' => 'myuser', 'name' => 'Test', 'phone' => '13800000002', 'email' => 'test2@test.com', 'password' => Hash::make('password'), 'status' => 'active']);

        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/login', [
            'account' => 'myuser',
            'password' => 'password',
            'captcha' => 'abcd',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200]);
    }

    public function test_login_with_email()
    {
        User::create(['role' => 'student', 'username' => 'user3', 'name' => 'Test', 'phone' => '13800000003', 'email' => 'user3@test.com', 'password' => Hash::make('password'), 'status' => 'active']);

        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/login', [
            'account' => 'user3@test.com',
            'password' => 'password',
            'captcha' => 'abcd',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200]);
    }

    public function test_login_with_wrong_password()
    {
        User::create(['role' => 'student', 'username' => 'user4', 'name' => 'Test', 'phone' => '13800000004', 'email' => 'a@b.com', 'password' => Hash::make('correct'), 'status' => 'active']);

        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/login', [
            'account' => '13800000004',
            'password' => 'wrong',
            'captcha' => 'abcd',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_disabled_user()
    {
        User::create(['role' => 'student', 'username' => 'disabled', 'name' => 'Test', 'phone' => '13800000005', 'email' => 'd@b.com', 'password' => Hash::make('password'), 'status' => 'disabled']);

        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/login', [
            'account' => '13800000005',
            'password' => 'password',
            'captcha' => 'abcd',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_requires_captcha()
    {
        $response = $this->postJson('/api/auth/login', [
            'account' => '13800000001',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_invalid_captcha()
    {
        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/login', [
            'account' => '13800000001',
            'password' => 'password',
            'captcha' => 'wrong',
        ]);

        $response->assertStatus(422);
    }

    // === Register Student ===

    public function test_register_student()
    {
        $school = School::create(['name' => '测试大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);
        StudentIdRule::create(['school_id' => $school->id, 'prefix' => '2024', 'college_id' => $college->id]);

        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/register/student', [
            'username' => 'newstudent',
            'email' => 'new@test.com',
            'student_no' => '20240001',
            'class_name' => '计科1班',
            'name' => '张三',
            'phone' => '13800000010',
            'password' => 'password123',
            'captcha' => 'abcd',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.role', 'student');

        $this->assertDatabaseHas('users', ['username' => 'newstudent', 'phone' => '13800000010']);
        $this->assertDatabaseHas('students', ['student_no' => '20240001']);
    }

    public function test_register_student_duplicate_username()
    {
        User::create(['role' => 'student', 'username' => 'existing', 'name' => 'Old', 'phone' => '13800000011', 'email' => 'old@test.com', 'password' => Hash::make('pass'), 'status' => 'active']);

        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/register/student', [
            'username' => 'existing',
            'email' => 'new@test.com',
            'student_no' => '20240001',
            'class_name' => '计科1班',
            'name' => '张三',
            'phone' => '13800000012',
            'password' => 'password123',
            'captcha' => 'abcd',
        ]);

        $response->assertStatus(422);
    }

    // === Register Enterprise ===

    public function test_register_enterprise_requires_docs()
    {
        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/register/enterprise', [
            'username' => 'newbiz',
            'name' => '测试公司',
            'credit_code' => '91110000MA01',
            'industry' => 'IT',
            'contact_name' => '李四',
            'contact_phone' => '13900000001',
            'email' => 'biz@test.com',
            'phone' => '13900000002',
            'password' => 'password123',
            'captcha' => 'abcd',
            // Missing doc_license, doc_id_card, doc_authorization
        ]);

        $response->assertStatus(422);
    }

    // === Logout ===

    public function test_logout()
    {
        $user = User::create(['role' => 'student', 'username' => 'user5', 'name' => 'Test', 'phone' => '13800000020', 'email' => 'e@b.com', 'password' => Hash::make('password'), 'status' => 'active']);

        $response = $this->actingAs($user)->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['code' => 200]);
    }

    // === Me ===

    public function test_me_authenticated()
    {
        $user = User::create(['role' => 'student', 'username' => 'user6', 'name' => 'Test', 'phone' => '13800000021', 'email' => 'f@b.com', 'password' => Hash::make('password'), 'status' => 'active']);

        $response = $this->actingAs($user)->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.username', 'user6');
    }

    public function test_me_unauthenticated()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_me_enterprise_with_profile()
    {
        $user = User::create(['role' => 'enterprise', 'username' => 'ent1', 'name' => 'Biz', 'phone' => '13800000022', 'email' => 'g@b.com', 'password' => Hash::make('password'), 'status' => 'active']);
        \App\Models\Enterprise::create(['user_id' => $user->id, 'name' => 'My Corp', 'credit_code' => '91110007', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000008', 'email' => 'g@b.com', 'status' => 'pending']);

        $response = $this->actingAs($user)->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.enterprise.name', 'My Corp');
    }
}
