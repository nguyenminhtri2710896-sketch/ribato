<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UserExport implements FromView
{
    protected $arrData;
    public function __construct($arrData)
    {
        $this->arrData = $arrData;
    }

    public function view(): View
    {
        return view('backend.'.config('app.backend_version').'.user.export.index', [
            'objUsers' => $this->arrData["objUsers"],
        ]);
    }
}
