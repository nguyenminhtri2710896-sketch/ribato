<?php

namespace App\Http\Controllers\Mod\V1;

use App\Services\UserVirtualAccountService;

class VirtualAccountController extends BaseController
{

    private $userVirtualAccountService = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserVirtualAccountService $userVirtualAccountService)
    {
        $this->userVirtualAccountService = $userVirtualAccountService;
    }


    public function index()
    {
        return view("mod.".config('app.mod_version').".virtual-account.index")->with([]);
    }

    public function ajaxGetList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["name"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }
        $arrParams["query"]["user_id"] = auth()->user()->user_id;
        $result = $this->userVirtualAccountService->getList($arrParams);
        if ($result["error_code"] != 0) {
            return $result;
        }
        $resultData = [];
        foreach ($result["data"]["user_virtual_accounts"] ?? [] as $objUservirtualAccount) {
            $arrItem = $objUservirtualAccount->toArray();
            if (isset($arrItem["gateway_id"])) {
                unset($arrItem["gateway_id"]);
            }

            if (isset($arrItem["gateway_name"])) {
                unset($arrItem["gateway_name"]);
            }
            $resultData[] = $arrItem;
        }

        $result["data"]["user_virtual_accounts"] = $resultData;

        return response()->json($result);
    }
    public function select2GetList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]["name"])) {
            $arrParams["query_or_like"] = [
                "user_virtual_accounts.bank_account_name" => $arrParams["query"]["name"],
                "user_virtual_accounts.bank_account_number" => $arrParams["query"]["name"],
            ];
            unset($arrParams["query"]["name"]);
        }

        $arrParams["query"]["user_id"] = auth()->user()->user_id;
        return response()->json($this->userVirtualAccountService->responseSelect2($this->userVirtualAccountService->getList($arrParams)));
    }

}