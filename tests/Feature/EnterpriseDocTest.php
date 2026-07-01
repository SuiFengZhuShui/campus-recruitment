<?php

namespace Tests\Feature;

use App\Models\Enterprise;
use App\Models\EnterpriseDoc;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EnterpriseDocTest extends TestCase
{
    private function createEnterpriseUser(): User
    {
        return User::create(['role' => 'enterprise', 'username' => 'doc_' . uniqid(), 'name' => '企业', 'phone' => '139' . rand(10000000, 99999999), 'email' => uniqid() . '@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
    }

    private function createApprovedEnterprise(User $user): Enterprise
    {
        return Enterprise::create(['user_id' => $user->id, 'name' => '测试公司', 'credit_code' => '91110' . rand(1000000, 9999999), 'industry' => '互联网', 'contact_name' => '张三', 'contact_phone' => $user->phone, 'email' => $user->email, 'status' => 'approved']);
    }

    // === Enterprise Doc Upload ===

    public function test_enterprise_can_upload_doc()
    {
        $user = $this->createEnterpriseUser();
        $this->createApprovedEnterprise($user);

        $response = $this->actingAs($user)->postJson('/api/my/docs', [
            'type' => 'license',
            'file' => \Illuminate\Http\UploadedFile::fake()->create('license.pdf', 100),
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200]);
    }

    public function test_enterprise_can_delete_doc()
    {
        $user = $this->createEnterpriseUser();
        $enterprise = $this->createApprovedEnterprise($user);

        $doc = EnterpriseDoc::create([
            'enterprise_id' => $enterprise->id,
            'type' => 'license',
            'file_path' => 'enterprises/' . $enterprise->id . '/docs/test.pdf',
            'file_name' => 'test.pdf',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->deleteJson('/api/my/docs/' . $doc->id);

        $response->assertStatus(200)
            ->assertJson(['code' => 200]);

        $this->assertDatabaseMissing('enterprise_docs', ['id' => $doc->id]);
    }

    // === Admin View Doc (fixed path) ===

    public function test_admin_can_view_doc()
    {
        $enterpriseUser = $this->createEnterpriseUser();
        $enterprise = $this->createApprovedEnterprise($enterpriseUser);

        // Create doc with path in the local disk
        $diskRoot = \Storage::disk('local')->path('');
        $filePath = 'enterprises/' . $enterprise->id . '/docs/viewtest.txt';
        file_put_contents($diskRoot . $filePath, 'test content');

        $doc = EnterpriseDoc::create([
            'enterprise_id' => $enterprise->id,
            'type' => 'license',
            'file_path' => $filePath,
            'file_name' => 'viewtest.txt',
            'status' => 'approved',
        ]);

        $admin = User::create(['role' => 'school', 'username' => 'admin_doc', 'name' => '管理员', 'phone' => '13910000001', 'password' => Hash::make('password'), 'status' => 'active']);

        $response = $this->actingAs($admin)->get('/admin/docs/' . $doc->id . '/view');

        $response->assertStatus(200);

        // Clean up
        unlink($diskRoot . $filePath);
    }

    public function test_view_doc_404_for_missing_file()
    {
        $enterpriseUser = $this->createEnterpriseUser();
        $enterprise = $this->createApprovedEnterprise($enterpriseUser);

        $doc = EnterpriseDoc::create([
            'enterprise_id' => $enterprise->id,
            'type' => 'license',
            'file_path' => 'enterprises/' . $enterprise->id . '/docs/nonexistent.pdf',
            'file_name' => 'nonexistent.pdf',
            'status' => 'approved',
        ]);

        $admin = User::create(['role' => 'school', 'username' => 'admin_doc2', 'name' => '管理员', 'phone' => '13910000002', 'password' => Hash::make('password'), 'status' => 'active']);

        $response = $this->actingAs($admin)->get('/admin/docs/' . $doc->id . '/view');

        $response->assertStatus(404);
    }
}
