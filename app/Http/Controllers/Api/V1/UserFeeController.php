<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserFeeService;

class UserFeeController extends BaseController
{

    private $userFeeService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserFeeService $userFeeService)
    {
        $this->userFeeService = $userFeeService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["name"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }
        return response()->json($this->userFeeService->getList($arrParams));
    }

    public function getDetail()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        return response()->json($this->userFeeService->getDetail($arrParams));
    }


    public function update()
    {
        $arrParams = request()->all();
        return response()->json($this->userFeeService->update($arrParams));
    }
}
