<?php

namespace App\Services;

use App\Jobs\TransactionCallbackResultJob;
use App\Models\Transaction;
use App\Models\TransactionCallback;
use App\Models\User;

class TransactionCallbackService extends AbstractService
{
    public static $arrCallbackStatus = [
        1 => [
            'name' => 'Chờ xử lý'
        ],
        2 => [
            'name' => 'Thành công'
        ],
        3 => [
            'name' => 'Thất bại'
        ],
    ];

    protected $arrFillable = [];

    public function __construct()
    {
        $this->arrFillable = array_unique(array_merge(
            (new TransactionCallback())->getFillable(),
            (new Transaction())->getFillable(),
            (new User())->getFillable()
        ));
    }

    protected function canManage(?int $userId): bool
    {
        if (auth()->user()->is_admin || auth()->user()->full_access) {
            return true;
        }

        if (empty($userId)) {
            return false;
        }

        return (int) auth()->id() === (int) $userId;
    }

    public function getList(array $arrParams = [])
    {
        if (!auth()->user()->is_admin && !auth()->user()->full_access) {
            $arrParams["query"]["transactions.user_id"] = auth()->id();
        }

        $intPage = max(1, (int) ($arrParams["page"] ?? 1));
        $intLimit = max(1, (int) ($arrParams["limit"] ?? 20));
        $intOffset = ($intPage - 1) * $intLimit;

        $objCallbacks = TransactionCallback::select([
            'transaction_callbacks.*',
            'transactions.id as transaction_id',
            'transactions.code as transaction_code',
            'transactions.ref_code as transaction_ref_code',
            'transactions.callback_status_id as callback_status_id',
            'transactions.callback_total_retry as callback_total_retry',
            'transactions.user_id as transaction_user_id',
            'transactions.user_email as transaction_user_email',
            'users.email as user_email',
            'users.fullname as user_fullname',
        ])
            ->leftJoin('transactions', 'transactions.id', '=', 'transaction_callbacks.transaction_id')
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id');

        $objCallbacks = $this->getListBuilder($objCallbacks, $arrParams, $this->arrFillable);
        $objTotal = clone $objCallbacks;
        $intTotal = $objTotal->count();

        if (empty($arrParams["sort"])) {
            $objCallbacks = $objCallbacks->orderBy('transaction_callbacks.id', 'DESC');
        }

        $objCallbacks = $objCallbacks->offset($intOffset)->limit($intLimit)->get();

        return $this->setStatusCode(0)->setData([
            'callbacks' => $objCallbacks,
            'records_total' => $intTotal,
            'status' => self::$arrCallbackStatus,
            'page' => $intPage,
            'limit' => $intLimit,
        ])->result();
    }

    public function detail(array $arrParams = [])
    {
        if (!isset($arrParams["query"]) || !is_array($arrParams["query"])) {
            $arrParams["query"] = [];
        }

        if (!empty($arrParams["id"])) {
            $arrParams["query"]["transaction_callbacks.id"] = $arrParams["id"];
            unset($arrParams["id"]);
        }

        if (!empty($arrParams["query"]["id"])) {
            $arrParams["query"]["transaction_callbacks.id"] = $arrParams["query"]["id"];
            unset($arrParams["query"]["id"]);
        }

        $objCallback = TransactionCallback::select([
            'transaction_callbacks.*',
            'transactions.id as transaction_id',
            'transactions.code as transaction_code',
            'transactions.ref_code as transaction_ref_code',
            'transactions.callback_status_id as callback_status_id',
            'transactions.callback_total_retry as callback_total_retry',
            'transactions.user_id as transaction_user_id',
            'transactions.user_email as transaction_user_email',
            'users.email as user_email',
            'users.fullname as user_fullname',
        ])
            ->leftJoin('transactions', 'transactions.id', '=', 'transaction_callbacks.transaction_id')
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id');

        $objCallback = $this->getListBuilder($objCallback, $arrParams, $this->arrFillable)->first();

        if (!$objCallback) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        if (!$this->canManage($objCallback->transaction_user_id ?? $objCallback->user_id)) {
            return $this->setStatusCode(403)->setMessage('')->setData([])->setErrors([
                [__('Bạn không có quyền thực hiện chức năng này.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData([
            'callback' => $objCallback,
            'status' => self::$arrCallbackStatus,
        ])->result();
    }

    public function resend(array $arrParams = [])
    {
        $intTransactionId = $arrParams["transaction_id"] ?? null;
        if (empty($intTransactionId)) {
            return $this->setStatusCode(414)->setMessage('')->setData([])->setErrors([
                [__('Vui lòng chọn giao dịch cần gửi lại IPN.')]
            ])->result();
        }

        $objTransaction = Transaction::where('id', $intTransactionId)->first();
        if (!$objTransaction) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy giao dịch.')]
            ])->result();
        }

        if (!$this->canManage($objTransaction->user_id)) {
            return $this->setStatusCode(403)->setMessage('')->setData([])->setErrors([
                [__('Bạn không có quyền thực hiện chức năng này.')]
            ])->result();
        }

        $objTransaction->callback_total_retry = 0;
        $objTransaction->callback_status_id = 1;
        $objTransaction->save();

        dispatch(new TransactionCallbackResultJob([
            'id' => $objTransaction->id,
        ]))->onQueue('callback');

        return $this->setStatusCode(0)->setMessage(__('Đã gửi lại IPN.'))->setData([
            'transaction_id' => $objTransaction->id
        ])->result();
    }
}


