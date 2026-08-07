<?php

namespace App\Http\Controllers\Backend\V1;


class UserTransactionController extends BaseController
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
                return view("backend.".config('app.backend_version').".user-transaction.index")->with([]);
        }


}