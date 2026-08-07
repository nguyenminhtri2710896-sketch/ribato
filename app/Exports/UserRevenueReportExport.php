<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UserRevenueReportExport implements FromView
{
    protected $arrData;

    public function __construct($arrData)
    {
        $this->arrData = $arrData;
    }

    public function view(): View
    {
        return view('backend.'.config('app.backend_version').'.user-revenue-report.export.index', [
            'reports' => $this->arrData['reports'] ?? [],
            'summary' => $this->arrData['summary'] ?? null,
            'type' => $this->arrData['type'] ?? [],
        ]);
    }
}

