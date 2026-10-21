<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Bank;
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
     *     summary="Collection IPN: partner endpoint that receives the collection (payment received) result",
     *     description="The system sends an HTTP POST (JSON) to the <b>webhook_url</b> declared by the partner when money has been received for a transaction. Respond with HTTP 2xx to acknowledge. A non-2xx response or a timeout (90s) is treated as failed and retried automatically (up to 4 attempts, 30s apart); it can also be resent from the IPN management page. Verify the <b>sign</b> field with the system public key before trusting the data.",
     *     tags={"IPN"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="error_code", type="integer", example=0, description="Always <b>0</b>."),
     *                 @OA\Property(property="msg", type="string", example="Thành công", description="Result message."),
     *                 @OA\Property(
     *                     property="result", type="object", description="Transaction data.",
     *                     @OA\Property(property="user_id", type="integer", example=1, description="Partner user ID."),
     *                     @OA\Property(property="amount", type="number", example=10000, description="Amount (VND) of the transaction."),
     *                     @OA\Property(property="received_amount", type="number", example=8000, description="Amount (VND) credited to the partner after fee."),
     *                     @OA\Property(property="received_date", type="string", example="2025-01-01 12:11:11", description="Time the money was received (Y-m-d H:i:s)."),
     *                     @OA\Property(property="fee", type="number", example=2000, description="Fee (VND) charged for the transaction."),
     *                     @OA\Property(property="content", type="string", example="Nội dung chuyển khoản", description="Transfer content."),
     *                     @OA\Property(property="ref_code", type="string", example="ABC123", description="Reference / order code of the transaction (the VA transaction reference, or the code the partner used when creating the payment)."),
     *                     @OA\Property(property="bank_short_name", type="string", example="BIDV", description="Receiving bank short name."),
     *                     @OA\Property(property="bank_short_code", type="string", example="BIDV", description="Receiving bank code."),
     *                     @OA\Property(property="bank_account_name", type="string", example="NGUYEN VAN BAY", description="Receiving account holder name."),
     *                     @OA\Property(property="bank_account_number", type="string", example="09928323232", description="Receiving account number (the VA number for VA transactions)."),
     *                     @OA\Property(property="bank_total_balance", type="number", example=1500000, description="Optional. Balance of the receiving bank account after the transaction, only sent when known."),
     *                     @OA\Property(property="sign", type="string", example="sss", description="Signature of the result fields. Empty if the partner has no system key.")
     *                 ),
     *                 example={"error_code":0,"msg":"Thành công","result":{"user_id":1,"amount":10000,"received_amount":8000,"received_date":"2025-01-01 12:11:11","fee":2000,"content":"Nội dung chuyển khoản","ref_code":"ABC123","bank_short_name":"BIDV","bank_short_code":"BIDV","bank_account_name":"NGUYEN VAN BAY","bank_account_number":"09928323232","bank_total_balance":1500000,"sign":"sss"}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK. Any HTTP 2xx response acknowledges the IPN; the body is ignored.",
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
     *     summary="Payout IPN: partner endpoint that receives the withdrawal (payout) result",
     *     description="The system sends an HTTP POST (JSON) to the <b>webhook_payout_url</b> declared by the partner whenever the status of a withdrawal request changes. <b>PENDING</b> is not final; only <b>SUCCESS</b> and <b>FAILED</b> are final. Respond with HTTP 2xx to acknowledge. A non-2xx response or a timeout (90s) is treated as failed and retried automatically (up to 4 attempts, 30s apart); it can also be resent from the IPN management page. Verify the <b>sign</b> field with the system public key before trusting the data.",
     *     tags={"IPN PAYOUT"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="error_code", type="integer", example=0, description="Always <b>0</b>."),
     *                 @OA\Property(property="msg", type="string", example="Thành công", description="Result message."),
     *                 @OA\Property(
     *                     property="result", type="object", description="Withdrawal data.",
     *                     @OA\Property(property="user_id", type="integer", example=1, description="Partner user ID."),
     *                     @OA\Property(property="amount", type="number", example=10000, description="Withdrawal amount (VND), excluding fee."),
     *                     @OA\Property(property="fee", type="number", example=3000, description="Fee (VND) charged for the withdrawal."),
     *                     @OA\Property(property="trans_code", type="string", example="2025091519847", description="Transaction code of the withdrawal generated by the system."),
     *                     @OA\Property(property="ref_code", type="string", example="ABC123", description="Reference code sent by the partner when creating the withdrawal request. Empty if none was sent."),
     *                     @OA\Property(property="content", type="string", example="ck", description="Transfer content (the remark sent when creating the request)."),
     *                     @OA\Property(property="status_id", type="integer", enum={1,4,5,2,3}, example=2, description="Withdrawal status. <b>1</b>: requested, <b>4</b>: processing, <b>5</b>: awaiting transaction verification, <b>2</b>: completed, <b>3</b>: cancelled / failed (amount and fee refunded)."),
     *                     @OA\Property(property="code", type="string", enum={"PENDING","SUCCESS","FAILED"}, example="SUCCESS", description="Result code. <b>PENDING</b>: status_id 1, 4, 5 (not final). <b>SUCCESS</b>: status_id 2. <b>FAILED</b>: status_id 3."),
     *                     @OA\Property(property="message", type="string", example="Thành công", description="Note of the withdrawal, e.g. the failure reason."),
     *                     @OA\Property(property="sign", type="string", example="sss", description="Signature of the result fields. Empty if the partner has no system key.")
     *                 ),
     *                 example={"error_code":0,"msg":"Thành công","result":{"user_id":1,"amount":10000,"fee":3000,"trans_code":"2025091519847","ref_code":"ABC123","content":"ck","status_id":2,"code":"SUCCESS","message":"Thành công","sign":"sss"}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK. Any HTTP 2xx response acknowledges the IPN; the body is ignored.",
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
     *     description="Returns the account holder name of a bank account from the bank (bank_id or bank_code) and account number. Use it to verify the recipient before creating a withdrawal request. Set <b>gateway_account_id</b> to look up through a specific GpayV2 gateway account; otherwise the system default gateway is used.",
     *     tags={"Bank"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"bank_account_number"},
     *                 @OA\Property(property="bank_id", type="integer", example=5, description="Bank ID from the bank list API (/api/bank/get-list). Either <b>bank_id</b> or <b>bank_code</b> is required. When bank_id is sent, the bank code is taken from it and bank_code is ignored."),
     *                 @OA\Property(property="bank_code", type="string", example="MB", description="Bank code (e.g. MB, BIDV, TCB, VCB). Required when bank_id is not sent."),
     *                 @OA\Property(property="bank_account_number", type="string", example="0123456789", description="Bank account number to look up."),
     *                 @OA\Property(property="gateway_account_id", type="integer", example=13, description="Optional. GpayV2 gateway account ID used for the lookup. The system default is used when omitted or invalid."),
     *                 example={"bank_id": 5, "bank_account_number": "0123456789"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="error_code", type="integer", example=0, description="<b>0</b>: success. <b>404</b>: neither bank_id nor bank_code sent, bank_id not found, bank_account_number missing, account holder name not found, or gateway error."),
     *             @OA\Property(property="message", type="string", example="Success.", description="Result message."),
     *             @OA\Property(property="system_time", type="string", example="2026-10-20 10:00:00", description="Server time."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), description="Error messages. Empty on success."),
     *             @OA\Property(
     *                 property="data", type="object", description="Response payload. Empty on error.",
     *                 @OA\Property(property="bank_code", type="string", example="MB", description="Bank code that was looked up (resolved from bank_id when bank_id is sent)."),
     *                 @OA\Property(property="bank_account_number", type="string", example="0123456789", description="Bank account number that was looked up."),
     *                 @OA\Property(property="bank_account_name", type="string", example="NGUYEN VAN A", description="Account holder name.")
     *             ),
     *             @OA\Examples(example="result_success", value={"error_code":0,"message":"Success.","system_time":"2026-10-20 10:00:00","errors":{},"data":{"bank_code":"MB","bank_account_number":"0123456789","bank_account_name":"NGUYEN VAN A"}}, summary="Result success."),
     *             @OA\Examples(example="result_error", value={"error_code":404,"message":"","system_time":"2026-10-20 10:00:00","errors":{{"Unable to get the account holder name."}},"data":{}}, summary="Result error."),
     *             @OA\Examples(example="result_invalid_bank", value={"error_code":404,"message":"","system_time":"2026-10-20 10:00:00","errors":{{"Ngân hàng không hợp lệ."}},"data":{}}, summary="Result error: bank_id not found.")
     *         )
     *     )
     * )
     */
    public function getBankAccountName()
    {
        $withdrawPaymenthotLogService = new WithdrawPaymenthotLogService();
        $arrParams = request(['bank_id', 'bank_code', 'bank_account_number']);

        /**
         * Có bank_id thì lấy bank_code từ bảng bank
         */
        if (!empty($arrParams["bank_id"])) {
            $objBank = Bank::where('id', $arrParams["bank_id"])->first();
            if (!$objBank) {
                return $withdrawPaymenthotLogService->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Ngân hàng không hợp lệ.")]
                ])->result();
            }
            $arrParams["bank_code"] = $objBank->short_code;
        }

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