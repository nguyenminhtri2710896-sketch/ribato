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

        if ((auth()->user()->id == 900 || auth()->user()->id == 897 || auth()->user()->id == 904 || auth()->user()->id == 16 || auth()->user()->id == 61) && empty($arrParams["show_full_transaction"]) && empty($arrParams["query"]["show_full_transaction"])) {
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
     *     summary="Tạo yêu cầu rút tiền",
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
     *                     property="bank_account_number",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="bank_account_name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="bank_id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="remark",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="sign",
     *                     type="string"
     *                 ),
     *                 example={"bank_account_number": "1021312323","bank_account_name":"NGUYEN VAN A","bank_id":"get to list bank","remark":"noi dung ck","amount":100, "sign": "signiture data"}
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