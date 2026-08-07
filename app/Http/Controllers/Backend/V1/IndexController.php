<?php

namespace App\Http\Controllers\Backend\V1;

use App\Models\Bank;
use App\Services\BankService;

class IndexController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
    }

    public function index()
    {
        return view("backend.".config('app.backend_version').".index.index")->with([]);
    }

    public function lang($locale)
    {
        if (in_array($locale, ['vi', 'en', 'zh'])) {
            session()->put('locale', $locale);
        }
        return redirect()->back();
    }
}