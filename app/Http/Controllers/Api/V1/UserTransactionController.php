<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserTransactionService;
use App\Utilities\General;

class UserTransactionController extends BaseController
{

    private $userTransactionService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserTransactionService $userTransactionService)
    {
        $this->userTransactionService = $userTransactionService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["user_transactions.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["user_transactions.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
            unset($arrParams["query"]["created_at_to"]);
        }
        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userTransactionService->getList($arrParams));
    }

    

    public function ajaxAddMoney()
    {
        $arrParams = request(['amount', 'note', 'user_id', 'wallet_id']);
        return response()->json($this->userTransactionService->recharge($arrParams));

    }

    public function ajaxDeductMoney()
    {
        $arrParams = request(['amount', 'note', 'user_id', 'wallet_id']);
        return response()->json($this->userTransactionService->withDrawal($arrParams));

    }

    public function exportExcel()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["user_transactions.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["user_transactions.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
            unset($arrParams["query"]["created_at_to"]);
        }
        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userTransactionService->exportExcel($arrParams));
    }

}