<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserToken;
use Tests\TestCase;

class ApiV2Test extends TestCase
{
    protected $user;
    protected $userToken;
    protected $apiToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Tìm hoặc tạo user để làm dữ liệu kiểm thử
        $this->user = User::first();
        if (!$this->user) {
            $this->user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Tìm hoặc tạo token tương ứng
        $this->userToken = UserToken::where('user_id', $this->user->id)->first();
        if (!$this->userToken) {
            $this->userToken = UserToken::create([
                'user_id' => $this->user->id,
                'token' => 'test-api-token-1234567890',
                'permission' => 'all',
                'expired_at' => date('Y-m-d H:i:s', strtotime('+1 year')),
            ]);
        }

        $this->apiToken = $this->userToken->token;
    }

    private function generateChecksum(array $params): string
    {
        if (isset($params['checksum'])) {
            unset($params['checksum']);
        }
        ksort($params);
        array_walk_recursive($params, function (&$val) {
            if ($val === null) {
                $val = '';
            }
        });
        $queryString = urldecode(http_build_query($params));
        return md5($queryString . $this->apiToken);
    }

    /**
     * Test API lấy danh sách ngân hàng V2
     */
    public function test_bank_get_list_v2()
    {
        $params = [
            'page' => 1,
            'limit' => 10,
        ];
        $params['checksum'] = $this->generateChecksum($params);

        $response = $this->json('POST', '/api/v2/bank/get-list', $params, [
            'api-token' => $this->apiToken
        ]);

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringNotContainsString('Checksum xác thực không đúng', $content);
    }

    /**
     * Test API lấy danh sách giao dịch tiền vào V2
     */
    public function test_transaction_get_list_v2()
    {
        $params = [
            'page' => 1,
            'limit' => 10,
        ];
        $params['checksum'] = $this->generateChecksum($params);

        $response = $this->json('POST', '/api/v2/transaction/get-list', $params, [
            'api-token' => $this->apiToken
        ]);

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringNotContainsString('Checksum xác thực không đúng', $content);
    }

    /**
     * Test API chi tiết giao dịch tiền vào V2
     */
    public function test_transaction_get_detail_v2()
    {
        $params = [
            'query' => [
                'ref_code' => 'TEST_REF_CODE',
            ],
        ];
        $params['checksum'] = $this->generateChecksum($params);

        $response = $this->json('POST', '/api/v2/transaction/get-detail', $params, [
            'api-token' => $this->apiToken
        ]);

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringNotContainsString('Checksum xác thực không đúng', $content);
    }

    /**
     * Test API lịch sử rút tiền V2
     */
    public function test_user_withdraw_get_list_v2()
    {
        $params = [
            'page' => 1,
            'limit' => 10,
        ];
        $params['checksum'] = $this->generateChecksum($params);

        $response = $this->json('POST', '/api/v2/user-withdraw/get-list', $params, [
            'api-token' => $this->apiToken
        ]);

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringNotContainsString('Checksum xác thực không đúng', $content);
    }

    /**
     * Test API tạo lệnh rút tiền V2
     */
    public function test_user_withdraw_create_v2()
    {
        $params = [
            'bank_id' => 1,
            'bank_account_number' => '123456789',
            'bank_account_name' => 'TEST ACCOUNT',
            'amount' => 50000,
            'remark' => 'TEST WITHDRAW',
        ];
        $params['checksum'] = $this->generateChecksum($params);

        $response = $this->json('POST', '/api/v2/user-withdraw/create', $params, [
            'api-token' => $this->apiToken
        ]);

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringNotContainsString('Checksum xác thực không đúng', $content);
    }
}
