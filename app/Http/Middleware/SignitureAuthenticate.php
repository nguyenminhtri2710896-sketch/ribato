<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use App\Utilities\General;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class SignitureAuthenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $arrParams = $request->all();
        if (isset($arrParams["q"])) {
            unset($arrParams["q"]);
        }

        $authService = new AuthService();

        if (empty($arrParams["sign"])) {
            return response()->json($authService->setStatusCode(404)->setErrors([
                [__("Vui lòng nhập sign.")]
            ])->result());
        }

        $objUserToken = null;
        if (isset($arrParams["objUserToken"])) {
            $objUserToken = $arrParams["objUserToken"];
            unset($arrParams["objUserToken"]);
        }

        if (empty($objUserToken->token)) {
            return response()->json($authService->setStatusCode(404)->setErrors([
                [__("Tài khoản chưa được cấu hình token.")]
            ])->result());
        }

        if (empty($objUserToken->public_key)) {
            return response()->json($authService->setStatusCode(404)->setErrors([
                [__("Tài khoản chưa được cấu hình public key.")]
            ])->result());
        }


        $strPublicKey  = $objUserToken->public_key;
        // $strPrivateKey = $objUserToken->system_private_key;
        $strToken      = $objUserToken->token;


        //   print_r(General::getSign($arrParams, $strToken, $strPrivateKey));exit;


        // $arrData = ["params" => $arrParams, "token" => $strToken, "strPublicKey" => $strPublicKey];

        $resultVerifySign = General::verifySign($arrParams, $strToken, $strPublicKey);
        if (!$resultVerifySign) {
            return response()->json($authService->setStatusCode(404)->setErrors([
                [__("Chữ ký chứng thực không đúng, vui lòng kiểm tra lại.")]
            ])->setData([])->result());
        }
        return $next($request);
    }
}
