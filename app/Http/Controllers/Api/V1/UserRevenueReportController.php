<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserRevenueReportService;
use App\Services\UserService;
use App\Services\UserWithdrawService;
use App\Utilities\General;

class UserRevenueReportController extends BaseController
{

    private $userRevenueReportService = null;
    private $userService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRevenueReportService $userRevenueReportService)
    {
        $this->userRevenueReportService = $userRevenueReportService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['report_at_from'])) {
            $arrParams["query_greater_than_equato"]["user_revenue_reports.report_at"] = General::formatInputDay($arrParams["query"]['report_at_from'] . " 00:00:00");
            unset($arrParams["query"]["report_at_from"]);
        }

        if (!empty($arrParams["query"]['report_at_to'])) {
            $arrParams["query_less_than_equato"]["user_revenue_reports.report_at"] = General::formatInputDay($arrParams["query"]['report_at_to'] . " 23:59:59");
            unset($arrParams["query"]["report_at_to"]);
        }

        if (!empty($arrParams["query"]['user_revenue_reports.user_id'])) {
            $users = (array) $arrParams["query"]['user_revenue_reports.user_id'];
            $arrParams["query_in_list"]["user_revenue_reports.user_id"] = $users;
            unset($arrParams["query"]['user_revenue_reports.user_id']);
        }

        return response()->json($this->userRevenueReportService->getList($arrParams));
    }

    public function exportExcel()
    {
        $arrParams = request(['query']);
        if (!empty($arrParams["query"]['report_at_from'])) {
            $arrParams["query_greater_than_equato"]["user_revenue_reports.report_at"] = General::formatInputDay($arrParams["query"]['report_at_from'] . " 00:00:00");
            unset($arrParams["query"]["report_at_from"]);
        }

        if (!empty($arrParams["query"]['report_at_to'])) {
            $arrParams["query_less_than_equato"]["user_revenue_reports.report_at"] = General::formatInputDay($arrParams["query"]['report_at_to'] . " 23:59:59");
            unset($arrParams["query"]["report_at_to"]);
        }

        if (!empty($arrParams["query"]['user_revenue_reports.user_id'])) {
            $users = (array) $arrParams["query"]['user_revenue_reports.user_id'];
            $arrParams["query_in_list"]["user_revenue_reports.user_id"] = $users;
            unset($arrParams["query"]['user_revenue_reports.user_id']);
        }

        return response()->json($this->userRevenueReportService->exportExcel($arrParams));
    }

}