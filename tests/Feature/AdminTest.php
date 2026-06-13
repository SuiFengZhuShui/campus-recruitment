<?php

namespace Tests\Feature;

use App\Models\College;
use App\Models\Enterprise;
use App\Models\EnterpriseDoc;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    private function createAdminUser(): User
    {
        return User::create(['role' => 'school', 'username' => 'admin', 'name' => '管理员', 'phone' => '13800000900', 'password' => Hash::make('password'), 'status' => 'active']);
    }

    // === Admin Login ===

    public function test_admin_login_success()
    {
        $this->createAdminUser();

        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
    }

    public function test_admin_login_wrong_password()
    {
        $this->createAdminUser();

        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_admin_login_non_school_user()
    {
        User::create(['role' => 'student', 'username' => 'student1', 'name' => '学生', 'phone' => '13800000901', 'password' => Hash::make('password'), 'status' => 'active']);

        $response = $this->post('/admin/login', [
            'username' => 'student1',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_admin_dashboard_requires_auth()
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_dashboard_authenticated()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    // === Enterprise Approval ===

    public function test_approve_enterprise()
    {
        $admin = $this->createAdminUser();
        $school = School::create(['name' => '测试大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);

        // Create pending enterprise
        $entUser = User::create(['role' => 'enterprise', 'username' => 'biz1', 'name' => '企业', 'phone' => '13800000902', 'email' => 'biz1@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => '待审公司', 'credit_code' => '91110000900', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000900', 'email' => 'biz1@test.com', 'status' => 'pending']);

        $response = $this->actingAs($admin)->post('/admin/enterprises/' . $enterprise->id . '/approve', [
            'college_id' => $college->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enterprises', ['id' => $enterprise->id, 'status' => 'approved', 'college_id' => $college->id]);
    }

    public function test_approve_requires_college_id()
    {
        $admin = $this->createAdminUser();
        $entUser = User::create(['role' => 'enterprise', 'username' => 'biz2', 'name' => '企业', 'phone' => '13800000903', 'email' => 'biz2@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => '待审公司2', 'credit_code' => '91110000901', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000901', 'email' => 'biz2@test.com', 'status' => 'pending']);

        $response = $this->actingAs($admin)->post('/admin/enterprises/' . $enterprise->id . '/approve', []);

        $response->assertSessionHasErrors();
    }

    public function test_reject_enterprise()
    {
        $admin = $this->createAdminUser();
        $entUser = User::create(['role' => 'enterprise', 'username' => 'biz3', 'name' => '企业', 'phone' => '13800000904', 'email' => 'biz3@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => '驳回公司', 'credit_code' => '91110000902', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000902', 'email' => 'biz3@test.com', 'status' => 'pending']);

        $response = $this->actingAs($admin)->post('/admin/enterprises/' . $enterprise->id . '/reject', [
            'audit_remark' => '资质不符合要求',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enterprises', ['id' => $enterprise->id, 'status' => 'rejected', 'audit_remark' => '资质不符合要求']);
    }

    public function test_cannot_approve_already_approved_enterprise()
    {
        $admin = $this->createAdminUser();
        $school = School::create(['name' => '测试大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);
        $entUser = User::create(['role' => 'enterprise', 'username' => 'biz4', 'name' => '企业', 'phone' => '13800000905', 'email' => 'biz4@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => '已审公司', 'credit_code' => '91110000903', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000903', 'email' => 'biz4@test.com', 'status' => 'approved', 'college_id' => $college->id]);

        $response = $this->actingAs($admin)->post('/admin/enterprises/' . $enterprise->id . '/approve', [
            'college_id' => $college->id,
        ]);

        $response->assertSessionHas('error');
    }

    // === Doc Approval ===

    public function test_approve_single_doc()
    {
        $admin = $this->createAdminUser();
        $entUser = User::create(['role' => 'enterprise', 'username' => 'biz5', 'name' => '企业', 'phone' => '13800000906', 'email' => 'biz5@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => '公司', 'credit_code' => '91110000904', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000904', 'email' => 'biz5@test.com', 'status' => 'pending']);
        $doc = EnterpriseDoc::create(['enterprise_id' => $enterprise->id, 'type' => 'license', 'file_path' => '/path/doc.pdf', 'file_name' => 'doc.pdf', 'status' => 'pending']);

        $response = $this->actingAs($admin)->post('/admin/enterprises/' . $enterprise->id . '/docs/' . $doc->id . '/approve');

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('enterprise_docs', ['id' => $doc->id, 'status' => 'approved']);
    }

    public function test_reject_single_doc()
    {
        $admin = $this->createAdminUser();
        $entUser = User::create(['role' => 'enterprise', 'username' => 'biz6', 'name' => '企业', 'phone' => '13800000907', 'email' => 'biz6@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => '公司', 'credit_code' => '91110000905', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000905', 'email' => 'biz6@test.com', 'status' => 'pending']);
        $doc = EnterpriseDoc::create(['enterprise_id' => $enterprise->id, 'type' => 'id_card', 'file_path' => '/path/id.pdf', 'file_name' => 'id.pdf', 'status' => 'pending']);

        $response = $this->actingAs($admin)->post('/admin/enterprises/' . $enterprise->id . '/docs/' . $doc->id . '/reject', [
            'reject_reason' => '图片不清晰',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('enterprise_docs', ['id' => $doc->id, 'status' => 'rejected', 'reject_reason' => '图片不清晰']);
    }

    public function test_approve_all_docs()
    {
        $admin = $this->createAdminUser();
        $entUser = User::create(['role' => 'enterprise', 'username' => 'biz7', 'name' => '企业', 'phone' => '13800000908', 'email' => 'biz7@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => '公司', 'credit_code' => '91110000906', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000906', 'email' => 'biz7@test.com', 'status' => 'pending']);
        EnterpriseDoc::create(['enterprise_id' => $enterprise->id, 'type' => 'license', 'file_path' => '/path/a.pdf', 'file_name' => 'a.pdf', 'status' => 'pending']);
        EnterpriseDoc::create(['enterprise_id' => $enterprise->id, 'type' => 'id_card', 'file_path' => '/path/b.pdf', 'file_name' => 'b.pdf', 'status' => 'pending']);
        EnterpriseDoc::create(['enterprise_id' => $enterprise->id, 'type' => 'authorization', 'file_path' => '/path/c.pdf', 'file_name' => 'c.pdf', 'status' => 'pending']);

        $this->actingAs($admin)->post('/admin/enterprises/' . $enterprise->id . '/docs/approve-all');

        $this->assertEquals(3, EnterpriseDoc::where('enterprise_id', $enterprise->id)->where('status', 'approved')->count());
    }
}
