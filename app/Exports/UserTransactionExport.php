<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UserTransactionExport implements FromView
{
    protected $arrData;
    public function __construct($arrData)
    {
        $this->arrData = $arrData;
    }

    public function view(): View
    {
        return view('backend.'.config('app.backend_version').'.user-transaction.export.index', [
            'objUserTransactions' => $this->arrData["objUserTransactions"],
            'types' => $this->arrData["types"],
        ]);
    }
}
