<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends BaseController
{

        protected $authService;
        public function __construct(AuthService $authService)
        {
                $this->authService = $authService;
        }

        public function signIn(Request $request)
        {
                $arrParams  = $request->only('email', 'password', 'remember', 'auth_2factor_code');
                return $this->authService->signIn($arrParams);
        }

        public function signOut(Request $request)
        {
                return response()->json($this->authService->signOut());
        }

        public function checkEmailLoginInfo()
        {
                $arrParams = request(['email']);
                return response()->json($this->authService->checkEmailLoginInfo($arrParams));
        }

        public function refreshToken()
        {
                return response()->json($this->authService->refreshToken());
        }

        public function signUp()
        {
                $arrParams = request(['agree', 'first_name', 'last_name', 'phone', 'email', 'password', 'password_confirmation']);
                return response()->json($this->authService->signUp($arrParams));
        }

        public function checkToken(Request $request)
        {
                return response()->json($this->authService->checkToken());
        }
}
