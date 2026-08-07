<?php

namespace App\Http\Controllers\Backend\V1;

use App\Services\AuthService;

class AuthController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /*
    Api khởi tạo (mã code) đến email hoặc số điện thoại
    */

    public function signIn()
    {
        return view("backend.".config('app.backend_version').".auth.sign-in")->with([]);
    }

    public function ajaxSignIn()
    {
        $arrParams = request()->only('email', 'password', 'remember', 'auth_2factor_code');
        return $this->authService->signIn($arrParams);
    }
}
