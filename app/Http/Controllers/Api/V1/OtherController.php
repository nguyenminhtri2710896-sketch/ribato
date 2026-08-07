<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\GatewayAccount;
use App\Services\UserWithdrawService;
use App\Services\WithdrawPaymenthotLogService;
use App\Services\WithdrawYoobilLogService;
use App\Utilities\General;
use App\Utilities\Paymenthot;

class OtherController extends BaseController
{

    private $withdrawYoobilLogService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(WithdrawYoobilLogService $withdrawYoobilLogService)
    {
        $this->withdrawYoobilLogService = $withdrawYoobilLogService;
    }



    // 'user_id' => $intUserId,
    // 'amount' => $objTransaction->amount,
    // 'received_amount' => $objTransaction->received_amount,
    // 'received_date' => $objTransaction->received_at,
    // 'fee' => $objTransaction->fee,
    // 'content' => $objTransaction->content,
    // 'ref_code' => $objTransaction->ref_code,
    // 'bank_short_name' => $objTransaction->bank_short_name,
    // 'bank_account_name' => $objTransaction->bank_account_name,
    // 'bank_account_number' => $objTransaction->bank_account_number


    /**
     * @OA\Post(
     *     path="doman/ipn-callback",
     *     summary="Địa chỉ đối tác khai báo để tiếp nhận kết quả giao dịch",
     *     tags={"IPN"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="received_amount",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="received_date",
     *                     type="date"
     *                 ),
     *                 @OA\Property(
     *                     property="fee",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="ref_code",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="bank_short_name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="bank_account_name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="bank_account_number",
     *                     type="string"
     *                 ),
     *                 example={"user_id": 1,"amount":10000,"received_amount":8000,"received_date":"2025-01-01 12:11:11","fee":2000,"content":"Nội dung chuyển khoản","ref_code":"ABC123","bank_short_name":"BIDV","bank_account_name":"NGUYEN VAN BAY","bank_account_number":"09928323232"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *         )
     *     )
     * )
     */
    public function webhook()
    {
        return null;
    }

    /**
     * @OA\Post(
     *     path="doman/ipn-callback-payout",
     *     summary="Địa chỉ đối tác khai báo để tiếp nhận kết quả rút tiền",
     *     tags={"IPN PAYOUT"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="fee",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="trans_code",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="status_id",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="code",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string"
     *                 ),
     *                 example={"error_code":0,"msg":"Thành công","result":{"user_id":1,"amount":10000,"fee":3000,"trans_code":"2025091519847","content":"ck","status_id":2,"code":"SUCCESS","message":"Thành công","sign":"sss"}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *         )
     *     )
     * )
     */
    public function webhookPayout()
    {
        return null;
    }

    public function getBankAccountName()
    {
        $withdrawPaymenthotLogService = new WithdrawPaymenthotLogService();
        $arrParams = request(['bank_code', 'bank_account_number']);
        if(empty($arrParams["bank_code"])){
            return $withdrawPaymenthotLogService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Vui lòng nhập mã ngân hàng.")]
            ])->result();
        }

        if(empty($arrParams["bank_account_number"])){
            return $withdrawPaymenthotLogService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Vui lòng nhập số tài khoản.")]
            ])->result();
        }
        

        $objGatewayAccount = GatewayAccount::where('id', 12)->where('gateway_id', 1)->first();
        $checkToken = $withdrawPaymenthotLogService->checkTokenCreateRequestV2($objGatewayAccount->id);
        if (!$checkToken) {
            return $withdrawPaymenthotLogService->setStatusCode(404)->setMessage("")->setData($checkToken)->setErrors([
                [__("Hệ thống giao dịch lỗi.")]
            ])->result();
        }
        $paymenthot = new Paymenthot();
        $bodGetName = $paymenthot->setAuthorization($objGatewayAccount->access_token)->setTenant($objGatewayAccount->tenant)->setUsername($objGatewayAccount->username)->setPassword($objGatewayAccount->password)->setPrivateKey($objGatewayAccount->private_key)->bodGetName([
            "bankId" => $arrParams["bank_code"]??"",
            "bankRefNumber" => $arrParams["bank_account_number"]??"",
        ]);

        if (empty($bodGetName["success"])) {
            return $withdrawPaymenthotLogService->setStatusCode(404)->setMessage("")->setData($bodGetName)->setErrors([
                [__("Không lấy được thông tin chủ khoản.")]
            ])->result();
        }

        $strBankAccountGet = str_replace("  ", " ", trim($bodGetName["data"]["data"]["bankRefName"] ?? ""));
        return $withdrawPaymenthotLogService->setStatusCode(0)->setMessage(__('Thành công.'))->setData([
            'bank_code' => $arrParams['bank_code']??"",
            'bank_account_number' => $arrParams['bank_account_number']??"",
            'bank_account_name' => $strBankAccountGet,
        ])->result();
    }

}