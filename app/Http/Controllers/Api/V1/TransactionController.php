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
         *     summary="Lấy danh sách giao dịch",
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
