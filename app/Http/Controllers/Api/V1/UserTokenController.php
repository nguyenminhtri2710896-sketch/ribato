<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserTokenService;

class UserTokenController extends BaseController
{

    private $userTokenService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserTokenService $userTokenService)
    {
        $this->userTokenService = $userTokenService;
    }

    public function updatePublicKey()
    {
        $arrParams            = request()->all();
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userTokenService->updatePublicKey($arrParams));
    }

    public function updateWebhookUrl()
    {
        $arrParams            = request()->all();
        $arrParams["user_id"] = auth()->user()->id;
        return response()->json($this->userTokenService->updateWebhookUrl($arrParams));
    }
}