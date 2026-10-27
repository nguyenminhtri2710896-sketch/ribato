<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserWithdrawWhitelistService;

class UserWithdrawWhitelistController extends BaseController
{
    private $userWithdrawWhitelistService = null;

    public function __construct(UserWithdrawWhitelistService $userWithdrawWhitelistService)
    {
        $this->userWithdrawWhitelistService = $userWithdrawWhitelistService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['bank_account_number'])) {
            $arrParams["query_like"]["bank_account_number"] = $arrParams["query"]['bank_account_number'];
            unset($arrParams["query"]["bank_account_number"]);
        }
        return response()->json($this->userWithdrawWhitelistService->getList($arrParams));
    }

    public function getDetail()
    {
        return response()->json($this->userWithdrawWhitelistService->getDetail(request()->all()));
    }

    public function add()
    {
        return response()->json($this->userWithdrawWhitelistService->add(request()->all()));
    }

    public function update()
    {
        return response()->json($this->userWithdrawWhitelistService->update(request()->all()));
    }

    public function delete()
    {
        return response()->json($this->userWithdrawWhitelistService->delete(request()->all()));
    }
}
