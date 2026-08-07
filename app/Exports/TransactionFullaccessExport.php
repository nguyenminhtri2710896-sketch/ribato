<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransactionFullaccessExport implements FromView
{
    protected $arrData;
    public function __construct($arrData)
    {
        $this->arrData = $arrData;
    }

    public function view(): View
    {
        return view('backend.'.config('app.backend_version').'.transaction.export.fullaccess-index', [
            'objTransactions' => $this->arrData["objTransactions"],
            'status' => $this->arrData["status"],
            'gateway' => $this->arrData["gateway"],
        ]);
    }
}
