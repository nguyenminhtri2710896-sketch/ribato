<?php

namespace App\Http\Middleware;

use App\Models\UserToken;
use App\Services\AuthService;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */

    protected function redirectTo($request)
    {

    }
    public function handle($request, Closure $next, ...$guards)
    {
        $arrParams   = $request->all();
        $guard       = config("auth.defaults.guard");
        $authService = new AuthService();
        if ($request->header('api-token')) {
            $objUserToken = UserToken::where('token', $request->header('api-token'))->first();
            if (!$objUserToken) {
                return response()->json($authService->setStatusCode(100)->setErrors([
                    [__("Token không tồn tại.")]
                ])->result());
            }

            if (strtotime($objUserToken->expired_at) < time()) {
                return response()->json($authService->setStatusCode(401)->setErrors([
                    [__("Token hết hạn.")]
                ])->result());
            }

            if ($objUserToken->permission === 'read') {
                $routeName = $request->route() ? $request->route()->getName() : null;
                if ($routeName) {
                    $hasAccess = \App\Models\UserTokenRouterAccess::where('route_name', $routeName)
                        ->where('permission', 'read')
                        ->exists();
                    if (!$hasAccess) {
                        return response()->json($authService->setStatusCode(403)->setErrors([
                            [__("Token chỉ có quyền đọc (Read-only) và không thể truy cập tính năng này.")]
                        ])->result());
                    }
                }
            }

            $objUser = User::where('id', $objUserToken->user_id)->first();
            if (!$objUser) {
                return response()->json($authService->setStatusCode(100)->setErrors([
                    [__("Tài khoản không tồn tại.")]
                ])->result());
            }

            $objUser->user_token_id = $objUserToken->id;
            auth()->setUser($objUser);
            $arrParams["objUserToken"] = $objUserToken;
            $request->merge($arrParams);
            return $next($request);
        }


        if (!auth()->user()) {
            if (!$request->ajax()) {
                if ($guard == "backend") {
                    return redirect()->route("backend.auth.sign-in");
                }

                if ($guard == "frontend") {
                    return redirect()->route("frontend.auth.sign-in");
                }
            }
            return response()->json($authService->setStatusCode(401)->setMessage("")->setData([])->setErrors([
                [__("access_token hết hạn hoặc không được phép sử dụng.")]
            ])->result());
        }

        /**
         * Merge input fullaccess
         */
        return $next($request);
    }
}
