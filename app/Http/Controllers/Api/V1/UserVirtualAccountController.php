<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserVirtualAccountService;

class UserVirtualAccountController extends BaseController
{

    private $userVirtualAccountService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserVirtualAccountService $userVirtualAccountService)
    {
        $this->userVirtualAccountService = $userVirtualAccountService;
    }

    /**
     * @OA\Post(
     *     path="/api/user-virtual-account/get-list",
     *     summary="Danh sách VA",
     *     tags={"Virtual Account"},
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
     *                     property="sign",
     *                     type="string"
     *                 ),
     *                 example={"sign": "signiture data"}
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
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["bank_account_number"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }

        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }

        $result = $this->userVirtualAccountService->getList($arrParams);
        if ($result["error_code"] != 0) {
            return $result;
        }

        if (!auth()->user()->full_access) {
            foreach ($result["data"]["user_virtual_accounts"] ?? [] as $objUservirtualAccount) {
                $arrItem = $objUservirtualAccount->toArray();
                if (isset($arrItem["gateway_id"])) {
                    unset($arrItem["gateway_id"]);
                }

                if (isset($arrItem["gateway_name"])) {
                    unset($arrItem["gateway_name"]);
                }
                $resultData[] = $arrItem;
            }
        } else {
            $resultData = $result["data"]["user_virtual_accounts"];
        }
        $result["data"]["user_virtual_accounts"] = $resultData;

        return response()->json($result);
    }

    public function select2GetList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]["name"])) {
            $arrParams["query_or_like"] = [
                "user_virtual_accounts.bank_account_name" => $arrParams["query"]["name"],
                "user_virtual_accounts.bank_account_number" => $arrParams["query"]["name"],
            ];
            unset($arrParams["query"]["name"]);
        }

        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->responseSelect2($this->userVirtualAccountService->getList($arrParams)));
    }
    public function add()
    {
        $arrParams = request()->all();
        if (!auth()->user()->full_access) {
            $arrParams["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->add($arrParams));
    }

    public function getDetail()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->getDetail($arrParams));
    }

    public function changeStatus()
    {
        $arrParams = request()->all();
        if (!auth()->user()->full_access) {
            $arrParams["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->changeStatus($arrParams));
    }
}