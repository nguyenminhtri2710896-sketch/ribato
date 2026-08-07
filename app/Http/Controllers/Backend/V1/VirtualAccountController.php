<?php

namespace App\Http\Controllers\Backend\V1;

class VirtualAccountController extends BaseController
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
        return view("backend.".config('app.backend_version').".virtual-account.index")->with([]);
    }
}