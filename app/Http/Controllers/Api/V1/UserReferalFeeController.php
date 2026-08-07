<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserReferalFeeService;

class UserReferalFeeController extends BaseController
{

    private $userReferalFeeService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserReferalFeeService $userReferalFeeService)
    {
        $this->userReferalFeeService = $userReferalFeeService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["name"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }
        return response()->json($this->userReferalFeeService->getList($arrParams));
    }

    public function getDetail()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        return response()->json($this->userReferalFeeService->getDetail($arrParams));
    }


    public function update()
    {
        $arrParams = request()->all();
        return response()->json($this->userReferalFeeService->update($arrParams));
    }
}
