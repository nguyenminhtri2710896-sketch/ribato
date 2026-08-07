<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\BankAccountService;

class BankAccountController extends BaseController
{

    private $bankAccountService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BankAccountService $bankAccountService)
    {
        $this->bankAccountService = $bankAccountService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["name"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }
        return response()->json($this->bankAccountService->getList($arrParams));
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
        return response()->json($this->bankAccountService->responseSelect2($this->bankAccountService->getList($arrParams)));
    }

    public function getDetail()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        return response()->json($this->bankAccountService->getDetail($arrParams));
    }

    public function add()
    {
        $arrParams = request(['bank_account_name', 'bank_account_number', 'bank_id', 'status_id', 'sorting']);
        return response()->json($this->bankAccountService->add($arrParams));
    }

    public function update()
    {
        $arrParams = request(['id', 'bank_account_name', 'bank_account_number', 'bank_id', 'status_id', 'sorting']);
        return response()->json($this->bankAccountService->update($arrParams));
    }

    public function delete()
    {
        $arrParams = request(['id']);
        return response()->json($this->bankAccountService->delete($arrParams));
    }
}