<?php

namespace Tests\Feature;

use App\Models\College;
use App\Models\Enterprise;
use App\Models\EnterpriseDoc;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EnterpriseManageTest extends TestCase
{
    private function createSchoolAdmin(): User
    {
        return User::create(['role' => 'school', 'username' => 'admin_ent', 'name' => '管理员', 'phone' => '13800001001', 'password' => Hash::make('password'), 'status' => 'active']);
    }

    private function createEnterprise(string $status = 'pending', string $name = '测试公司'): Enterprise
    {
        $user = User::create(['role' => 'enterprise', 'username' => 'biz_' . uniqid(), 'name' => '企业', 'phone' => '138' . rand(10000000, 99999999), 'email' => uniqid() . '@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        return Enterprise::create(['user_id' => $user->id, 'name' => $name, 'credit_code' => '91110000' . rand(1000, 9999), 'industry' => '互联网', 'contact_name' => '张三', 'contact_phone' => '1390000' . rand(1000, 9999), 'email' => $user->email, 'status' => $status]);
    }

    // === Enterprise List ===

    public function test_enterprise_list_shows_all_statuses()
    {
        $admin = $this->createSchoolAdmin();
        $this->createEnterprise('pending', '待审公司A');
        $this->createEnterprise('approved', '已审公司B');

        $response = $this->actingAs($admin)->get('/admin/enterprises?status=all');

        $response->assertStatus(200)
            ->assertSee('待审公司A')
            ->assertSee('已审公司B');
    }

    public function test_enterprise_list_filter_by_pending()
    {
        $admin = $this->createSchoolAdmin();
        $this->createEnterprise('pending', '待审公司');
        $this->createEnterprise('approved', '已审公司');

        $response = $this->actingAs($admin)->get('/admin/enterprises?status=pending');

        $response->assertStatus(200)
            ->assertSee('待审公司')
            ->assertDontSee('已审公司');
    }

    public function test_enterprise_list_search()
    {
        $admin = $this->createSchoolAdmin();
        $this->createEnterprise('pending');

        $response = $this->actingAs($admin)->get('/admin/enterprises?search=测试公司');

        $response->assertStatus(200)
            ->assertSee('测试公司');
    }

    // === Enterprise Edit ===

    public function test_enterprise_edit_page()
    {
        $admin = $this->createSchoolAdmin();
        $enterprise = $this->createEnterprise('approved', '编辑测试公司');

        $response = $this->actingAs($admin)->get('/admin/enterprises/' . $enterprise->id . '/edit');

        $response->assertStatus(200)
            ->assertSee('编辑测试公司');
    }

    public function test_enterprise_update()
    {
        $admin = $this->createSchoolAdmin();
        $enterprise = $this->createEnterprise('approved', '原始公司');
        $school = School::create(['name' => '测试大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);

        $response = $this->actingAs($admin)->put('/admin/enterprises/' . $enterprise->id, [
            'name' => '改名公司',
            'credit_code' => $enterprise->credit_code,
            'industry' => '金融',
            'contact_name' => '李四',
            'contact_phone' => '13800001111',
            'college_id' => $college->id,
            'status' => 'approved',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enterprises', ['id' => $enterprise->id, 'name' => '改名公司', 'industry' => '金融']);
    }

    // === Enterprise Delete ===

    public function test_enterprise_delete()
    {
        $admin = $this->createSchoolAdmin();
        $enterprise = $this->createEnterprise('rejected');

        $response = $this->actingAs($admin)->delete('/admin/enterprises/' . $enterprise->id);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('enterprises', ['id' => $enterprise->id]);
    }
}
