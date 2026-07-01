<?php

namespace Tests\Feature;

use App\Models\College;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CollegeAdminManageTest extends TestCase
{
    private function createSchoolAdmin(): User
    {
        return User::create(['role' => 'school', 'username' => 'sa_' . uniqid(), 'name' => '学校管理员', 'phone' => '138' . rand(10000000, 99999999), 'password' => Hash::make('password'), 'status' => 'active']);
    }

    private function createCollege(): College
    {
        $school = School::create(['name' => '测试大学' . uniqid()]);
        return College::create(['school_id' => $school->id, 'name' => '计算机学院']);
    }

    // === List ===

    public function test_college_admin_list()
    {
        $admin = $this->createSchoolAdmin();
        $college = $this->createCollege();
        User::create(['role' => 'college', 'username' => 'ca_test', 'name' => '学院管理员', 'phone' => '13800001101', 'email' => 'ca@test.com', 'college_id' => $college->id, 'password' => Hash::make('password'), 'status' => 'active']);

        $response = $this->actingAs($admin)->get('/admin/admins');

        $response->assertStatus(200)
            ->assertSee('ca_test')
            ->assertSee('学院管理员');
    }

    public function test_college_admin_list_search()
    {
        $admin = $this->createSchoolAdmin();
        $college = $this->createCollege();
        User::create(['role' => 'college', 'username' => 'searchme', 'name' => '张三', 'phone' => '13800001102', 'email' => 'ca2@test.com', 'college_id' => $college->id, 'password' => Hash::make('password'), 'status' => 'active']);

        $response = $this->actingAs($admin)->get('/admin/admins?search=张三');

        $response->assertStatus(200)
            ->assertSee('张三');
    }

    // === Create ===

    public function test_create_college_admin()
    {
        $admin = $this->createSchoolAdmin();
        $college = $this->createCollege();

        $response = $this->actingAs($admin)->post('/admin/admins', [
            'username' => 'new_ca',
            'name' => '新管理员',
            'phone' => '13800001103',
            'email' => 'newca@test.com',
            'college_id' => $college->id,
            'password' => 'password123',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['username' => 'new_ca', 'role' => 'college']);
    }

    // === Edit ===

    public function test_edit_college_admin()
    {
        $admin = $this->createSchoolAdmin();
        $college = $this->createCollege();
        $ca = User::create(['role' => 'college', 'username' => 'editme', 'name' => '旧名', 'phone' => '13800001104', 'email' => 'editme@test.com', 'college_id' => $college->id, 'password' => Hash::make('password'), 'status' => 'active']);

        $response = $this->actingAs($admin)->put('/admin/admins/' . $ca->id, [
            'username' => 'editme',
            'name' => '新名',
            'phone' => '13800001104',
            'email' => 'editme@test.com',
            'college_id' => $college->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['id' => $ca->id, 'name' => '新名']);
    }

    // === Toggle ===

    public function test_toggle_college_admin()
    {
        $admin = $this->createSchoolAdmin();
        $college = $this->createCollege();
        $ca = User::create(['role' => 'college', 'username' => 'togme', 'name' => '待禁用', 'phone' => '13800001105', 'email' => 'togme@test.com', 'college_id' => $college->id, 'password' => Hash::make('password'), 'status' => 'active']);

        $this->actingAs($admin)->post('/admin/admins/' . $ca->id . '/toggle');
        $this->assertDatabaseHas('users', ['id' => $ca->id, 'status' => 'disabled']);

        $this->actingAs($admin)->post('/admin/admins/' . $ca->id . '/toggle');
        $this->assertDatabaseHas('users', ['id' => $ca->id, 'status' => 'active']);
    }

    // === Delete ===

    public function test_delete_college_admin()
    {
        $admin = $this->createSchoolAdmin();
        $college = $this->createCollege();
        $ca = User::create(['role' => 'college', 'username' => 'delme', 'name' => '待删除', 'phone' => '13800001106', 'email' => 'delme@test.com', 'college_id' => $college->id, 'password' => Hash::make('password'), 'status' => 'active']);

        $response = $this->actingAs($admin)->delete('/admin/admins/' . $ca->id);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $ca->id]);
    }

    // === Permission ===

    public function test_college_user_cannot_access_admins()
    {
        $college = $this->createCollege();
        $collegeUser = User::create(['role' => 'college', 'username' => 'ca_perm', 'name' => '学院', 'phone' => '13800001107', 'email' => 'cap@test.com', 'college_id' => $college->id, 'password' => Hash::make('password'), 'status' => 'active']);

        $response = $this->actingAs($collegeUser)->get('/admin/admins');

        $response->assertStatus(403);
    }
}
