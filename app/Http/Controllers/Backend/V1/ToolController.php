<?php

namespace App\Http\Controllers\Backend\V1;

use App\Models\UserWithdraw;
use App\Services\AbstractService;
use App\Utilities\General;

class ToolController extends BaseController
{


        /**
         * Create a new controller instance.
         *
         * @return void
         */
        public function __construct()
        {
        }

        public function index($hash = "")
        {
                return view("backend.".config('app.backend_version').".tool.index")->with([]);
        }

        public function createSign($hash = "")
        {
                return view("backend.".config('app.backend_version').".tool.create-sign")->with([]);
        }

        public function ajaxCreateSign($hash = "")
        {
                $arrParram = request()->all();

                $abtractService = new AbstractService();

                if (empty($arrParram["private_key"])) {
                        return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Vui lòng nhập private key')]])->result());
                }

                if (empty($arrParram["plain_text"])) {
                        return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Vui lòng nhập plain text')]])->result());
                }

                if (empty($arrParram["secret_code"])) {
                        return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Vui lòng nhập secret code')]])->result());
                }

                /**
                 * Kiểm tra mã có hợp lệ ko 
                 * Kiểm tra mã nhập vào, nếu nhập vào ok thì tạo ra mã hash điều hướng
                 */
                $sign = General::getSignDebug($arrParram["plain_text"], $arrParram["secret_code"], $arrParram["private_key"]);

                return response()->json($abtractService->setStatusCode(0)->setMessage('')->setData([
                        "sign" => $sign
                ])->setErrors([[__('Thành công.')]])->result());


        }
}