<?php

namespace App\Http\Controllers\Backend\V1;

class DocController extends BaseController
{
    public function index()
    {
        return view('backend.'.config('app.backend_version').'.doc.index')->with([]);
    }

    public function ipnCollection()
    {
        return view('backend.'.config('app.backend_version').'.doc.ipn-collection')->with([]);
    }

    public function ipnPayout()
    {
        return view('backend.'.config('app.backend_version').'.doc.ipn-payout')->with([]);
    }

    public function payout()
    {
        return view('backend.'.config('app.backend_version').'.doc.payout')->with([]);
    }

    public function collection()
    {
        return view('backend.'.config('app.backend_version').'.doc.collection')->with([]);
    }
}
