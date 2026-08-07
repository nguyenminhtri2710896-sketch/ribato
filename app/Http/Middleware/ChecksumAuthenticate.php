<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use App\Utilities\General;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class ChecksumAuthenticate extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $arrParams = $request->all();
        if (isset($arrParams["q"])) {
            unset($arrParams["q"]);
        }

        $authService = new AuthService();

        if (empty($arrParams["checksum"])) {
            return response()->json($authService->setStatusCode(404)->setErrors([
                [__("Vui lòng nhập checksum.")]
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

        $strToken = $objUserToken->token;
        $receivedChecksum = $arrParams['checksum'];
        unset($arrParams['checksum']);

        // Checksum formula: md5(urldecode(http_build_query(sorted_params)) . token)
        ksort($arrParams);

        $expectedChecksum = md5(urldecode(General::httpBuildQuery($arrParams)) . $strToken);

        if (strtolower($receivedChecksum) !== strtolower($expectedChecksum)) {
            return response()->json($authService->setStatusCode(404)->setErrors([
                [__("Checksum xác thực không đúng, vui lòng kiểm tra lại.")]
            ])->setData([])->result());
        }

        return $next($request);
    }
}
