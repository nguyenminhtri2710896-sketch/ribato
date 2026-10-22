<?php

namespace App\Http\Controllers\Backend\V1;

use App\Models\User;
use App\Models\UserToken;
use App\Models\UserVirtualAccount;
use App\Services\UserWithdrawService;
use Illuminate\Support\Facades\Http;

/**
 * Công cụ test dành cho quản trị (chỉ sandbox):
 * - Collect: giả lập cổng gửi IPN collection về hệ thống theo đúng endpoint / payload của từng cổng.
 * - Payout: tạo yêu cầu rút tiền thay user qua đúng luồng tạo rút tiền (kết quả được sandbox giả lập ngẫu nhiên).
 */
class CollectionSimulatorController extends BaseController
{
    protected function allowed(): bool
    {
        return config('app.simulator_enabled') && (auth()->user()->is_admin || auth()->user()->full_access);
    }

    protected function error($strMessage)
    {
        return response()->json([
            "error_code" => 404,
            "message" => $strMessage,
            "data" => [],
            "errors" => [[$strMessage]],
            "system_time" => date('Y-m-d H:i:s'),
        ]);
    }

    public function index()
    {
        if (!$this->allowed()) {
            abort(403);
        }

        return view('backend.' . config('app.backend_version') . '.tool.simulate-collection');
    }

    public function ajaxSend()
    {
        if (!$this->allowed()) {
            return $this->error(__("Bạn không có quyền thực hiện thao tác này."));
        }

        $strAccountNumber = trim((string) request('bank_account_number'));
        $intUserId = (int) request('user_id');
        $intAmount = (int) request('amount');
        $strContent = trim((string) request('content'));

        if ($strAccountNumber === "" && !$intUserId) {
            return $this->error(__("Vui lòng chọn người dùng hoặc tài khoản VA."));
        }
        if ($intAmount <= 0) {
            return $this->error(__("Vui lòng nhập số tiền."));
        }
        if ($strContent === "") {
            return $this->error(__("Vui lòng nhập nội dung chuyển khoản."));
        }

        if ($strAccountNumber !== "") {
            // Chọn VA: dùng đúng VA đó (nếu có chọn user thì VA phải thuộc user đó)
            $objVa = UserVirtualAccount::where('bank_account_number', $strAccountNumber)->first();
            if (!$objVa) {
                return $this->error(__("Không tìm thấy tài khoản VA."));
            }
            if ($intUserId && $objVa->user_id != $intUserId) {
                return $this->error(__("Tài khoản VA không thuộc người dùng đã chọn."));
            }
        } else {
            // Chỉ chọn user: lấy ngẫu nhiên một VA đang hoạt động của user
            if (!User::where('id', $intUserId)->exists()) {
                return $this->error(__("Người dùng không tồn tại."));
            }
            $objVa = UserVirtualAccount::where('user_id', $intUserId)->where('status_id', 2)->inRandomOrder()->first();
            if (!$objVa) {
                return $this->error(__("Người dùng chưa có tài khoản VA đang hoạt động."));
            }
        }

        $objUserToken = UserToken::where('user_id', $objVa->user_id)->first();
        if (!$objUserToken) {
            return $this->error(__("Người dùng của VA chưa có token."));
        }

        $strTradeNo = "SBX" . date('YmdHis') . random_int(1000, 9999);
        $strNowMs = (string) round(microtime(true) * 1000);

        if ($objVa->gateway_id == 8) {
            $strUrl = $objVa->ipn_collection ?: url()->route('api.gpay.ipn', ["token" => $objUserToken->token_gateway]);
            $arrPayload = [
                "token" => $objUserToken->token_gateway,
                "action" => "CHANGE_BALANCE",
                "gpay_trans_id" => $strTradeNo,
                "amount" => $intAmount,
                "account_number" => $objVa->bank_account_number,
                "account_name" => $objVa->bank_account_name,
                "message" => $strContent,
                "created_date" => $strNowMs,
            ];
        } elseif ($objVa->gateway_id == 3) {
            $strUrl = $objVa->ipn_collection ?: url()->route('api.ipn.yoobil-collection', ["token" => $objUserToken->token_gateway]);
            $arrPayload = [
                "token" => $objUserToken->token_gateway,
                "bypas_verified" => 1,
                "code" => 10000,
                "msg" => "the request is succeed.",
                "result" => [
                    "accountName" => $objVa->bank_account_name,
                    "accountNo" => $objVa->bank_account_number,
                    "amount" => $intAmount,
                    "bankName" => $objVa->bank_short_code,
                    "currency" => "VND",
                    "orderNo" => $objVa->order_no,
                    "purchaseAmount" => $intAmount,
                    "purchaseCurrency" => "VND",
                    "purchaseTime" => $strNowMs,
                    "remark" => $strContent,
                    "tradeNo" => $strTradeNo,
                    "userName" => $objVa->bank_account_name,
                ],
            ];
        } else {
            return $this->error(__("Cổng của VA này chưa hỗ trợ giả lập."));
        }

        try {
            $response = Http::timeout(30)->asJson()->post($strUrl, $arrPayload);
        } catch (\Exception $ex) {
            return $this->error(__("Gửi IPN thất bại: ") . $ex->getMessage());
        }

        return response()->json([
            "error_code" => 0,
            "message" => __("Đã gửi giao dịch giả lập."),
            "data" => [
                "url" => $strUrl,
                "trade_no" => $strTradeNo,
                "request" => $arrPayload,
                "http_status" => $response->status(),
                "response" => $response->json() ?? $response->body(),
            ],
            "errors" => [],
            "system_time" => date('Y-m-d H:i:s'),
        ]);
    }

    public function ajaxCreatePayout()
    {
        if (!$this->allowed()) {
            return $this->error(__("Bạn không có quyền thực hiện thao tác này."));
        }

        $arrParams = request(['user_id', 'bank_id', 'bank_account_number', 'bank_account_name', 'amount', 'remark', 'ref_code']);
        $objUser = User::where('id', (int) ($arrParams["user_id"] ?? 0))->first();
        if (!$objUser) {
            return $this->error(__("Người dùng không tồn tại."));
        }

        $arrParams["user_id"] = $objUser->id;
        $arrParams["platform"] = "backend_test";

        // Giống API tạo rút tiền: user v1 đi luồng add, còn lại đi luồng addV2
        $userWithdrawService = new UserWithdrawService();
        if ($objUser->withdraw_version == "v1") {
            return response()->json($userWithdrawService->add($arrParams));
        }
        return response()->json($userWithdrawService->addV2($arrParams));
    }
}
