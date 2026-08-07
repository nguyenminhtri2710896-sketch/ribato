<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UserWithdrawExport implements FromView
{
    protected $arrData;
    public function __construct($arrData)
    {
        $this->arrData = $arrData;
    }

    public function view(): View
    {
        return view('backend.'.config('app.backend_version').'.user-withdraw.export.index', [
            'objUserWithdraws' => $this->arrData["objUserWithdraws"],
            'status' => $this->arrData["status"],
        ]);
    }
}
