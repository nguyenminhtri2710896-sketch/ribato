<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\GatewayAccount;
use App\Services\UserWithdrawService;
use App\Services\WithdrawPaymenthotLogService;
use App\Services\WithdrawYoobilLogService;
use App\Utilities\General;
use App\Utilities\GpayV2;
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

    /**
     * @OA\Post(
     *     path="/api/other/get-bank-account-name",
     *     summary="Look up bank account holder name",
     *     description="Returns the account holder name of a bank account from the bank code and account number. Use it to verify the recipient before creating a withdrawal request. Set <b>gateway_account_id</b> to look up through a specific GpayV2 gateway account; otherwise the system default gateway is used.",
     *     tags={"Bank"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"bank_code","bank_account_number"},
     *                 @OA\Property(property="bank_code", type="string", example="MB", description="Bank code (e.g. MB, BIDV, TCB, VCB)."),
     *                 @OA\Property(property="bank_account_number", type="string", example="0123456789", description="Bank account number to look up."),
     *                 @OA\Property(property="gateway_account_id", type="integer", example=13, description="Optional. GpayV2 gateway account ID used for the lookup. The system default is used when omitted or invalid.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="error_code", type="integer", example=0, description="<b>0</b>: success. <b>404</b>: missing parameter, account holder name not found, or gateway error."),
     *             @OA\Property(property="message", type="string", example="Success.", description="Result message."),
     *             @OA\Property(property="system_time", type="string", example="2026-10-20 10:00:00", description="Server time."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), description="Error messages. Empty on success."),
     *             @OA\Property(
     *                 property="data", type="object", description="Response payload. Empty on error.",
     *                 @OA\Property(property="bank_code", type="string", example="MB", description="Bank code that was looked up."),
     *                 @OA\Property(property="bank_account_number", type="string", example="0123456789", description="Bank account number that was looked up."),
     *                 @OA\Property(property="bank_account_name", type="string", example="NGUYEN VAN A", description="Account holder name.")
     *             ),
     *             @OA\Examples(example="result_success", value={"error_code":0,"message":"Success.","system_time":"2026-10-20 10:00:00","errors":{},"data":{"bank_code":"MB","bank_account_number":"0123456789","bank_account_name":"NGUYEN VAN A"}}, summary="Result success."),
     *             @OA\Examples(example="result_error", value={"error_code":404,"message":"","system_time":"2026-10-20 10:00:00","errors":{{"Unable to get the account holder name."}},"data":{}}, summary="Result error.")
     *         )
     *     )
     * )
     */
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
        

        $intGatewayAccountId = (int) request('gateway_account_id');
       
        if(!empty(env('CHECK_INFO_BANK_WITH_ACCOUNT'))){
           $intGatewayAccountId = (int) env('CHECK_INFO_BANK_WITH_ACCOUNT'); 
        }

        if ($intGatewayAccountId) {
            $objGpayAccount = GatewayAccount::where('id', $intGatewayAccountId)->where('gateway_id', 8)->first();
            if ($objGpayAccount) {
                $gpayV2 = GpayV2::fromGatewayAccount($objGpayAccount);
                $resQuery = $gpayV2->queryBankAccount((string) $arrParams["bank_account_number"], (string) $arrParams["bank_code"]);
                if (empty($resQuery["success"]) || empty($resQuery["full_name"])) {
                    return $withdrawPaymenthotLogService->setStatusCode(404)->setMessage("")->setData($resQuery)->setErrors([
                        [__("Không lấy được thông tin chủ khoản.")]
                    ])->result();
                }

                return $withdrawPaymenthotLogService->setStatusCode(0)->setMessage(__('Thành công.'))->setData([
                    'bank_code' => $arrParams['bank_code'],
                    'bank_account_number' => $arrParams['bank_account_number'],
                    'bank_account_name' => str_replace("  ", " ", trim($resQuery["full_name"])),
                ])->result();
            }
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