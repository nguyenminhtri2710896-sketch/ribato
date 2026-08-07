<?php

namespace App\Http\Controllers\Api\V1;

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

    public function getTotalTransactionAmount()
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
        // $arrParams["query"]["status_id"] = 2;


        if (!auth()->user()->full_access && !auth()->user()->is_accountant) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->reportService->totalTransactionAmount($arrParams));
    }



    /**
     * @OA\Post(
     *     path="/api/report/qrcode-revenue",
     *     summary="Báo cáo giao dịch",
     *     tags={"Report"},
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
     *                     property="id",
     *                     type="number"
     *                 ),
     *                 example={"id": 1}
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
    public function qrcodeRevenue()
    {
        $arrParams = request()->all();
        if (!auth()->user()->full_access && !auth()->user()->is_accountant) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }

        $arrParams["query"]["status_id"] = 2;
        if (!empty($arrParams["id"])) {
            $arrParams["query"]["user_id_qrcode_id"] = $arrParams["id"];
        }
        return response()->json($this->reportService->sumTransation($arrParams));
    }

    public function getRevenuePaymenthot()
    {
        $arrParams = request()->all();
        if (!auth()->user()->full_access && !auth()->user()->is_accountant) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }

        $arrParams["query"]["status_id"] = 2;
        if (!empty($arrParams["id"])) {
            $arrParams["query"]["user_id_qrcode_id"] = $arrParams["id"];
        }
        return response()->json($this->reportService->getRevenuePaymenthot($arrParams));
    }

    public function getTopUser()
    {
        $arrParams = request()->all();
        // $arrParams["query"]["user_id"] = auth()->user()->id;
        return response()->json($this->reportService->getTopUser($arrParams));
    }

    public function revenuesByDay()
    {
        $arrParams = request()->all();
        return response()->json($this->reportService->revenuesByDay($arrParams));
    }

    public function revenuesByMonth()
    {
        $arrParams = request()->all();
        return response()->json($this->reportService->revenuesByMonth($arrParams));
    }

    public function profitChart()
    {
        $arrParams = request(['from_date', 'to_date', 'user_id']);
        return response()->json($this->reportService->profitChart($arrParams));
    }

    public function getSystemBalance()
    {

        return response()->json($this->reportService->totalSystemBalance());
    }


}