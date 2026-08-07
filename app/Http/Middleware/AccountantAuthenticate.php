<?php

namespace App\Http\Middleware;

use App\Models\UserGroup;
use App\Services\AuthService;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AccountantAuthenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $authService = new AuthService();
        $guard       = config("auth.defaults.guard");
        
        if (!auth()->user() || !auth()->user()->is_admin) {
            if (!$request->ajax()) {
                if ($guard == "backend") {
                    return redirect()->route("backend.index.index");
                }

                if ($guard == "frontend") {
                    return redirect()->route("frontend.index.index");
                }
            }

            return response()->json($authService->setStatusCode(100)->setErrors([
                [__("Bạn không có quyền truy cập vào đây.")]
            ])->result());
        }

        $intGroupId   = auth()->user()->group_id;
        $objUserGroup = UserGroup::where('id', $intGroupId)->first();

        if (!$objUserGroup) {
            return response()->json($authService->setStatusCode(100)->setErrors([
                [__("Nhóm của bạn không tồn tại.")]
            ])->result());
        }

        if ($objUserGroup->full_access != 1) {
            if ($objUserGroup->is_accountant != 1) {
                return response()->json($authService->setStatusCode(100)->setErrors([
                    [__("Bạn không có quyền truy cập vào đây.")]
                ])->result());
            }
            return $next($request);
        }
        
        return $next($request);
    }
}
