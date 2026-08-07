<?php

namespace App\Http\Controllers\Backend\V1;

use App\Services\AbstractService;
use Curl\Curl;

class UserRevenueReportController extends BaseController
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
        return view("backend.".config('app.backend_version').".user-revenue-report.index")->with([]);
    }
}