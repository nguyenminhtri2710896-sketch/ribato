<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\V1\BaseController;
use App\Services\UserService;
use App\Services\UserWithdrawService;
use App\Utilities\General;

class UserWithdrawController extends BaseController
{
    private $userWithdrawService = null;
    private $userService = null;

    public function __construct(UserWithdrawService $userWithdrawService, UserService $userService)
    {
        $this->userWithdrawService = $userWithdrawService;
        $this->userService         = $userService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["user_withdraws.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["user_withdraws.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
            unset($arrParams["query"]["created_at_to"]);
        }

        if (!empty($arrParams["query"]['updated_at_from'])) {
            $arrParams["query_greater_than"]["user_withdraws.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_from']);
            unset($arrParams["query"]["updated_at_from"]);
        }

        if (!empty($arrParams["query"]['updated_at_to'])) {
            $arrParams["query_less_than"]["user_withdraws.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_to'] . " 23:59:59");
            unset($arrParams["query"]["updated_at_to"]);
        }

        if (!empty($arrParams["query"]['bank_account_name'])) {
            $arrParams["query_like"]["user_withdraws.bank_account_name"] = $arrParams["query"]['bank_account_name'];
            unset($arrParams["query"]["bank_account_name"]);
        }

        if (!empty($arrParams["query"]['remark'])) {
            $arrParams["query_like"]["user_withdraws.remark"] = $arrParams["query"]['remark'];
            unset($arrParams["query"]["remark"]);
        }

        if (!empty($arrParams["query"]['gateway_id'])) {
            $arrParams["query"]["user_withdraws.gateway_id"] = $arrParams["query"]['gateway_id'];
            unset($arrParams["query"]["gateway_id"]);
        }

        $arrParams["query"]["is_show"] = 1;
        $arrParams["query"]["user_id"] = auth()->user()->id;
        return response()->json($this->userWithdrawService->getList($arrParams));
    }

    public function add()
    {
        $arrParams            = request(['user_business_id', 'bank_id', 'type_id', 'bank_account_number', 'bank_account_name', 'amount', 'remark', 'otp', 'ref_code']);
        $arrParams["user_id"] = auth()->user()->id;

        if (!empty(auth()->user()->authy_2factor)) {
            $resultCheckAuthenticator = $this->userService->checkAuthenticator($arrParams);
            if ($resultCheckAuthenticator["error_code"] != 0) {
                return $resultCheckAuthenticator;
            }
        }

        $arrParams["platform"] = "api";
        return response()->json($this->userWithdrawService->addV2($arrParams));
    }
}
