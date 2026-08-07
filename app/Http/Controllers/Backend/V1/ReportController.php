<?php

namespace App\Http\Controllers\Backend\V1;

use App\Services\AbstractService;
use Curl\Curl;

class ReportController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
    }

    public function index()
    {
        return view("backend.".config('app.backend_version').".report.index")->with([]);
    }

    public function user()
    {
        return view("backend.".config('app.backend_version').".report.user")->with([]);
    }

    public function gateway()
    {
        return view("backend.".config('app.backend_version').".report.gateway")->with([]);
    }

    public function byDay()
    {
        return view("backend.".config('app.backend_version').".report.by-day")->with([]);
    }

    public function revenuePaymenthot()
    {
        return view("backend.".config('app.backend_version').".report.revenue-paymenthot")->with([]);
    }


    public function getListRevenuePaymenthot()
    {

        $_curl = new Curl();
        $_curl->setTimeout(40);
        $_curl->setConnectTimeout(40);
        $_curl->setUserAgent('Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36');
        $_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
        $_curl->setHeader('api-token', 'e59c23e68a1f66004cff7b3a0b88cc94');
        $_curl->get("https://uat.ribato.com/api/report/get-revenue-paymenthot");
        $arrResponse = json_decode($_curl->rawResponse, true);
        $arrReports = $arrResponse["data"]["report"] ?? [];
        $arrResult = [];
        $totalCurrentBalance = 0;
        $totalWithdrawTotalAmount = 0;
        $totalWithdrawTotalFee = 0;
        $totalTransactionTotalAmount = 0;
        $totalTransactionTotalFee = 0;
        foreach ($arrReports as $arrReport) {
            $arrResult[] = [
                "web" => "Ribato",
                "email" => $arrReport["user"]["email"] ?? "",
                "fullname" => $arrReport["user"]["fullname"] ?? "",
                "current_balance" => $arrReport["current_balance"] ?? "",
                "withdraw_total_amount" => $arrReport["withdraw"]["total_amount"] ?? "",
                "withdraw_total_fee" => $arrReport["withdraw"]["total_fee"] ?? "",
                "transaction_total_amount" => $arrReport["transaction"]["total_amount"] ?? "",
                "transaction_total_fee" => $arrReport["transaction"]["total_fee"] ?? "",
                "fee" => $arrReport["fee"] ?? [],
            ];
            $totalCurrentBalance += $arrReport["current_balance"] ?? 0;
            $totalWithdrawTotalAmount += $arrReport["withdraw"]["total_amount"] ?? 0;
            $totalWithdrawTotalFee += $arrReport["withdraw"]["total_fee"] ?? 0;
            $totalTransactionTotalAmount += $arrReport["transaction"]["total_amount"] ?? 0;
            $totalTransactionTotalFee += $arrReport["transaction"]["total_fee"] ?? 0;
        }

        $abstractService = new AbstractService();


        $_curl->setHeader('api-token', '409b9f8bbb9610f96bf6cb2859329w12');
        $_curl->get("https://uat.vnpay.biz/api/report/get-revenue-paymenthot");
        $arrResponse = json_decode($_curl->rawResponse, true);
        $arrReports = $arrResponse["data"]["report"] ?? [];


        foreach ($arrReports as $arrReport) {
            $arrResult[] = [
                "web" => "Vnpay",
                "email" => $arrReport["user"]["email"] ?? "",
                "fullname" => $arrReport["user"]["fullname"] ?? "",
                "current_balance" => $arrReport["current_balance"] ?? "",
                "withdraw_total_amount" => $arrReport["withdraw"]["total_amount"] ?? "",
                "withdraw_total_fee" => $arrReport["withdraw"]["total_fee"] ?? "",
                "transaction_total_amount" => $arrReport["transaction"]["total_amount"] ?? "",
                "transaction_total_fee" => $arrReport["transaction"]["total_fee"] ?? "",
                "fee" => $arrReport["fee"] ?? [],
            ];

            $totalCurrentBalance += $arrReport["current_balance"] ?? 0;
            $totalWithdrawTotalAmount += $arrReport["withdraw"]["total_amount"] ?? 0;
            $totalWithdrawTotalFee += $arrReport["withdraw"]["total_fee"] ?? 0;
            $totalTransactionTotalAmount += $arrReport["transaction"]["total_amount"] ?? 0;
            $totalTransactionTotalFee += $arrReport["transaction"]["total_fee"] ?? 0;

        }

        $arrResult[] = [
            "web" => "TOTAL",
            "email" => "",
            "fullname" => "",
            "current_balance" => $totalCurrentBalance,
            "withdraw_total_amount" => $totalWithdrawTotalAmount,
            "withdraw_total_fee" => $totalWithdrawTotalFee,
            "transaction_total_amount" => $totalTransactionTotalAmount,
            "transaction_total_fee" => $totalTransactionTotalFee,
            "fee" => []
        ];

        return $abstractService->setStatusCode(0)->setData([
            'reports' => $arrResult,
            'records_total' => count($arrResult),
            'page' => (int) 1,
            'limit' => (int) 1000,
        ])->result();

    }
}