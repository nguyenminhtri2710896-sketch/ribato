<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\AuthService;
use App\Services\TransactionService;
use App\Utilities\General;
use Illuminate\Http\Request;

class TransactionController extends BaseController
{

        protected $transactionService;
        public function __construct(TransactionService $transactionService)
        {
                $this->transactionService = $transactionService;
        }

        /**
         * @OA\Post(
         *     path="/api/transaction/create-payment",
         *     summary="Tạo giao dịch thanh toán",
         *     tags={"Transactions"},
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
         *                     property="ref_code",
         *                     type="string"
         *                 ),
         *                 @OA\Property(
         *                     property="amount",
         *                     type="integer"
         *                 ),
         *                 @OA\Property(
         *                     property="payment_success_url",
         *                     type="string"
         *                 ),
         *                 @OA\Property(
         *                     property="payment_cancel_url",
         *                     type="string"
         *                 ),
         *                 @OA\Property(
         *                     property="sign",
         *                     type="string"
         *                 ),
         *                 example={"ref_code": "12232", "amount": 20000, "payment_success_url": "url success return", "payment_cancel_url": "url cancel return", "sign": "signiture data"}
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

        public function createPayment(Request $request)
        {
                $arrParams                  = $request->all();
                $arrParams["user_id"]       = auth()->user()->id;
                $arrParams["user_token_id"] = auth()->user()->user_token_id;
                return $this->transactionService->createPayment($arrParams);
        }


        /**
         * @OA\Post(
         *     path="/api/transaction/create-payment-base",
         *     summary="Tạo giao dịch thanh toán cơ bản",
         *     tags={"Transactions"},
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
         *                     property="ref_code",
         *                     type="string"
         *                 ),
         *                 @OA\Property(
         *                     property="amount",
         *                     type="integer"
         *                 ),
         *                 @OA\Property(
         *                     property="payment_success_url",
         *                     type="string"
         *                 ),
         *                 @OA\Property(
         *                     property="payment_cancel_url",
         *                     type="string"
         *                 ),
         *                 @OA\Property(
         *                     property="sign",
         *                     type="string"
         *                 ),
         *                 example={"ref_code": "12232", "amount": 20000, "payment_success_url": "url success return", "payment_cancel_url": "url cancel return", "sign": "signiture data"}
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

        public function createPaymentBase(Request $request)
        {
                $arrParams                  = $request->all();
                $arrParams["user_id"]       = auth()->user()->id;
                $arrParams["user_token_id"] = auth()->user()->user_token_id;
                return $this->transactionService->createPayment($arrParams);
        }


        /**
         * @OA\Post(
         *     path="/api/transaction/get-list",
         *     summary="Get transaction list",
         *     description="Returns a paginated list of the authenticated partner's collection transactions, newest first. Filters are passed in <b>query</b> and are combined with AND; unknown keys are ignored.",
         *     tags={"Transactions"},
         *     @OA\Parameter(name="api-token", in="header", description="token allow create service", @OA\Schema(type="string")),
         *     @OA\RequestBody(
         *         @OA\MediaType(
         *             mediaType="application/json",
         *             @OA\Schema(
         *                 @OA\Property(
         *                     property="query", type="object", description="Optional filters.",
         *                     @OA\Property(property="code", type="string", example="FPAW41136030933", description="Transaction code generated by the system (exact match)."),
         *                     @OA\Property(property="ref_code", type="string", example="ABC123", description="Reference / order code (exact match)."),
         *                     @OA\Property(property="status_id", type="integer", enum={1,2,3,5,6,7}, example=2, description="Transaction status. <b>1</b>: processing, <b>2</b>: success, <b>3</b>: failed, <b>5</b>: pending review (amount mismatch), <b>6</b>: awaiting reconciliation, <b>7</b>: forwarded."),
         *                     @OA\Property(property="callback_status_id", type="integer", enum={1,2,3}, example=2, description="IPN delivery status. <b>1</b>: pending, <b>2</b>: success, <b>3</b>: failed."),
         *                     @OA\Property(property="bank_account_number", type="string", example="963699822100000003", description="Receiving account / VA number (exact match)."),
         *                     @OA\Property(property="content", type="string", example="noi dung", description="Transfer content (partial match)."),
         *                     @OA\Property(property="created_at_from", type="string", example="2026-10-01", description="Created from date (Y-m-d), inclusive."),
         *                     @OA\Property(property="created_at_to", type="string", example="2026-10-31", description="Created to date (Y-m-d), inclusive."),
         *                     @OA\Property(property="received_at_from", type="string", example="2026-10-01", description="Money received from date (Y-m-d), inclusive."),
         *                     @OA\Property(property="received_at_to", type="string", example="2026-10-31", description="Money received to date (Y-m-d), inclusive.")
         *                 ),
         *                 @OA\Property(property="sort", type="object", example={"id":"desc"}, description="Optional. Sort by column: {column: asc|desc}. Default is id desc."),
         *                 @OA\Property(property="limit", type="integer", example=10, description="Records per page. Default 10."),
         *                 @OA\Property(property="page", type="integer", example=1, description="Page number, starting from 1. Default 1."),
         *                 @OA\Property(property="sign", type="string", example="signiture data", description="Signature of the request."),
         *                 example={"query": {"status_id":2,"created_at_from":"2026-10-01","created_at_to":"2026-10-31"},"limit":10,"page":1, "sign": "signiture data"}
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="OK",
         *         @OA\JsonContent(
         *             @OA\Property(property="error_code", type="integer", example=0, description="<b>0</b>: success. Other values: error (401 unauthorized)."),
         *             @OA\Property(property="message", type="string", example="success", description="Result message."),
         *             @OA\Property(property="system_time", type="string", example="2026-10-21 10:00:00", description="Server time."),
         *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), description="Error messages. Empty on success."),
         *             @OA\Property(
         *                 property="data", type="object", description="Response payload.",
         *                 @OA\Property(
         *                     property="transactions", type="array", description="Transactions of the current page.",
         *                     @OA\Items(
         *                         type="object",
         *                         @OA\Property(property="id", type="integer", example=1, description="Transaction ID."),
         *                         @OA\Property(property="user_id", type="integer", example=10, description="Partner user ID."),
         *                         @OA\Property(property="code", type="string", example="FPAW41136030933", description="Transaction code generated by the system."),
         *                         @OA\Property(property="ref_code", type="string", example="ABC123", description="Reference / order code."),
         *                         @OA\Property(property="type_id", type="integer", example=1, description="Transaction type. <b>1</b>: collection (deposit), <b>2</b>: withdrawal."),
         *                         @OA\Property(property="status_id", type="integer", example=2, description="Transaction status. <b>1</b>: processing, <b>2</b>: success, <b>3</b>: failed, <b>5</b>: pending review (amount mismatch), <b>6</b>: awaiting reconciliation, <b>7</b>: forwarded."),
         *                         @OA\Property(property="callback_status_id", type="integer", example=2, description="IPN delivery status. <b>1</b>: pending, <b>2</b>: success, <b>3</b>: failed."),
         *                         @OA\Property(property="callback_total_retry", type="integer", example=1, description="Number of IPN delivery attempts."),
         *                         @OA\Property(property="currency", type="string", example="vnd", description="Currency."),
         *                         @OA\Property(property="amount", type="number", example=1000000, description="Transaction amount (VND)."),
         *                         @OA\Property(property="fee", type="number", example=6000, description="Fee (VND)."),
         *                         @OA\Property(property="amount_after_fee", type="number", example=994000, description="Amount (VND) after fee."),
         *                         @OA\Property(property="received_amount", type="number", example=994000, description="Amount (VND) credited to the partner."),
         *                         @OA\Property(property="content", type="string", example="noi dung chuyen khoan", description="Transfer content."),
         *                         @OA\Property(property="bank_short_name", type="string", example="BIDV", description="Receiving bank short name."),
         *                         @OA\Property(property="bank_short_code", type="string", example="BIDV", description="Receiving bank code."),
         *                         @OA\Property(property="bank_account_name", type="string", example="NGUYEN VAN A", description="Receiving account holder name."),
         *                         @OA\Property(property="bank_account_number", type="string", example="963699822100000003", description="Receiving account number (the VA number for VA transactions)."),
         *                         @OA\Property(property="received_at", type="string", example="2026-10-21 10:40:07", description="Time the money was received."),
         *                         @OA\Property(property="expired_at", type="string", example="2026-10-21 11:40:07", description="Payment expiry time."),
         *                         @OA\Property(property="created_at", type="string", example="2026-10-21 10:40:07", description="Created time."),
         *                         @OA\Property(property="updated_at", type="string", example="2026-10-21 10:40:07", description="Last updated time.")
         *                     )
         *                 ),
         *                 @OA\Property(property="records_total", type="integer", example=1, description="Total records matching the filters."),
         *                 @OA\Property(property="status", type="object", description="Map of transaction status_id to its name.", example={"1":{"name":"Đang xử lý"},"2":{"name":"Thành công"},"3":{"name":"Thất bại"}}),
         *                 @OA\Property(property="type", type="object", description="Map of transaction type_id to its name.", example={"1":{"name":"Nạp"},"2":{"name":"Rút"}}),
         *                 @OA\Property(property="page", type="integer", example=1, description="Current page."),
         *                 @OA\Property(property="limit", type="integer", example=10, description="Records per page."),
         *                 @OA\Property(property="params", type="object", description="The request parameters as received.")
         *             ),
         *             @OA\Examples(example="result_false", value={"error_code":401,"system_time":"2023-11-24 11:15:59","message":"access_token hết hạn hoặc không được phép sử dụng","errors":{"access_token hết hạn hoặc không được phép sử dụng"},"data":null}, summary="Result error."),
         *             @OA\Examples(example="result_success", value={"error_code":0,"message":"success","system_time":"2026-10-21 10:00:00","errors":{},"data":{"transactions":{{"id":1,"user_id":10,"code":"FPAW41136030933","ref_code":"ABC123","type_id":1,"status_id":2,"callback_status_id":2,"callback_total_retry":1,"currency":"vnd","amount":1000000,"fee":6000,"amount_after_fee":994000,"received_amount":994000,"content":"noi dung chuyen khoan","bank_short_name":"BIDV","bank_short_code":"BIDV","bank_account_name":"NGUYEN VAN A","bank_account_number":"963699822100000003","received_at":"2026-10-21 10:40:07","expired_at":"2026-10-21 11:40:07","created_at":"2026-10-21 10:40:07","updated_at":"2026-10-21 10:40:07"}},"records_total":1,"page":1,"limit":10}}, summary="Result success.")
         *         )
         *     )
         * )
         */

