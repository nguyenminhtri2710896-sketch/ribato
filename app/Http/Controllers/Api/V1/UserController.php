<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserService;
use App\Utilities\General;

class UserController extends BaseController
{

    private $userService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);


        if (!empty($arrParams["query"]['email'])) {
            $arrParams["query_like"]["users.email"] = $arrParams["query"]['email'];
            unset($arrParams["query"]["email"]);
        }

        if (!empty($arrParams["query"]['phone'])) {
            $arrParams["query_like"]["users.phone"] = $arrParams["query"]['phone'];
            unset($arrParams["query"]["phone"]);
        }

        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["users.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["users.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to']);
            unset($arrParams["query"]["created_at_to"]);
        }
        return response()->json($this->userService->getList($arrParams));
    }

    public function select2GetList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]["name"])) {
            $arrParams["query_or_like"] = [
                "users.email" => $arrParams["query"]["name"],
                "users.fullname" => $arrParams["query"]["name"]
            ];
            unset($arrParams["query"]["name"]);
        }
        return response()->json($this->userService->responseSelect2($this->userService->getList($arrParams)));
    }
    public function add()
    {
       
        $arrParams = request(['for_control_type', 'number_day_for_control', 'group_id', 'user_parent_id', 'first_name', 'last_name', 'phone', 'email', 'password', 'password_confirmation', 'city_id', 'district_id', 'ward_id', 'company_name', 'address', 'gateway_withdraw_ids', 'gateway_collection_ids']);
        return response()->json($this->userService->add($arrParams));
    }


    public function update()
    {
       
        $arrParams       = request(['for_control_type', 'number_day_for_control','user_id', 'group_id', 'user_parent_id', 'first_name', 'last_name', 'password', 'password_confirmation', 'city_id', 'district_id', 'ward_id', 'company_name', 'address', 'gateway_withdraw_ids', 'gateway_collection_ids']);
        $arrParams["id"] = $arrParams["user_id"];
        return response()->json($this->userService->update($arrParams));
    }

    public function delete()
    {
      
        $arrParams = request(['id']);
        return response()->json($this->userService->delete($arrParams));
    }

    public function updateActiveAndDeactive()
    {

        $arrParams = request(['id', 'actived']);
        return response()->json($this->userService->updateActiveAndDeactive($arrParams));
    }

    public function updateWithdrawLimit()
    {
        $arrParams = request(['user_id', 'withdraw_limit_in_day', 'withdraw_limit_bank_account_in_day', 'lock_withdraw', 'allow_withdraw_day']);
        return response()->json($this->userService->updateWithdrawLimit($arrParams));
    }

    public function getDetail()
    {
      
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        // print_r( $arrParams);exit;
        return response()->json($this->userService->getDetail($arrParams));
    }


    public function changePassword()
    {
        $arrParams = request(['user_id', 'old_password', 'password', 'password_confirmation']);
        return response()->json($this->userService->resetPassword($arrParams));
    }

    public function ajaxExportExcel()
    {
        $arrParams = request(['query', 'sort']);

        if (!empty($arrParams["query"]['email'])) {
            $arrParams["query_like"]["users.email"] = $arrParams["query"]['email'];
            unset($arrParams["query"]["email"]);
        }

        if (!empty($arrParams["query"]['phone'])) {
            $arrParams["query_like"]["users.phone"] = $arrParams["query"]['phone'];
            unset($arrParams["query"]["phone"]);
        }

        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["users.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["users.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to']);
            unset($arrParams["query"]["created_at_to"]);
        }
        return response()->json($this->userService->exportExcel($arrParams));
    }

}