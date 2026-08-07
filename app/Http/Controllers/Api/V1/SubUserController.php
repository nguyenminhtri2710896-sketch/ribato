<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\SubUserService;
use App\Services\UserService;
use App\Services\UserWithdrawService;
use App\Utilities\General;

class SubUserController extends BaseController
{

    private $subUserService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SubUserService $subUserService)
    {
        $this->subUserService = $subUserService;
    }
    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["sub_users.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than"]["sub_users.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
            unset($arrParams["query"]["created_at_to"]);
        }

        if (!empty($arrParams["query"]['updated_at_from'])) {
            $arrParams["query_greater_than"]["sub_users.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_from']);
            unset($arrParams["query"]["updated_at_from"]);
        }

        if (!empty($arrParams["query"]['updated_at_to'])) {
            $arrParams["query_less_than"]["sub_users.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_to'] . " 23:59:59");
            unset($arrParams["query"]["updated_at_to"]);
        }

        if (!auth()->user()->full_access) {
            $arrParams["query"]["sub_users.user_id"] = auth()->user()->id;
        }
        return response()->json($this->subUserService->getList($arrParams));
    }


    public function add()
    {
        $arrParams = request(['first_name', 'last_name', 'phone', 'email', 'password', 'password_confirmation', 'city_id', 'district_id', 'ward_id', 'company_name', 'address']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->subUserService->add($arrParams));
    }

    public function update()
    {
        $arrParams = request(['sub_user_id', 'first_name', 'last_name', 'password', 'password_confirmation', 'city_id', 'district_id', 'ward_id', 'company_name', 'address']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->subUserService->update($arrParams));
    }

    public function delete()
    {
        $arrParams = request(['sub_user_id']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->subUserService->delete($arrParams));
    }


    public function getDetail()
    {
        $arrParams = request(['sub_user_id']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->subUserService->getDetail($arrParams));
    }

}