<?php

namespace App\Http\Controllers\Backend\V1;

class PersonalTokenController extends BaseController
{
    public function __construct()
    {
    }

    public function index()
    {
        return view("backend.".config('app.backend_version').".personal-token.index")->with([]);
    }
}
