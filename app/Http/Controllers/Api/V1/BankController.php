<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\BankService;
use App\Services\UserService;
use App\Utilities\General;

class BankController extends BaseController
{

    private $bankService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BankService $bankService)
    {
        $this->bankService = $bankService;
    }


    /**
     * @OA\Post(
     *     path="/api/bank/get-list",
     *     summary="Danh sách  ngân hàng",
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
        $arrParams = request(['page', 'limit', 'query', 'query_in_list', 'sort']);
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["name"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }
        return response()->json($this->bankService->getList($arrParams));
    }

    public function select2GetList()
    {
        $arrParams = request(['page', 'limit', 'query', 'query_in_list', 'sort']);
        if (!empty($arrParams["query"]["name"])) {
            $arrParams["query_or_like"] = [
                "banks.name" => $arrParams["query"]["name"],
                "banks.short_name" => $arrParams["query"]["name"],
                "banks.short_code" => $arrParams["query"]["name"]
            ];
            unset($arrParams["query"]["name"]);
        }
        return response()->json($this->bankService->responseSelect2($this->bankService->getList($arrParams)));
    }
    public function add()
    {
        $arrParams = request(['']);
        return response()->json($this->bankService->add($arrParams));
    }


    public function update()
    {
        $arrParams = request(['id']);
        return response()->json($this->bankService->update($arrParams));
    }

    public function getDetail()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        return response()->json($this->bankService->getDetail($arrParams));
    }
}