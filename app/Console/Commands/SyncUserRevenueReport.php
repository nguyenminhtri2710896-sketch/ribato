<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\UserRevenueReport;
use App\Models\UserWithdraw;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SyncUserRevenueReport extends Command
{
    protected $signature = 'user:sync-revenue-report {--from=} {--to=}';

    protected $description = 'Đồng bộ số liệu lợi nhuận theo ngày cho giao dịch nạp/rút';

    private const TRANSACTION_SUCCESS_STATUSES = [2, 6];
    private const WITHDRAW_SUCCESS_STATUSES = [2];
    private const TYPE_COLLECTION = 1;
    private const TYPE_PAYOUT = 2;

    public function handle(): int
    {
        $from = $this->option('from');
        $to = $this->option('to');

        try {
            $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->startOfDay();
            $toDate = $to ? Carbon::parse($to)->startOfDay() : $fromDate->clone();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        if ($fromDate->greaterThan($toDate)) {
            [$fromDate, $toDate] = [$toDate->clone(), $fromDate->clone()];
        }

        $cursor = $fromDate->clone();
        while ($cursor->lte($toDate)) {
            $this->syncForDate($cursor->clone());
            $cursor->addDay();
        }

        return self::SUCCESS;
    }

    private function syncForDate(Carbon $date): void
    {
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $this->syncCollection($start, $end);
        $this->syncPayout($start, $end);
    }

    private function syncCollection(Carbon $start, Carbon $end): void
    {
        $rows = Transaction::selectRaw('user_id,
            COALESCE(SUM(referal_fee),0) AS total_referal_fee,
            COALESCE(SUM(gateway_fee),0) AS total_gateway_fee,
            COALESCE(SUM(profit),0) AS total_profit,
            COALESCE(SUM(amount),0) AS total_transaction_amount,
            COALESCE(SUM(fee),0) AS total_transaction_fee')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status_id', self::TRANSACTION_SUCCESS_STATUSES)
            ->whereNull('deleted_at')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->get();

        foreach ($rows as $row) {
            $this->upsertReport(
                (int) $row->user_id,
                self::TYPE_COLLECTION,
                'Giao dịch nạp',
                [
                    'total_referal_fee' => (float) $row->total_referal_fee,
                    'total_gateway_fee' => (float) $row->total_gateway_fee,
                    'total_profit' => (float) $row->total_profit,
                    'total_transaction_amount' => (float) $row->total_transaction_amount,
                    'total_transaction_fee' => (float) $row->total_transaction_fee,
                ],
                $start
            );
        }
    }

    private function syncPayout(Carbon $start, Carbon $end): void
    {
        $rows = UserWithdraw::selectRaw('user_id,
            COALESCE(SUM(referal_fee),0) AS total_referal_fee,
            COALESCE(SUM(gateway_fee),0) AS total_gateway_fee,
            COALESCE(SUM(profit),0) AS total_profit,
            COALESCE(SUM(amount),0) AS total_transaction_amount,
            COALESCE(SUM(fee),0) AS total_transaction_fee')
            ->whereNotIn('gateway_id', [0, 7])
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status_id', self::WITHDRAW_SUCCESS_STATUSES)
            ->whereNull('deleted_at')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->get();

        foreach ($rows as $row) {
            $this->upsertReport(
                (int) $row->user_id,
                self::TYPE_PAYOUT,
                'Giao dịch rút',
                [
                    'total_referal_fee' => (float) $row->total_referal_fee,
                    'total_gateway_fee' => (float) $row->total_gateway_fee,
                    'total_profit' => (float) $row->total_profit,
                    'total_transaction_amount' => (float) $row->total_transaction_amount,
                    'total_transaction_fee' => (float) $row->total_transaction_fee,
                ],
                $start
            );
        }
    }

    private function upsertReport(int $userId, int $typeId, string $name, array $metrics, Carbon $reportDate): void
    {
        if ($userId <= 0) {
            return;
        }

        UserRevenueReport::updateOrCreate(
            [
                'user_id' => $userId,
                'type_id' => $typeId,
                'report_yyyymmdd' => $reportDate->format('Ymd'),
            ],
            array_merge($metrics, [
                'name' => $name,
                'report_at' => $reportDate->copy()->startOfDay(),
            ])
        );
    }
}

