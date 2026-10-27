<?php

namespace App\Http\Controllers\Backend\V1;

class UserWithdrawWhitelistController extends BaseController
{
    public function index()
    {
        return view("backend." . config('app.backend_version') . ".user-withdraw-whitelist.index")->with([]);
    }
}
