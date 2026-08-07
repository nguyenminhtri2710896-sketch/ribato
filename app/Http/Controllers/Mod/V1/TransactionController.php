<?php

namespace App\Http\Controllers\Mod\V1;

use App\Services\TransactionService;

class TransactionController extends BaseController
{


        /**
         * Create a new controller instance.
         *
         * @return void
         */
        protected $transactionService;
        public function __construct(TransactionService $transactionService)
        {
                $this->transactionService = $transactionService;
        }


        public function index()
        {
                return view("mod.".config('app.mod_version').".transaction.index")->with([]);
        }


        public function ajaxGetList()
        {
                $arrParams = request()->all();
                $arrParams["query"]["user_id"] = auth()->user()->user_id;
                return response()->json($this->transactionService->getList($arrParams));
        }
        public function ajaxExportExcel()
        {
                $arrParams = request()->all();
                $arrParams["query"]["user_id"] = auth()->user()->user_id;
                return response()->json($this->transactionService->exportExcel($arrParams));
        }
}