<?php

namespace Tests\Feature;

use App\Models\College;
use App\Models\Enterprise;
use App\Models\Job;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CollegeAdminTest extends TestCase
{
    private $collegeAdmin, $college, $schoolAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $school = School::create(['name' => '测试大学']);
        $this->college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);

        $this->schoolAdmin = User::create(['role' => 'school', 'username' => 'admin_s', 'name' => '学校管理员', 'phone' => '13800000900', 'password' => Hash::make('password'), 'status' => 'active']);

        $this->collegeAdmin = User::create(['role' => 'college', 'username' => 'college_a', 'name' => '学院管理员', 'college_id' => $this->college->id, 'phone' => '13800000901', 'password' => Hash::make('password'), 'status' => 'active']);
    }

    // === Auth ===

    public function test_college_admin_can_login()
    {
        session(['captcha_code' => 'abcd']);

        $response = $this->postJson('/api/auth/login', [
            'account' => 'college_a',
            'password' => 'password',
            'captcha' => 'abcd',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.role', 'college');
    }

    public function test_college_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->collegeAdmin)->get('/admin/college');

        $response->assertStatus(200);
    }

    public function test_college_admin_cannot_access_school_routes()
    {
        $response = $this->actingAs($this->collegeAdmin)->get('/admin/enterprises');

        $response->assertStatus(403);
    }

    public function test_school_admin_can_access_college_routes()
    {
        $response = $this->actingAs($this->schoolAdmin)->get('/admin/college');

        $response->assertStatus(200);
    }

    // === College stats ===

    public function test_college_dashboard_shows_stats()
    {
        // Create a student in this college
        $stuUser = User::create(['role' => 'student', 'username' => 'stu_c', 'name' => '学生', 'phone' => '13800000902', 'college_id' => $this->college->id, 'email' => 'stuc@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        Student::create(['user_id' => $stuUser->id, 'student_no' => '20240001', 'class_name' => '计科1班', 'grade' => '2024', 'college_id' => $this->college->id]);

        $response = $this->actingAs($this->collegeAdmin)->get('/admin/college');

        $response->assertStatus(200)
            ->assertSee('1'); // student count
    }

    public function test_college_students_list()
    {
        $stuUser = User::create(['role' => 'student', 'username' => 'stu2', 'name' => '学生李', 'phone' => '13800000903', 'college_id' => $this->college->id, 'email' => 'stu2@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        Student::create(['user_id' => $stuUser->id, 'student_no' => '20240002', 'class_name' => '计科2班', 'grade' => '2024', 'college_id' => $this->college->id]);

        $response = $this->actingAs($this->collegeAdmin)->get('/admin/college/students');

        $response->assertStatus(200)
            ->assertSee('学生李');
    }
}
