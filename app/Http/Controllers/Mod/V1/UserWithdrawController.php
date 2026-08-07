<?php

namespace App\Http\Controllers\Mod\V1;

use App\Services\UserWithdrawService;
use App\Utilities\General;

class UserWithdrawController extends BaseController
{


        /**
         * Create a new controller instance.
         *
         * @return void
         */

        private $userWithdrawService = null;
        /**
         * Create a new controller instance.
         *
         * @return void
         */
        public function __construct(UserWithdrawService $userWithdrawService)
        {
                $this->userWithdrawService = $userWithdrawService;
        }

        public function index()
        {
                return view("mod.".config('app.mod_version').".user-withdraw.index")->with([]);
        }

        public function ajaxGetList()
        {
                $arrParams = request(['page', 'limit', 'query', 'sort']);
                if (!empty($arrParams["query"]['created_at_from'])) {
                        $arrParams["query_greater_than"]["user_withdraws.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
                        unset($arrParams["query"]["created_at_from"]);
                }

                if (!empty($arrParams["query"]['created_at_to'])) {
                        $arrParams["query_less_than"]["user_withdraws.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
                        unset($arrParams["query"]["created_at_to"]);
                }

                if (!empty($arrParams["query"]['updated_at_from'])) {
                        $arrParams["query_greater_than"]["user_withdraws.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_from']);
                        unset($arrParams["query"]["updated_at_from"]);
                }

                if (!empty($arrParams["query"]['updated_at_to'])) {
                        $arrParams["query_less_than"]["user_withdraws.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_to'] . " 23:59:59");
                        unset($arrParams["query"]["updated_at_to"]);
                }

                if (!empty($arrParams["query"]['bank_account_name'])) {
                        $arrParams["query_like"]["user_withdraws.bank_account_name"] = $arrParams["query"]['bank_account_name'];
                        unset($arrParams["query"]["bank_account_name"]);
                }

                if (!empty($arrParams["query"]['remark'])) {
                        $arrParams["query_like"]["user_withdraws.remark"] = $arrParams["query"]['remark'];
                        unset($arrParams["query"]["remark"]);
                }

                // if ((auth()->user()->id == 900 || auth()->user()->id == 897 || auth()->user()->id == 904 || auth()->user()->id == 16 || auth()->user()->id == 61) && empty($arrParams["show_full_transaction"]) && empty($arrParams["query"]["show_full_transaction"])) {
                $arrParams["query_not_like"]["remark"] = "SA%";
                // }

                $arrParams["query"]["user_id"] = auth()->user()->user_id;
                $arrParams["query"]["is_show"] = 1;
                return response()->json($this->userWithdrawService->getList($arrParams));
        }


        public function ajaxExportExcel()
        {
                $arrParams = request(['page', 'limit', 'query', 'sort']);
                if (!empty($arrParams["query"]['created_at_from'])) {
                        $arrParams["query_greater_than"]["user_withdraws.created_at"] = General::formatInputDay($arrParams["query"]['created_at_from']);
                        unset($arrParams["query"]["created_at_from"]);
                }

                if (!empty($arrParams["query"]['created_at_to'])) {
                        $arrParams["query_less_than"]["user_withdraws.created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
                        unset($arrParams["query"]["created_at_to"]);
                }

                if (!empty($arrParams["query"]['updated_at_from'])) {
                        $arrParams["query_greater_than"]["user_withdraws.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_from']);
                        unset($arrParams["query"]["updated_at_from"]);
                }

                if (!empty($arrParams["query"]['updated_at_to'])) {
                        $arrParams["query_less_than"]["user_withdraws.updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_to'] . " 23:59:59");
                        unset($arrParams["query"]["updated_at_to"]);
                }

                if (!empty($arrParams["query"]['bank_account_name'])) {
                        $arrParams["query_like"]["user_withdraws.bank_account_name"] = $arrParams["query"]['bank_account_name'];
                        unset($arrParams["query"]["bank_account_name"]);
                }

                if (!empty($arrParams["query"]['remark'])) {
                        $arrParams["query_like"]["user_withdraws.remark"] = $arrParams["query"]['remark'];
                        unset($arrParams["query"]["remark"]);
                }

                $arrParams["query"]["user_id"] = auth()->user()->user_id;
                $arrParams["query"]["is_show"] = 1;
                return response()->json($this->userWithdrawService->exportExcel($arrParams));
        }
}