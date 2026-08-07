<?php

namespace App\Http\Controllers\Backend\V1;

class GatewayAccountController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
    }


    public function index()
    {
        return view("backend.".config('app.backend_version').".gateway-account.index")->with([]);
    }

    public function detail()
    {
        return view("backend.".config('app.backend_version').".gateway-account.detail")->with([]);
    }
}