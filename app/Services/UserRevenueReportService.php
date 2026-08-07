<?php

namespace App\Services;

use App\Exports\UserRevenueReportExport;
use App\Models\UserWithdraw;
use App\Models\UserRevenueReport;


class UserRevenueReportService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserWithdraw())->getFillable();
    }

    public static $arrTypeId = [
        1 => [
            'name' => 'Giao dịch nạp'
        ],
        2 => [
            'name' => 'Giao dịch rút'
        ]
    ];

    public function getList($arrParams = [])
    {

        $intPage               = $arrParams["page"] ?? 1;
        $intLimit              = $arrParams["limit"] ?? 10;
        $intOffset             = ($intPage - 1) * $intLimit;
        $this->arrFillable     = array_merge($this->arrFillable, (new UserRevenueReport())->getFillable());
        $objUserRevenueReports = UserRevenueReport::select(\DB::raw('user_revenue_reports.*,users.fullname as user_fullname,users.email as user_email'))
            ->leftJoin('users', 'users.id', 'user_revenue_reports.user_id');
        $objUserRevenueReports = $this->getListBuilder($objUserRevenueReports, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objUserRevenueReports;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUserRevenueReports = $objUserRevenueReports->orderBy("user_revenue_reports.id", "DESC");
        }
        $objUserRevenueReports = $objUserRevenueReports->offset($intOffset)->limit($intLimit)->get();

        $arrUserRevenueReports   = [];

        $objUserRevenueSumReport = UserRevenueReport::select(\DB::raw('SUM(total_referal_fee) as total_referal_fee ,SUM(total_gateway_fee) as total_gateway_fee,SUM(total_profit) as total_profit,SUM(total_transaction_amount) as total_transaction_amount,SUM(total_transaction_fee) as total_transaction_fee'));
        $objUserRevenueSumReport = $this->getListBuilder($objUserRevenueSumReport, $arrParams, $this->arrFillable)->first();

        // $arrUserRevenueReports[] = [
        //     "user_fullname" => "Tổng",
        //     "user_email" => "",
        //     "user_id" => 0,
        //     "total_referal_fee" => $objUserRevenueSumReport->total_referal_fee,
        //     "total_gateway_fee" => $objUserRevenueSumReport->total_gateway_fee,
        //     "total_profit" => $objUserRevenueSumReport->total_profit,
        //     "total_transaction_amount" => $objUserRevenueSumReport->total_transaction_amount,
        //     "total_transaction_fee" => $objUserRevenueSumReport->total_transaction_fee,
        //     "type_id" => 0,
        //     "report_at" => ""
        // ];


        // foreach ($objUserRevenueReports as $objUserRevenueReport) {
        //     $arrUserRevenueReports[] = [
        //         "user_fullname" => $objUserRevenueReport->user_fullname,
        //         "user_email" => $objUserRevenueReport->user_email,
        //         "user_id" => $objUserRevenueReport->user_id,
        //         "total_referal_fee" => $objUserRevenueReport->total_referal_fee,
        //         "total_gateway_fee" => $objUserRevenueReport->total_gateway_fee,
        //         "total_profit" => $objUserRevenueReport->total_profit,
        //         "total_transaction_amount" => $objUserRevenueReport->total_transaction_amount,
        //         "total_transaction_fee" => $objUserRevenueReport->total_transaction_fee,
        //         "type_id" => $objUserRevenueReport->type_id,
        //         "report_at" => $objUserRevenueReport->report_at
        //     ];
        // }



        return $this->setStatusCode(0)->setData([
            'user_revenue_reports' => $objUserRevenueReports,
            'user_revenue_sum_report' => $objUserRevenueSumReport,
            'records_total' => $intTotal,
            'type' => self::$arrTypeId,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }

    public function exportExcel($arrParams = [])
    {
        $this->arrFillable = array_merge((new UserWithdraw())->getFillable(), (new UserRevenueReport())->getFillable());
        $objUserRevenueReports = UserRevenueReport::select(\DB::raw('user_revenue_reports.*,users.fullname as user_fullname,users.email as user_email'))
            ->leftJoin('users', 'users.id', 'user_revenue_reports.user_id');
        $objUserRevenueReports = $this->getListBuilder($objUserRevenueReports, $arrParams, $this->arrFillable);
        $objUserRevenueReports = $objUserRevenueReports->orderBy("user_revenue_reports.id", "DESC")->get();

        if (!$objUserRevenueReports || $objUserRevenueReports->isEmpty()) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu để xuất.')]
            ])->result();
        }

        $objUserRevenueSumReport = UserRevenueReport::select(\DB::raw('SUM(total_referal_fee) as total_referal_fee ,SUM(total_gateway_fee) as total_gateway_fee,SUM(total_profit) as total_profit,SUM(total_transaction_amount) as total_transaction_amount,SUM(total_transaction_fee) as total_transaction_fee'));
        $objUserRevenueSumReport = $this->getListBuilder($objUserRevenueSumReport, $arrParams, $this->arrFillable)->first();

        $fileName = 'exports/export_user_revenue_report_' . time() . '.xlsx';
        $resultExport = \Excel::store(new UserRevenueReportExport([
            'reports' => $objUserRevenueReports,
            'summary' => $objUserRevenueSumReport,
            'type' => self::$arrTypeId,
        ]), $fileName, 'export-excel', null);

        if (!$resultExport) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Xuất file thất bại.')]])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Xuất báo cáo thành công.'))->setData([
            'url' => url("static/" . $fileName)
        ])->result();
    }
}