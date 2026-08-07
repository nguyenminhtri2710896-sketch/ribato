<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserBankAccountService;

class UserBankAccountController extends BaseController
{

    private $userBankAccountService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserBankAccountService $userBankAccountService)
    {
        $this->userBankAccountService = $userBankAccountService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["name"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }

        return response()->json($this->userBankAccountService->getList($arrParams));
    }

    public function select2GetList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]["name"])) {
            $arrParams["query_or_like"] = [
                "bank_accounts.bank_account_name" => $arrParams["query"]["name"],
                "bank_accounts.bank_account_number" => $arrParams["query"]["name"]
            ];
            unset($arrParams["query"]["name"]);
        }
        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userBankAccountService->responseSelect2($this->userBankAccountService->getList($arrParams)));
    }


  

    public function getDetail()
    {
        $arrParams = request()->all();
        return response()->json($this->userBankAccountService->getDetail($arrParams));
    }

    public function add()
    {
        $arrParams = request()->all();
        return response()->json($this->userBankAccountService->add($arrParams));
    }

    public function update()
    {
        $arrParams = request()->all();
        return response()->json($this->userBankAccountService->update($arrParams));
    }

    public function delete()
    {
        $arrParams = request()->all();
        return response()->json($this->userBankAccountService->delete($arrParams));
    }

}