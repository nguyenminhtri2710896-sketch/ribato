<?php

namespace App\Http\Controllers\Api\V1;


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
                return response()->json([]);
        }


}