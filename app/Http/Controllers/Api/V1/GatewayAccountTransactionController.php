<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\GatewayAccountTransactionService;
use App\Utilities\General;

class GatewayAccountTransactionController extends BaseController
{

    private $gatewayAccountTransactionService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(GatewayAccountTransactionService $gatewayAccountTransactionService)
    {
        $this->gatewayAccountTransactionService = $gatewayAccountTransactionService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["gateway_account_transactions.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["gateway_account_transactions.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
            unset($arrParams["query"]["created_at_to"]);
        }
        return response()->json($this->gatewayAccountTransactionService->getList($arrParams));
    }



    public function ajaxAddMoney()
    {
        $arrParams = request(['amount', 'note', 'gateway_account_id', 'user_id']);
        return response()->json($this->gatewayAccountTransactionService->recharge($arrParams));

    }

    public function ajaxDeductMoney()
    {
        $arrParams = request(['amount', 'note', 'gateway_account_id', 'user_id']);
        return response()->json($this->gatewayAccountTransactionService->withDrawal($arrParams));

    }
}