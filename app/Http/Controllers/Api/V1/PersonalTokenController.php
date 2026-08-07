<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserTokenService;

class PersonalTokenController extends BaseController
{
    private $userTokenService = null;

    public function __construct(UserTokenService $userTokenService)
    {
        $this->userTokenService = $userTokenService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userTokenService->getPersonalTokensList($arrParams));
    }

    public function add()
    {
        $arrParams = request(['name', 'permission']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userTokenService->addPersonalToken($arrParams));
    }

    public function delete()
    {
        $arrParams = request(['id']);
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userTokenService->deletePersonalToken($arrParams));
    }
}
