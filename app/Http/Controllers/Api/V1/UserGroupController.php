<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserGroupService;

class UserGroupController extends BaseController
{

    private $userGroupService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserGroupService $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["name"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }
        return response()->json($this->userGroupService->getList($arrParams));
    }

    public function select2GetList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]["name"])) {
            $arrParams["query_or_like"] = [
                "banks.name" => $arrParams["query"]["name"],
            ];
            unset($arrParams["query"]["name"]);
        }
        return response()->json($this->userGroupService->responseSelect2($this->userGroupService->getList($arrParams)));
    }
    public function add()
    {
        $arrParams = request()->all();
        return response()->json($this->userGroupService->add($arrParams));
    }


    public function update()
    {
        $arrParams = request()->all();
        return response()->json($this->userGroupService->update($arrParams));
    }

    public function getDetail()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        return response()->json($this->userGroupService->getDetail($arrParams));
    }
}