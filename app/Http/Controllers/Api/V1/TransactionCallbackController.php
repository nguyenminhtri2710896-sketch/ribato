<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\TransactionCallbackService;
use App\Utilities\General;

class TransactionCallbackController extends BaseController
{
    protected $transactionCallbackService;

    public function __construct(TransactionCallbackService $transactionCallbackService)
    {
        $this->transactionCallbackService = $transactionCallbackService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);

        if (!empty($arrParams["query"]['transaction_code'])) {
            $arrParams["query_like"]["transactions.code"] = $arrParams["query"]['transaction_code'];
            unset($arrParams["query"]["transaction_code"]);
        }

        if (!empty($arrParams["query"]['ref_code'])) {
            $arrParams["query_like"]["transactions.ref_code"] = $arrParams["query"]['ref_code'];
            unset($arrParams["query"]["ref_code"]);
        }

        if (!empty($arrParams["query"]['status_id'])) {
            $arrParams["query"]["transactions.callback_status_id"] = $arrParams["query"]['status_id'];
            unset($arrParams["query"]["status_id"]);
        }

        if (!empty($arrParams["query"]['user_id'])) {
            $arrParams["query"]["transactions.user_id"] = $arrParams["query"]['user_id'];
        }

        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["transaction_callbacks.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["transaction_callbacks.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
            unset($arrParams["query"]["created_at_to"]);
        }

        return response()->json($this->transactionCallbackService->getList($arrParams));
    }

    public function detail()
    {
        $arrParams = request(['id', 'query']);
        return response()->json($this->transactionCallbackService->detail($arrParams));
    }

    public function resend()
    {
        $arrParams = request(['transaction_id']);
        return response()->json($this->transactionCallbackService->resend($arrParams));
    }
}


