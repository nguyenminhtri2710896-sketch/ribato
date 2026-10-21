<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserService;
use App\Services\UserWithdrawService;
use App\Utilities\General;

class UserWithdrawController extends BaseController
{

    private $userWithdrawService = null;
    private $userService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserWithdrawService $userWithdrawService, UserService $userService)
    {
        $this->userWithdrawService = $userWithdrawService;
        $this->userService = $userService;
    }
    /**
     * @OA\Post(
     *     path="/api/user-withdraw/get-list",
     *     summary="Lấy danh sách yêu cầu rút tiền",
     *     tags={"Widthdraws"},
     *      @OA\Parameter(
     *         name="api-token",
     *         in="header",
     *         description="token allow create service",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="query",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="limit",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="page",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="sign",
     *                     type="string"
     *                 ),
     *                 example={"query": {},"limit":100,"page":1, "sign": "signiture data"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Examples(example="result_false", value={"error_code":401,"system_time":"2023-11-24 11:15:59","message":"access_token hết hạn hoặc không được phép sử dụng","errors":{"access_token hết hạn hoặc không được phép sử dụng"},"data":null,"tranid_tracking":"bc47d534b97f7cdb4c2d5d229376915c"}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code": 200,"message": "success","system_time":"2023-11-24 11:15:59","errors":{},"data":{}}, summary="Result success."),
     *         )
     *     )
     * )
     */
    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["user_withdraws.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["user_withdraws.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
            unset($arrParams["query"]["created_at_to"]);
        }

        if (!empty($arrParams["query"]['updated_at_from'])) {
            $arrParams["query_greater_than"]["user_withdraws.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_from']);
            unset($arrParams["query"]["updated_at_from"]);
        }

        if (!empty($arrParams["query"]['updated_at_to'])) {
            $arrParams["query_less_than"]["user_withdraws.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_to'] . " 23:59:59");
            unset($arrParams["query"]["updated_at_to"]);
        }

        if ((auth()->user()->id == 900 ||auth()->user()->id == 1010 ||auth()->user()->id == 1013 || auth()->user()->id == 897 || auth()->user()->id == 904 || auth()->user()->id == 16 || auth()->user()->id == 61) && empty($arrParams["show_full_transaction"]) && empty($arrParams["query"]["show_full_transaction"])) {
            $arrParams["query_not_like"]["remark"] = "SA%";
        }

        if (!empty($arrParams["query"]['bank_account_name'])) {
            $arrParams["query_like"]["user_withdraws.bank_account_name"] = $arrParams["query"]['bank_account_name'];
            unset($arrParams["query"]["bank_account_name"]);
        }

        if (!empty($arrParams["query"]['remark'])) {
            $arrParams["query_like"]["user_withdraws.remark"] = $arrParams["query"]['remark'];
            unset($arrParams["query"]["remark"]);
        }

        if (!empty($arrParams["query"]['gateway_id'])) {
            $arrParams["query"]["user_withdraws.gateway_id"] = $arrParams["query"]['gateway_id'];
            unset($arrParams["query"]["gateway_id"]);
        }

        if (!auth()->user()->full_access && !auth()->user()->is_accountant) {
            $arrParams["query"]["is_show"] = 1;
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userWithdrawService->getList($arrParams));
    }


    /**
     * @OA\Post(
     *     path="/api/user-withdraw/create",
     *     summary="Create withdrawal request",
     *     description="Creates a withdrawal request to a bank account. The amount plus fee is deducted from the partner balance immediately. The result (SUCCESS / FAILED) is sent to the partner's <b>webhook_payout_url</b> (see the Payout IPN). Amount must be at least 10,000 VND and below 300,000,000 VND. When 2-factor authentication is enabled on the account, <b>otp</b> is required.",
     *     tags={"Widthdraws"},
     *     @OA\Parameter(name="api-token", in="header", description="token allow create service", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"bank_id","bank_account_number","bank_account_name","amount","remark"},
     *                 @OA\Property(property="bank_id", type="integer", example=5, description="Recipient bank ID. Get it from the bank list API (/api/bank/get-list)."),
     *                 @OA\Property(property="bank_account_number", type="string", example="1021312323", description="Recipient bank account number."),
     *                 @OA\Property(property="bank_account_name", type="string", example="NGUYEN VAN A", description="Recipient account holder name. You can verify it first with /api/other/get-bank-account-name."),
     *                 @OA\Property(property="amount", type="integer", example=100000, description="Amount (VND) to transfer to the recipient, excluding fee. Minimum 10,000."),
     *                 @OA\Property(property="remark", type="string", example="noi dung ck", description="Transfer content."),
     *                 @OA\Property(property="ref_code", type="string", example="ABC123", description="Optional. Your own reference code of the request. It is returned in <b>user_withdraw.ref_code</b> and in the Payout IPN (<b>result.ref_code</b>) so you can match the result to your order."),
     *                 @OA\Property(property="otp", type="string", example="123456", description="Required only when 2-factor authentication is enabled on the account."),
     *                 @OA\Property(property="sign", type="string", example="signiture data", description="Signature of the request.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="error_code", type="integer", example=0, description="<b>0</b>: success. Other values: error (404 business error such as invalid bank / insufficient balance / limit exceeded, 414 invalid payload, 401 unauthorized)."),
     *             @OA\Property(property="message", type="string", example="Tạo yêu cầu rút tiền thành công.", description="Result message."),
     *             @OA\Property(property="system_time", type="string", example="2026-10-20 10:00:00", description="Server time."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), description="Error messages. Empty on success."),
     *             @OA\Property(
     *                 property="data", type="object", description="Response payload. Empty on error.",
     *                 @OA\Property(
     *                     property="user_withdraw", type="object", description="Created withdrawal request.",
     *                     @OA\Property(property="id", type="integer", example=1, description="Withdrawal request ID."),
     *                     @OA\Property(property="user_id", type="integer", example=10, description="Partner user ID."),
     *                     @OA\Property(property="trans_code", type="string", example="2025091519847", description="Transaction code of the withdrawal generated by the system. Returned in the Payout IPN."),
     *                     @OA\Property(property="ref_code", type="string", example="ABC123", description="Your reference code. Empty if not sent."),
     *                     @OA\Property(property="bank_id", type="integer", example=5, description="Recipient bank ID."),
     *                     @OA\Property(property="bank_short_name", type="string", example="MB", description="Recipient bank short name."),
     *                     @OA\Property(property="bank_account_number", type="string", example="1021312323", description="Recipient bank account number."),
     *                     @OA\Property(property="bank_account_name", type="string", example="NGUYEN VAN A", description="Recipient account holder name."),
     *                     @OA\Property(property="remark", type="string", example="noi dung ck", description="Transfer content."),
     *                     @OA\Property(property="amount", type="integer", example=100000, description="Amount (VND) transferred to the recipient."),
     *                     @OA\Property(property="fee", type="number", example=3000, description="Fee (VND) charged for the withdrawal."),
     *                     @OA\Property(property="amount_after_fee", type="number", example=103000, description="Total amount (VND) deducted from the balance (amount + fee)."),
     *                     @OA\Property(property="status_id", type="integer", example=1, description="Withdrawal status. <b>1</b>: requested, <b>4</b>: processing, <b>5</b>: awaiting transaction verification, <b>2</b>: completed, <b>3</b>: cancelled / failed."),
     *                     @OA\Property(property="created_at", type="string", example="2026-10-20T10:00:00.000000Z", description="Created time.")
     *                 )
     *             ),
     *             @OA\Examples(example="result_false", value={"error_code":404,"system_time":"2023-11-24 11:15:59","message":"","errors":{{"Số tiền yêu cầu rút tối thiểu 10,000đ."}},"data":{}}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code":0,"message":"Tạo yêu cầu rút tiền thành công.","system_time":"2023-11-24 11:15:59","errors":{},"data":{"user_withdraw":{"id":1,"user_id":10,"trans_code":"2025091519847","ref_code":"ABC123","bank_id":5,"bank_short_name":"MB","bank_account_number":"1021312323","bank_account_name":"NGUYEN VAN A","remark":"noi dung ck","amount":100000,"fee":3000,"amount_after_fee":103000,"status_id":1,"created_at":"2026-10-20T10:00:00.000000Z"}}}, summary="Result success.")
     *         )
     *     )
     * )
     */
    public function add()
    {
        $arrParams = request(['user_business_id', 'bank_id', 'type_id', 'bank_account_number', 'bank_account_name', 'amount', 'remark', 'otp', 'ref_code']);
        // if (!auth()->user()->full_access) {
        $arrParams["user_id"] = auth()->user()->id;


        //}
        if (!empty(auth()->user()->authy_2factor)) {
            $resultCheckAuthenticator = $this->userService->checkAuthenticator($arrParams);
            if ($resultCheckAuthenticator["error_code"] != 0) {
                return $resultCheckAuthenticator;
            }
        }

        $arrParams["platform"] = "api";
        if (auth()->user()->withdraw_version == "v1") {
            return response()->json($this->userWithdrawService->add($arrParams));

        } else {
            return response()->json($this->userWithdrawService->addV2($arrParams));
        }
    }

    public function addManual()
    {
        if (!(auth()->user()->is_admin || auth()->user()->full_access)) {
            return response()->json([
                "error_code" => 403,
                "message" => __("Bạn không có quyền thực hiện thao tác này."),
                "data" => [],
                "errors" => [
                    [__("Không được phép.")]
                ],
                "system_time" => date('Y-m-d H:i:s')
            ]);
        }

        $arrParams = request(['user_id', 'bank_id', 'bank_account_number', 'bank_account_name', 'amount', 'fee', 'remark', 'otp', 'type_id', 'ref_code', 'gateway_id', 'status_id', 'trans_code', 'created_at']);

        // if (!empty(auth()->user()->authy_2factor)) {
        //     $resultCheckAuthenticator = $this->userService->checkAuthenticator($arrParams);
        //     if ($resultCheckAuthenticator["error_code"] != 0) {
        //         return $resultCheckAuthenticator;
        //     }
        // }

        $arrParams["platform"] = "backend_manual";
        return response()->json($this->userWithdrawService->addManual($arrParams));
    }

    // public function addV2()
    // {
    //     $arrParams            = request(['user_business_id', 'bank_id', 'bank_account_number', 'bank_account_name', 'amount', 'remark', 'otp']);
    //     $arrParams["user_id"] = auth()->user()->id;

    //     if (!empty(auth()->user()->authy_2factor)) {
    //         $resultCheckAuthenticator = $this->userService->checkAuthenticator($arrParams);
    //         if ($resultCheckAuthenticator["error_code"] != 0) {
    //             return $resultCheckAuthenticator;
    //         }
    //     }

    //     $arrParams["platform"] = "api";
    //     return response()->json($this->userWithdrawService->addV2($arrParams));
    // }

    public function addMultible()
    {
        $arrParams = request(['content', 'otp']);
        // if (!auth()->user()->full_access) {
        $arrParams["user_id"] = auth()->user()->id;
        //}

        if (!empty(auth()->user()->authy_2factor)) {
            $resultCheckAuthenticator = $this->userService->checkAuthenticator($arrParams);
            if ($resultCheckAuthenticator["error_code"] != 0) {
                return $resultCheckAuthenticator;
            }
        }
        $arrParams["platform"] = "api";
        $arrParams["withdraw_version"] = auth()->user()->withdraw_version;
        return response()->json($this->userWithdrawService->addMultible($arrParams));
    }

    public function addMultibleCheck()
    {
        $arrParams = request(['content', 'otp']);
        // if (!auth()->user()->full_access) {
        $arrParams["user_id"] = auth()->user()->id;
        //}

        return response()->json($this->userWithdrawService->addMultibleCheck($arrParams));
    }

    public function changeStatus()
    {
        $arrParams = request(['id', 'status_id', 'user_id', 'note']);
        return response()->json($this->userWithdrawService->changeStatus($arrParams));
    }

    public function getDetail()
    {
        $arrParams = request(['query']);
        return response()->json($this->userWithdrawService->getDetail($arrParams));
    }


    public function createBill()
    {
        $arrParams = request(['id']);
        return response()->json($this->userWithdrawService->createBill($arrParams));
    }

    public function exportExcel()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["user_withdraws.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["user_withdraws.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
            unset($arrParams["query"]["created_at_to"]);
        }

        if (!empty($arrParams["query"]['updated_at_from'])) {
            $arrParams["query_greater_than"]["user_withdraws.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_from']);
            unset($arrParams["query"]["updated_at_from"]);
        }

        if (!empty($arrParams["query"]['updated_at_to'])) {
            $arrParams["query_less_than"]["user_withdraws.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_to'] . " 23:59:59");
            unset($arrParams["query"]["updated_at_to"]);
        }

        if ((auth()->user()->id == 900 || auth()->user()->id == 897 || auth()->user()->id == 904) && empty($arrParams["show_full_transaction"]) && empty($arrParams["query"]["show_full_transaction"])) {
            $arrParams["query_not_like"]["remark"] = "SA%";
        }

        if (!empty($arrParams["query"]['bank_account_name'])) {
            $arrParams["query_like"]["user_withdraws.bank_account_name"] = $arrParams["query"]['bank_account_name'];
            unset($arrParams["query"]["bank_account_name"]);
        }

        if (!empty($arrParams["query"]['remark'])) {
            $arrParams["query_like"]["user_withdraws.remark"] = $arrParams["query"]['remark'];
            unset($arrParams["query"]["remark"]);
        }

        if (!auth()->user()->full_access && !auth()->user()->is_accountant) {
            $arrParams["query"]["is_show"] = 1;
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }

        if (auth()->user()->full_access) {
            return response()->json($this->userWithdrawService->exportExcelFullaccess($arrParams));
        }

        if (auth()->user()->is_accountant) {
            return response()->json($this->userWithdrawService->exportExcelAccountant($arrParams));

        }
        return response()->json($this->userWithdrawService->exportExcel($arrParams));
    }
}