<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\GatewayAccountService;

class GatewayAccountController extends BaseController
{

    private $gatewayAccountService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(GatewayAccountService $gatewayAccountService)
    {
        $this->gatewayAccountService = $gatewayAccountService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['gateway_accounts.name'])) {
            $arrParams["query_like"]["gateway_accounts.name"] = $arrParams["query"]['gateway_accounts.name'];
            unset($arrParams["query"]["gateway_accounts.name"]);
        }
        return response()->json($this->gatewayAccountService->getList($arrParams));
    }

    public function getHistoryList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        return response()->json($this->gatewayAccountService->getHistoryList($arrParams));
    }

    public function select2GetList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]["name"])) {
            $arrParams["query_or_like"] = [
                "gateway_accounts.name" => $arrParams["query"]["name"],
            ];
            unset($arrParams["query"]["name"]);
        }
        return response()->json($this->gatewayAccountService->responseSelect2($this->gatewayAccountService->getList($arrParams)));
    }
    public function add()
    {
        $arrParams = request()->all();
        return response()->json($this->gatewayAccountService->add($arrParams));
    }


    public function update()
    {
        $arrParams = request()->all();
        return response()->json($this->gatewayAccountService->update($arrParams));
    }

    public function getDetail()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        return response()->json($this->gatewayAccountService->getDetail($arrParams));
    }

    public function downloadPublicKey()
    {
        $id = request('id');
        $account = \App\Models\GatewayAccount::find($id);
        
        if (!$account || empty($account->public_key)) {
            return response("Public key not found", 404);
        }
        
        $filename = 'public_key_' . $account->name . '.pem';
        
        return response($account->public_key)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}