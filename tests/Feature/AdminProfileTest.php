<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    private function createAdmin(string $role = 'school'): User
    {
        return User::create(['role' => $role, 'username' => 'prof_' . uniqid(), 'name' => '管理员', 'phone' => '138' . rand(10000000, 99999999), 'password' => Hash::make('oldpassword'), 'status' => 'active']);
    }

    public function test_school_admin_can_see_profile_page()
    {
        $admin = $this->createAdmin('school');

        $response = $this->actingAs($admin)->get('/admin/profile');

        $response->assertStatus(200)
            ->assertSee('修改密码');
    }

    public function test_college_admin_can_see_profile_page()
    {
        $admin = $this->createAdmin('college');

        $response = $this->actingAs($admin)->get('/admin/profile');

        $response->assertStatus(200)
            ->assertSee('修改密码');
    }

    public function test_change_password_success()
    {
        $admin = $this->createAdmin('school');

        $response = $this->actingAs($admin)->put('/admin/profile/password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('newpassword123', $admin->fresh()->password));
    }

    public function test_change_password_wrong_current()
    {
        $admin = $this->createAdmin('school');

        $response = $this->actingAs($admin)->put('/admin/profile/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors();
    }
}
