<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\V1\BaseController;
use App\Services\BankService;

class BankController extends BaseController
{
    private $bankService = null;

    public function __construct(BankService $bankService)
    {
        $this->bankService = $bankService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'query_in_list', 'sort']);
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["name"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }
        return response()->json($this->bankService->getList($arrParams));
    }
}
