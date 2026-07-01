<?php

namespace Tests\Feature;

use App\Models\College;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CollegeCrudTest extends TestCase
{
    private function createSchoolAdmin(): User
    {
        return User::create(['role' => 'school', 'username' => 'admin_coll', 'name' => '管理员', 'phone' => '13810000001', 'password' => Hash::make('password'), 'status' => 'active']);
    }

    private function createSchool(): School
    {
        return School::firstOrCreate(['name' => '测试学校'], ['short_name' => '测试']);
    }

    // === College CRUD ===

    public function test_admin_can_create_college()
    {
        $admin = $this->createSchoolAdmin();
        $school = $this->createSchool();

        $response = $this->actingAs($admin)->post('/admin/colleges', [
            'name' => '新学院',
            'school_id' => $school->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('colleges', ['name' => '新学院']);
    }

    public function test_admin_can_update_college()
    {
        $admin = $this->createSchoolAdmin();
        $school = $this->createSchool();
        $college = College::create(['school_id' => $school->id, 'name' => '原学院']);

        $response = $this->actingAs($admin)->put('/admin/colleges/' . $college->id, [
            'name' => '新名称',
            'school_id' => $school->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('colleges', ['id' => $college->id, 'name' => '新名称']);
    }

    public function test_admin_can_delete_college_without_enterprises()
    {
        $admin = $this->createSchoolAdmin();
        $school = $this->createSchool();
        $college = College::create(['school_id' => $school->id, 'name' => '待删学院']);

        $response = $this->actingAs($admin)->delete('/admin/colleges/' . $college->id);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('colleges', ['id' => $college->id]);
    }

    public function test_college_list_page_loads()
    {
        $admin = $this->createSchoolAdmin();
        $school = $this->createSchool();
        College::create(['school_id' => $school->id, 'name' => '学院A']);

        $response = $this->actingAs($admin)->get('/admin/colleges');

        $response->assertStatus(200)
            ->assertSee('学院A');
    }
}
