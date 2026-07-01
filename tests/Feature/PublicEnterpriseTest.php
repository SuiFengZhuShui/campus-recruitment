<?php

namespace Tests\Feature;

use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PublicEnterpriseTest extends TestCase
{
    private function createApprovedEnterprise(): Enterprise
    {
        $user = User::create(['role' => 'enterprise', 'username' => 'pub_' . uniqid(), 'name' => '公众企业', 'phone' => '138' . rand(10000000, 99999999), 'email' => uniqid() . '@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        return Enterprise::create(['user_id' => $user->id, 'name' => '公开公司', 'credit_code' => '911100X' . rand(1000, 9999), 'industry' => '互联网', 'contact_name' => '王五', 'contact_phone' => '1390000' . rand(1000, 9999), 'email' => $user->email, 'status' => 'approved']);
    }

    public function test_public_enterprise_list()
    {
        $this->createApprovedEnterprise();

        $response = $this->getJson('/api/enterprises');

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.list.0.name', '公开公司');
    }

    public function test_public_enterprise_list_excludes_pending()
    {
        $this->createApprovedEnterprise();
        // Create a pending enterprise — should not be listed
        $user = User::create(['role' => 'enterprise', 'username' => 'pend_' . uniqid(), 'name' => '待审', 'phone' => '138' . rand(10000000, 99999999), 'email' => uniqid() . '@t.com', 'password' => Hash::make('password'), 'status' => 'active']);
        Enterprise::create(['user_id' => $user->id, 'name' => '未审公司', 'credit_code' => '911100Y' . rand(1000, 9999), 'industry' => '教育', 'contact_name' => '赵六', 'contact_phone' => '1390000' . rand(1000, 9999), 'email' => $user->email, 'status' => 'pending']);

        $response = $this->getJson('/api/enterprises');

        $response->assertStatus(200)
            ->assertJsonMissing(['name' => '未审公司']);
    }

    public function test_public_enterprise_detail()
    {
        $enterprise = $this->createApprovedEnterprise();

        $response = $this->getJson('/api/enterprises/' . $enterprise->id);

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.name', '公开公司');
    }

    public function test_public_enterprise_detail_404_for_pending()
    {
        $user = User::create(['role' => 'enterprise', 'username' => 'hid_' . uniqid(), 'name' => '隐藏', 'phone' => '138' . rand(10000000, 99999999), 'email' => uniqid() . '@t.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $user->id, 'name' => '隐藏公司', 'credit_code' => '911100Z' . rand(1000, 9999), 'industry' => '教育', 'contact_name' => '钱七', 'contact_phone' => '1390000' . rand(1000, 9999), 'email' => $user->email, 'status' => 'pending']);

        $response = $this->getJson('/api/enterprises/' . $enterprise->id);

        $response->assertStatus(404);
    }

    public function test_public_enterprise_industries()
    {
        $this->createApprovedEnterprise();

        $response = $this->getJson('/api/enterprises/industries');

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonFragment(['互联网']);
    }
}
