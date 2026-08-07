<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserIdQrcodeService;
use App\Services\UserService;
use App\Services\UserWithdrawService;
use App\Utilities\General;

class UserIdQrcodeController extends BaseController
{

    private $userIdQrcodeService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserIdQrcodeService $userIdQrcodeService)
    {
        $this->userIdQrcodeService = $userIdQrcodeService;
    }
    /**
     * @OA\Post(
     *     path="/api/user-id-qrcode/get-list",
     *     summary="Danh sách mã qr cá nhân, sale",
     *     tags={"QR Cá nhân , Qr Sale"},
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

        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userIdQrcodeService->getList($arrParams));
    }


    /**
     * @OA\Post(
     *     path="/api/user-id-qrcode/create",
     *     summary="Tạo Qrcode theo mã",
     *     tags={"QR Cá nhân , Qr Sale"},
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
     *                     property="user_bank_account_id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="code",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="note",
     *                     type="string"
     *                 ),
     *                 example={"user_bank_account_id":1,"name": "Nguyễn Văn A","code":"AA01","note":"ghi chú thông tin"}
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
    public function add()
    {
        $arrParams            = request(['user_bank_account_id', 'name', 'code']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userIdQrcodeService->add($arrParams));
    }


    /**
     * @OA\Post(
     *     path="/api/user-id-qrcode/va-get-list",
     *     summary="Lấy danh sách VA",
     *     tags={"QR Cá nhân , Qr Sale"},
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
    public function vaGetList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["name"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }
        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userIdQrcodeService->getList($arrParams));
    }

    public function delete()
    {
        $arrParams = request(['id']);
        if (!auth()->user()->full_access) {
            $arrParams["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userIdQrcodeService->delete($arrParams));
    }


    public function getDetail()
    {
        $arrParams = request(['query']);
        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userIdQrcodeService->getDetail($arrParams));
    }


}