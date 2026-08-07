<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserWithdrawCallbackService;
use App\Utilities\General;

class UserWithdrawCallbackController extends BaseController
{
    protected $userWithdrawCallbackService;

    public function __construct(UserWithdrawCallbackService $userWithdrawCallbackService)
    {
        $this->userWithdrawCallbackService = $userWithdrawCallbackService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);

        if (!empty($arrParams["query"]['trans_code'])) {
            $arrParams["query_like"]["user_withdraws.trans_code"] = $arrParams["query"]['trans_code'];
            unset($arrParams["query"]["trans_code"]);
        }

        if (!empty($arrParams["query"]['ref_code'])) {
            $arrParams["query_like"]["user_withdraws.ref_code"] = $arrParams["query"]['ref_code'];
            unset($arrParams["query"]["ref_code"]);
        }

        if (!empty($arrParams["query"]['status_id'])) {
            $arrParams["query"]["user_withdraws.callback_status_id"] = $arrParams["query"]['status_id'];
            unset($arrParams["query"]["status_id"]);
        }

        if (!empty($arrParams["query"]['user_id'])) {
            $arrParams["query"]["user_withdraws.user_id"] = $arrParams["query"]['user_id'];
        }

        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["user_withdraw_callbacks.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["user_withdraw_callbacks.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
            unset($arrParams["query"]["created_at_to"]);
        }

        return response()->json($this->userWithdrawCallbackService->getList($arrParams));
    }

    public function detail()
    {
        $arrParams = request(['id', 'query']);
        return response()->json($this->userWithdrawCallbackService->detail($arrParams));
    }

    public function resend()
    {
        $arrParams = request(['user_withdraw_id']);
        return response()->json($this->userWithdrawCallbackService->resend($arrParams));
    }
}


