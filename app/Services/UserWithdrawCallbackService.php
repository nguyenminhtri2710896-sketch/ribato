<?php

namespace App\Services;

use App\Jobs\PayoutCallbackResultJob;
use App\Models\User;
use App\Models\UserWithdraw;
use App\Models\UserWithdrawCallback;

class UserWithdrawCallbackService extends AbstractService
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
            (new UserWithdrawCallback())->getFillable(),
            (new UserWithdraw())->getFillable(),
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
            $arrParams["query"]["user_withdraws.user_id"] = auth()->id();
        }

        $intPage = max(1, (int) ($arrParams["page"] ?? 1));
        $intLimit = max(1, (int) ($arrParams["limit"] ?? 20));
        $intOffset = ($intPage - 1) * $intLimit;

        $objCallbacks = UserWithdrawCallback::select([
            'user_withdraw_callbacks.*',
            'user_withdraws.id as user_withdraw_id',
            'user_withdraws.trans_code as trans_code',
            'user_withdraws.ref_code as withdraw_ref_code',
            'user_withdraws.callback_status_id as callback_status_id',
            'user_withdraws.status_id as withdraw_status_id',
            'user_withdraws.callback_total_retry as callback_total_retry',
            'user_withdraws.user_id as withdraw_user_id',
            'user_withdraws.user_email as withdraw_user_email',
            'user_withdraws.amount as amount',
            'user_withdraws.fee as fee',
            'users.email as user_email',
            'users.fullname as user_fullname',
        ])
            ->leftJoin('user_withdraws', 'user_withdraws.id', '=', 'user_withdraw_callbacks.user_withdraw_id')
            ->leftJoin('users', 'users.id', '=', 'user_withdraws.user_id');

        $objCallbacks = $this->getListBuilder($objCallbacks, $arrParams, $this->arrFillable);
        $objTotal = clone $objCallbacks;
        $intTotal = $objTotal->count();

        if (empty($arrParams["sort"])) {
            $objCallbacks = $objCallbacks->orderBy('user_withdraw_callbacks.id', 'DESC');
        }

        $objCallbacks = $objCallbacks->offset($intOffset)->limit($intLimit)->get();

        return $this->setStatusCode(0)->setData([
            'callbacks' => $objCallbacks,
            'records_total' => $intTotal,
            'status' => self::$arrCallbackStatus,
            'withdraw_status' => UserWithdrawService::$arrStatusId,
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
            $arrParams["query"]["user_withdraw_callbacks.id"] = $arrParams["id"];
            unset($arrParams["id"]);
        }

        $objCallback = UserWithdrawCallback::select([
            'user_withdraw_callbacks.*',
            'user_withdraws.id as user_withdraw_id',
            'user_withdraws.trans_code as trans_code',
            'user_withdraws.ref_code as withdraw_ref_code',
            'user_withdraws.callback_status_id as callback_status_id',
            'user_withdraws.status_id as withdraw_status_id',
            'user_withdraws.callback_total_retry as callback_total_retry',
            'user_withdraws.user_id as withdraw_user_id',
            'user_withdraws.user_email as withdraw_user_email',
            'user_withdraws.amount as amount',
            'user_withdraws.fee as fee',
            'users.email as user_email',
            'users.fullname as user_fullname',
        ])
            ->leftJoin('user_withdraws', 'user_withdraws.id', '=', 'user_withdraw_callbacks.user_withdraw_id')
            ->leftJoin('users', 'users.id', '=', 'user_withdraws.user_id');

        $objCallback = $this->getListBuilder($objCallback, $arrParams, $this->arrFillable)->first();

        if (!$objCallback) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        if (!$this->canManage($objCallback->withdraw_user_id ?? $objCallback->user_id)) {
            return $this->setStatusCode(403)->setMessage('')->setData([])->setErrors([
                [__('Bạn không có quyền thực hiện chức năng này.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData([
            'callback' => $objCallback,
            'status' => self::$arrCallbackStatus,
            'withdraw_status' => UserWithdrawService::$arrStatusId,
        ])->result();
    }

    public function resend(array $arrParams = [])
    {
        $intWithdrawId = $arrParams["user_withdraw_id"] ?? null;
        if (empty($intWithdrawId)) {
            return $this->setStatusCode(414)->setMessage('')->setData([])->setErrors([
                [__('Vui lòng chọn giao dịch cần gửi lại IPN.')]
            ])->result();
        }

        $objUserWithdraw = UserWithdraw::where('id', $intWithdrawId)->first();
        if (!$objUserWithdraw) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy giao dịch.')]
            ])->result();
        }

        if (!$this->canManage($objUserWithdraw->user_id)) {
            return $this->setStatusCode(403)->setMessage('')->setData([])->setErrors([
                [__('Bạn không có quyền thực hiện chức năng này.')]
            ])->result();
        }

        $objUserWithdraw->callback_total_retry = 0;
        $objUserWithdraw->callback_status_id = 1;
        $objUserWithdraw->save();

        dispatch(new PayoutCallbackResultJob([
            'id' => $objUserWithdraw->id,
        ]))->onQueue('callback');

        return $this->setStatusCode(0)->setMessage(__('Đã gửi lại IPN.'))->setData([
            'user_withdraw_id' => $objUserWithdraw->id
        ])->result();
    }
}



