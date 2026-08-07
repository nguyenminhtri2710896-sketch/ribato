<?php

namespace App\Services;

use App\Models\GatewayAccount;
use App\Models\PaymenthotAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBalance;
use App\Models\UserFee;
use App\Models\UserWithdraw;
use App\Models\UserRevenueReport;
use App\Utilities\General;

class ReportService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
    }

    public function totalTransactionAmount($arrParams = [])
    {
        $this->arrFillable = (new Transaction())->getFillable();
        $objTransaction = Transaction::select(\DB::raw('SUM(amount) as total_amount'));
        $objTransaction = $this->getListBuilder($objTransaction, $arrParams, $this->arrFillable);
        $objTransaction = $objTransaction->first();

        return $this->setStatusCode(0)->setData([
            'total_amount' => $objTransaction->total_amount,
            "params" => $arrParams,
        ])->result();

    }


    public function sumTransation($arrParams = [])
    {


        $objTransaction = Transaction::select(\DB::raw('SUM(amount) as total_amount, SUM(fee) as total_fee'));
        $objTransaction = $this->getListBuilder($objTransaction, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTransaction = $objTransaction->first();
        if (empty($objTransaction)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['report' => $objTransaction])->result();
    }


    public function getRevenuePaymenthot($arrParams = [])
    {

        $arrUserIds = [];
        $paymentHotAccounts = PaymenthotAccount::get();
        foreach ($paymentHotAccounts as $paymentHotAccount) {
            $arrUserIds[] = $paymentHotAccount->user_id;
        }

        $arrUserIds = array_unique($arrUserIds);
        $objUsers = User::where(function ($query) use ($arrUserIds) {
            $query->whereIn('id', $arrUserIds)->orWhereIn('withdraw_refer_user_id', $arrUserIds);
        })->get();

        $arrUserData = [];
        foreach ($objUsers as $objUser) {
            $arrUserIds[] = $objUser->id;
            $arrUserData[$objUser->id] = $objUser->toArray();
        }
        $arrUserIds = array_unique($arrUserIds);

        $objTransactions = Transaction::select(\DB::raw('SUM(amount) as total_amount, SUM(fee) as total_fee,user_id'))->whereIn('user_id', $arrUserIds)->where('status_id', 2)->groupBy('user_id')->get();
        $objUserWithdraws = UserWithdraw::select(\DB::raw('SUM(amount) as total_amount, SUM(fee) as total_fee,user_id'))->whereIn('user_id', $arrUserIds)->where('status_id', 2)->groupBy('user_id')->get();

        $arrResult = [];
        foreach ($objTransactions as $objTransaction) {
            $arrResult[$objTransaction->user_id]["transaction"]['total_amount'] = round($objTransaction->total_amount);
            $arrResult[$objTransaction->user_id]["transaction"]['total_fee'] = round($objTransaction->total_fee);
            $arrResult[$objTransaction->user_id]["user"] = [
                "email" => $arrUserData[$objTransaction->user_id]["email"] ?? [],
                "fullname" => $arrUserData[$objTransaction->user_id]["fullname"] ?? []
            ];
        }

        foreach ($objUserWithdraws as $objUserWithdraw) {
            $arrResult[$objUserWithdraw->user_id]["withdraw"]['total_amount'] = round($objUserWithdraw->total_amount);
            $arrResult[$objUserWithdraw->user_id]["withdraw"]['total_fee'] = round($objUserWithdraw->total_fee);
        }

        $objUserFees = UserFee::whereIn('user_id', $arrUserIds)->where('status_id', 2)->get();
        foreach ($objUserFees as $objUserFee) {
            $arrResult[$objUserFee->user_id]["fee"][] = [
                "name" => $objUserFee->name . " " . UserFeeService::$arrTypeId[$objUserFee->type_id]['name'] ?? "",
                "fee" => $objUserFee->fee,
                "min_fee" => $objUserFee->min_fee,
            ];
        }

        $objUserBalances = UserBalance::whereIn('user_id', $arrUserIds)->get();
        foreach ($objUserBalances as $objUserBalance) {
            $arrResult[$objUserBalance->user_id]["current_balance"] = $objUserBalance->balance;
        }


        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['report' => $arrResult])->result();
    }

    public function getTopUser($arrParams = [])
    {

        $strFromDate = date('Y-m-d 00:00:00');
        $strToDate = date('Y-m-d 23:59:59');

        $arrTotalBalance = [];
        $objUserBalances = UserBalance::join('users', 'users.id', 'user_balances.user_id')->orderBy('balance', 'DESC')->limit(20)->get();
        $arrUserId = [];
        foreach ($objUserBalances as $objUserBalance) {
            $arrTotalBalance[$objUserBalance->user_id] = [
                'email' => $objUserBalance->email,
                'balance' => (int) $objUserBalance->balance,
                'amount_withdraw' => 0,
                'amount_collection' => 0,
                'amount_collection_pending' => 0
            ];
            $arrUserId[] = $objUserBalance->user_id;
        }

        $objUserWithdraws = UserWithdraw::select(\DB::raw('SUM(amount) as amount,user_id'))->where('created_at', '>', $strFromDate)->where('created_at', '<=', $strToDate)->where('status_id', 2)->whereIn('user_id', $arrUserId)->groupBy('user_id')->get();

        foreach ($objUserWithdraws as $objUserWithdraw) {
            $arrTotalBalance[$objUserWithdraw->user_id]["amount_withdraw"] = (int) $objUserWithdraw->amount;
        }


        $objTransactions = Transaction::select(\DB::raw('SUM(amount) as amount,user_id'))->where('created_at', '>', $strFromDate)->where('created_at', '<=', $strToDate)->where('status_id', 2)->whereIn('user_id', $arrUserId)->groupBy('user_id')->get();
        foreach ($objTransactions as $objTransaction) {
            $arrTotalBalance[$objTransaction->user_id]["amount_collection"] = (int) $objTransaction->amount;
        }

        $objTransactions = Transaction::select(\DB::raw('SUM(amount) as amount,user_id'))->where('status_id', 6)->whereIn('user_id', $arrUserId)->groupBy('user_id')->get();
        foreach ($objTransactions as $objTransaction) {
            $arrTotalBalance[$objTransaction->user_id]["amount_collection_pending"] = (int) $objTransaction->amount;
        }

        $arrResult = [];
        foreach ($arrTotalBalance as $arrItem) {
            $arrResult["email"][] = $arrItem["email"];
            $arrResult["balance"][] = $arrItem["balance"];
            $arrResult["amount_withdraw"][] = $arrItem["amount_withdraw"];
            $arrResult["amount_collection"][] = $arrItem["amount_collection"];
            $arrResult["amount_collection_pending"][] = $arrItem["amount_collection_pending"];
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['reports' => $arrResult])->result();
    }

    public function revenuesByDay($arrParams = [])
    {

        $strFromDate = date('Y-m-1 00:00:00');
        $strToDate = date('Y-m-t 23:59:59');


        $objTransactions = Transaction::select(\DB::raw('SUM(amount) as amount,DATE_FORMAT(created_at, "%Y%m%d") as date'))->where('created_at', '>', $strFromDate)->where('created_at', '<=', $strToDate)->where('status_id', 2)->groupBy('date')->get();
        $arrTransactions = [];
        foreach ($objTransactions as $objTransaction) {
            $arrTransactions[$objTransaction->date]["amount_collection"] = (int) $objTransaction->amount;
        }


        $objTransactions = Transaction::select(\DB::raw('SUM(amount) as amount,DATE_FORMAT(created_at, "%Y%m%d") as date'))->where('created_at', '>', $strFromDate)->where('created_at', '<=', $strToDate)->where('status_id', 6)->groupBy('date')->get();
        foreach ($objTransactions as $objTransaction) {
            $arrTransactions[$objTransaction->date]["amount_collection_pending"] = (int) $objTransaction->amount;
        }
        $arrResult = [];
        for ($i = 1; $i <= date("t"); $i++) {
            $day = sprintf('%02d', $i);
            $key = date('Ym', strtotime($strToDate)) . $day;
            $arrResult["day"][] = $day . "-" . date('m-Y', strtotime($strToDate));
            $arrResult["amount_collection"][] = $arrTransactions[$key]["amount_collection"] ?? 0;
            $arrResult["amount_collection_pending"][] = $arrTransactions[$key]["amount_collection_pending"] ?? 0;
        }
        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['reports' => $arrResult])->result();
    }

    public function revenuesByMonth($arrParams = [])
    {

        $strFromDate = date('Y-01-01 00:00:00');
        $strToDate = date('Y-12-t 23:59:59');


        $objTransactions = Transaction::select(\DB::raw('SUM(amount) as amount,DATE_FORMAT(created_at, "%Y%m") as month'))->where('created_at', '>', $strFromDate)->where('created_at', '<=', $strToDate)->where('status_id', 2)->groupBy('month')->get();
        $arrTransactions = [];
        foreach ($objTransactions as $objTransaction) {
            $arrTransactions[$objTransaction->month]["amount_collection"] = (int) $objTransaction->amount;
        }


        $objTransactions = Transaction::select(\DB::raw('SUM(amount) as amount,DATE_FORMAT(created_at, "%Y%m") as month'))->where('created_at', '>', $strFromDate)->where('created_at', '<=', $strToDate)->where('status_id', 6)->groupBy('month')->get();
        foreach ($objTransactions as $objTransaction) {
            $arrTransactions[$objTransaction->month]["amount_collection_pending"] = (int) $objTransaction->amount;
        }
        $arrResult = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = sprintf('%02d', $i);
            $key = date('Y', strtotime($strToDate)) . $month;
            $arrResult["month"][] = $month . "-" . date('Y', strtotime($strToDate));
            $arrResult["amount_collection"][] = $arrTransactions[$key]["amount_collection"] ?? 0;
            $arrResult["amount_collection_pending"][] = $arrTransactions[$key]["amount_collection_pending"] ?? 0;
        }
        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['reports' => $arrResult])->result();
    }

    public function profitChart($arrParams = [])
    {
        $strFromDate = !empty($arrParams['from_date']) ? General::formatInputDay($arrParams['from_date']) : date('Y-m-d', strtotime('-29 days'));
        $strToDate = !empty($arrParams['to_date']) ? General::formatInputDay($arrParams['to_date']) : date('Y-m-d');

        $arrUserIdFilter = null;
        $userLabel = null;
        if (!empty($arrParams['user_id'])) {
            $arrUserIdFilter = $arrParams['user_id'];
        }

        if (strtotime($strFromDate) > strtotime($strToDate)) {
            [$strFromDate, $strToDate] = [$strToDate, $strFromDate];
        }

        // Giới hạn tối đa 120 ngày để tránh tải dữ liệu quá lớn
        $maxRangeDays = 120;
        $diffDays = (int) floor((strtotime($strToDate) - strtotime($strFromDate)) / 86400);
        if ($diffDays > $maxRangeDays) {
            $strFromDate = date('Y-m-d', strtotime($strToDate . ' -' . $maxRangeDays . ' days'));
        }

        $fromDateTime = $strFromDate . ' 00:00:00';
        $toDateTime = $strToDate . ' 23:59:59';

        $queryReports = UserRevenueReport::select(\DB::raw('DATE_FORMAT(report_at, "%Y%m%d") as report_day,
                SUM(CASE WHEN type_id = 1 THEN total_transaction_amount ELSE 0 END) as total_collection_amount,
                SUM(total_profit) as total_profit'))
            ->where('report_at', '>=', $fromDateTime)
            ->where('report_at', '<=', $toDateTime);

        if ($arrUserIdFilter) {
            $queryReports->whereIn('user_id', $arrUserIdFilter);
        }

        $objReports = $queryReports->groupBy('report_day')->orderBy('report_day', 'ASC')->get();

        $objOverallProfitQuery = UserRevenueReport::select(\DB::raw('SUM(total_profit) as total_profit'));
        if ($arrUserIdFilter) {
            $objOverallProfitQuery->whereIn('user_id', $arrUserIdFilter);
            // $users = User::whereIn('id', $arrUserIdFilter).get();å
            // if ($users) {
            //     $userLabel = "";
            //     foreach ($users as $user) {
            //         $userLabel .= $user->email ?? $user->fullname ?? ("User #" . implode(",", $arrUserIdFilter));
            //     }
            // }
        }
        $objOverallProfit = $objOverallProfitQuery->first();

        $arrReports = [];
        foreach ($objReports as $objReport) {
            $arrReports[$objReport->report_day] = [
                'collection' => (int) $objReport->total_collection_amount,
                'profit' => (int) $objReport->total_profit,
            ];
        }

        $arrCategories = [];
        $arrCollectionSeries = [];
        $arrProfitSeries = [];
        $intTotalProfit = 0;

        $current = strtotime($strFromDate);
        $end = strtotime($strToDate);
        while ($current <= $end) {
            $key = date('Ymd', $current);
            $collection = $arrReports[$key]['collection'] ?? 0;
            $profit = $arrReports[$key]['profit'] ?? 0;

            $arrCategories[] = date('d/m', $current);
            $arrCollectionSeries[] = $collection;
            $arrProfitSeries[] = $profit;
            $intTotalProfit += $profit;

            $current = strtotime('+1 day', $current);
        }

        return $this->setStatusCode(0)->setData([
            'categories' => $arrCategories,
            'series' => [
                'collection' => $arrCollectionSeries,
                'profit' => $arrProfitSeries,
            ],
            'total_profit' => $intTotalProfit,
            'overall_total_profit' => (int) ($objOverallProfit->total_profit ?? 0),
            'filters' => [
                'from_date' => date('d/m/Y', strtotime($strFromDate)),
                'to_date' => date('d/m/Y', strtotime($strToDate)),
                'user_id' => $arrUserIdFilter,
                'user_label' => $userLabel,
            ],
        ])->result();
    }

    public function totalSystemBalance($arrParams = [])
    {
        $totalUserBalance = UserBalance::sum('balance');
        $totalGatewayBalance = GatewayAccount::sum('balance');
        $totalGatewayPendingBalance = GatewayAccount::sum('pending_balance');

        return $this->setStatusCode(0)->setData([
            'total_user_balance' => (float) $totalUserBalance,
            'total_gateway_balance' => (float) $totalGatewayBalance,
            'total_gateway_pending_balance' => (float) $totalGatewayPendingBalance,
        ])->result();
    }
}
