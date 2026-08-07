<?php

namespace App\Http\Controllers\Mod\V1;

use App\Services\ReportService;
use App\Services\TransactionService;
use App\Utilities\General;

class ReportController extends BaseController
{

    private $reportService = null;
    private $transactionService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ReportService $reportService, TransactionService $transactionService)
    {
        $this->reportService = $reportService;
        $this->transactionService = $transactionService;
    }

    public function ajaxGetTotalTransactionAmount()
    {
        $arrParams = request(['query']);
        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["transactions.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["transactions.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
            unset($arrParams["query"]["created_at_to"]);
        }
        $arrParams["query"]["user_id"] = auth()->user()->user_id;
        return response()->json($this->reportService->totalTransactionAmount($arrParams));
    }
}