        public function getList()
        {
                $arrParams = request()->all();
                if (!empty($arrParams["query"]["user_id"])) {
                        $arrParams["query_difference"]["status_id"] = 1;
                }


                if (!auth()->user()->full_access && !auth()->user()->is_accountant) {
                        $arrParams["query"]["user_id"] = auth()->user()->id;
                }

                if (!empty($arrParams["query"]["content"])) {
                        $arrParams["query_or_like"] = [
                                "content" => $arrParams["query"]["content"]
                        ];
                        unset($arrParams["query"]["content"]);
                }

                if (!empty($arrParams["query"]["list_user_id"])) {
                        $arrParams["query_in_list"]["user_id"] = $arrParams["query"]["list_user_id"];
                        unset($arrParams["query"]["list_user_id"]);
                }

                // if ((auth()->user()->id == 900 || auth()->user()->id == 897 || auth()->user()->id == 904) && empty($arrParams["show_full_transaction"])) {
                //         $arrParams["query_not_like"]["content"] = "SA%";
                // }

                return response()->json($this->transactionService->getList($arrParams));
        }


        /**
         * @OA\Post(
         *     path="/api/transaction/get-detail",
         *     summary="Lấy chi tiết giao dịch",
         *     tags={"Transactions"},
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
         *                     property="sign",
         *                     type="string"
         *                 ),
         *                 example={"query": {"id":1}, "sign": "signiture data"}
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

        public function getDetail()
        {
                $arrParams = request()->all();
                if (!empty($arrParams["query"]["user_id"])) {
                        $arrParams["query_difference"]["status_id"] = 1;
                }
                if (!auth()->user()->full_access) {
                        $arrParams["query"]["user_id"] = auth()->user()->id;
                }
                return response()->json($this->transactionService->getDetail($arrParams));
        }

        /**
         * @OA\Post(
         *     path="/api/transaction/create-qr-payment",
         *     summary="Tạo QRCode thanh toán giao dịch",
         *     tags={"Transactions"},
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
         *                     property="amount",
         *                     type="string"
         *                 ),
         *                 example={"amount": 10000}
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
        public function createQrPayment()
        {
                $arrParams            = request()->all();
                $arrParams["user_id"] = auth()->user()->id;
                return response()->json($this->transactionService->createQrPayment($arrParams));
        }

        public function exportExcel()
        {
                $arrParams = request()->all();
                if (!empty($arrParams["query"]["user_id"])) {
                        $arrParams["query_difference"]["status_id"] = 1;
                }
                if (!auth()->user()->full_access && !auth()->user()->is_accountant) {
                        $arrParams["query"]["user_id"] = auth()->user()->id;
                }

                if (!empty($arrParams["query"]["list_user_id"])) {
                        $arrParams["query_in_list"]["user_id"] = $arrParams["query"]["list_user_id"];
                        unset($arrParams["query"]["list_user_id"]);
                }


                if (auth()->user()->full_access) {
                        return response()->json($this->transactionService->exportExcelFullAccess($arrParams));

                }

                if (auth()->user()->is_accountant) {
                        return response()->json($this->transactionService->exportExcelAccountant($arrParams));

                }
                return response()->json($this->transactionService->exportExcel($arrParams));
        }



}
