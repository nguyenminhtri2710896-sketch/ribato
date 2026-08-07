<?php

namespace App\Http\Middleware;

use App\Models\UserGroup;
use App\Services\AuthService;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class ModAuthenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $authService = new AuthService();
        $guard       = config("auth.defaults.guard");
        if (!auth()->user()) {
            if (!$request->ajax()) {
                return redirect()->route("mod.auth.sign-in");
            }
            return response()->json($authService->setStatusCode(401)->setMessage("")->setData([])->setErrors([
                [__("access_token hết hạn hoặc không được phép sử dụng.")]
            ])->result());
        }
        return $next($request);
    }
}
