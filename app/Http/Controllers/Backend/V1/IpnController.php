<?php

namespace App\Http\Controllers\Backend\V1;

use App\Services\TransactionCallbackService;
use App\Services\UserWithdrawCallbackService;

class IpnController extends BaseController
{
    protected function authorizeIpn(): void
    {
        if (!(auth()->user()->is_admin || auth()->user()->full_access)) {
            abort(403);
        }
    }

    public function collection()
    {
        // $this->authorizeIpn();
        return view('backend.'.config('app.backend_version').'.ipn.collection', [
            'callbackStatuses' => TransactionCallbackService::$arrCallbackStatus,
        ]);
    }

    public function payout()
    {
        // $this->authorizeIpn();
        return view('backend.'.config('app.backend_version').'.ipn.payout', [
            'callbackStatuses' => UserWithdrawCallbackService::$arrCallbackStatus,
        ]);
    }
}


