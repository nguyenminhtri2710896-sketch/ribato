<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\V1\BaseController;
use App\Services\TransactionService;

class TransactionController extends BaseController
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

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

        return response()->json($this->transactionService->getList($arrParams));
    }

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
}
