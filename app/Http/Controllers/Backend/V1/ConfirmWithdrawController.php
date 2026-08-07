<?php

namespace App\Http\Controllers\Backend\V1;

use App\Models\UserWithdraw;
use App\Services\AbstractService;
use App\Services\UserWithdrawService;

class ConfirmWithdrawController extends BaseController
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
                if (empty($hash)) {
                        return redirect()->route('backend.index.index')->with([]);
                }
                $arrParram = request()->all();
                $strCode = $arrParram["code"] ?? "";

                if (empty($strCode)) {
                        return redirect()->route('backend.confirm-withdraw.verify', ["hash" => $hash]);
                }

                if (request()->isMethod('post')) {
                        $abtractService = new AbstractService();

                        if (empty($arrParram["code"])) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Vui lòng nhập mật khẩu giao dịch.')]])->result());
                        }

                        /**
                         * Kiểm tra mã có hợp lệ ko 
                         * Kiểm tra mã nhập vào, nếu nhập vào ok thì tạo ra mã hash điều hướng
                         */
                        $objUserWithdraw = UserWithdraw::where('partner_hash_code', $hash)->first();
                        if (!$objUserWithdraw) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Giao dịch không tồn tại.')]])->result());
                        }
                        if ($objUserWithdraw->partner_auth_code != $strCode) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Mã truy cập không đúng.')]])->result());
                        }

                        return response()->json($abtractService->setStatusCode(0)->setMessage('')->setData([
                                "url_redirect" => route('backend.confirm-withdraw.index', ["hash" => $hash, "code" => $strCode])
                        ])->setErrors([[__('Mã truy cập hợp lệ.')]])->result());

                }
                $objUserWithdraw = UserWithdraw::where('partner_hash_code', $hash)->first();
                return view("backend.".config('app.backend_version').".confirm-withdraw.index")->with(["hash" => $hash, 'code' => $strCode, 'objUserWithdraw' => $objUserWithdraw]);
        }

        public function verify($hash = "")
        {
                $arrParram = request()->all();
                $strCode = $arrParram["code"] ?? "";

                if (empty($hash)) {
                        return redirect()->route('backend.index.index')->with([]);
                }

                if (request()->isMethod('post')) {
                        $abtractService = new AbstractService();

                        if (empty($arrParram["code"])) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Vui lòng nhập mật khẩu giao dịch.')]])->result());
                        }
                        /**
                         * Kiểm tra mã có hợp lệ ko 
                         * Kiểm tra mã nhập vào, nếu nhập vào ok thì tạo ra mã hash điều hướng
                         */
                        $objUserWithdraw = UserWithdraw::where('partner_hash_code', $hash)->first();
                        if (!$objUserWithdraw) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Giao dịch không tồn tại.')]])->result());
                        }
                        if ($objUserWithdraw->partner_auth_code != $strCode) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Mã truy cập không đúng.')]])->result());
                        }

                        return response()->json($abtractService->setStatusCode(0)->setMessage('')->setData([
                                "url_redirect" => route('backend.confirm-withdraw.index', ["hash" => $hash, "code" => $strCode])
                        ])->setErrors([[__('Thành công.')]])->result());

                }
                $objUserWithdraw = UserWithdraw::where('partner_hash_code', $hash)->first();
                return view("backend.".config('app.backend_version').".confirm-withdraw.verify")->with(["hash" => $hash, 'objUserWithdraw' => $objUserWithdraw]);
        }

        public function ajaxConfirm()
        {
                $arrParram = request()->all();
                $strCode = $arrParram["code"] ?? "";
                $hash = $arrParram["hash"] ?? "";
                if (request()->isMethod('post')) {
                        $abtractService = new AbstractService();

                        if (empty($arrParram["code"])) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Vui lòng nhập mật khẩu giao dịch.')]])->result());
                        }

                        if (empty($arrParram["hash"])) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Vui lòng nhập mã hash.')]])->result());
                        }

                        if (empty($arrParram["partner_transaction_image"])) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Vui lòng up hình ảnh chứng từ.')]])->result());
                        }

                        /**
                         * Kiểm tra mã có hợp lệ ko 
                         * Kiểm tra mã nhập vào, nếu nhập vào ok thì tạo ra mã hash điều hướng
                         */
                        $objUserWithdraw = UserWithdraw::where('partner_hash_code', $hash)->first();
                        if (!$objUserWithdraw) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Giao dịch không tồn tại.')]])->result());
                        }
                        if ($objUserWithdraw->partner_auth_code != $strCode) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Mã truy cập không đúng.')]])->result());
                        }

                        if (in_array($objUserWithdraw->partner_transaction_status_id, [2, 3])) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Bạn không thể cập nhật giao dịch ở trạng thái này.')]])->result());
                        }

                        if (in_array($objUserWithdraw->status_id, [2, 3])) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Giao dịch đã được xử lý, bạn không cần xử lý lại.')]])->result());
                        }
                        $objUserWithdraw->partner_transaction_status_id = 2;
                        $objUserWithdraw->partner_transaction_image = $arrParram["partner_transaction_image"];
                        if (!$objUserWithdraw->save()) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Xác nhận thất bại.')]])->result());
                        }

                        $userWithdrawService = new UserWithdrawService();
                        $resultChangeStatus = $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 2, "note" => "Thành công"]);
                        if ($resultChangeStatus["error_code"] != 0) {
                                return $resultChangeStatus;
                        }

                        $strMessage = "THÔNG BÁO \nThời gian : " . date('Y-m-d H:i:s') . "\n" .
                                "Loại : XỬ LÝ GIAO DỊCH RÚT TIỀN\n" .
                                "Mã giao dịch: " . $objUserWithdraw->trans_code . "\n\n" .
                                "Trang thái: Thành công \n" .
                                "Chứng từ: " . asset($objUserWithdraw->partner_transaction_image) . "  \n";
                        \App\Jobs\TelegramNotificationJob::dispatch([
                                'message' => $strMessage,
                                'type' => "notification-partner",
                                'bot_token' => '8409524834:AAHHiSgv1oEGobkVx3egx_khqBDgEp3nihg',
                                'user_id' => $objUserWithdraw->partner_process_id
                        ])->onQueue('notification');

                        \App\Jobs\TelegramNotificationJob::dispatch([
                                'message' => $strMessage,
                                'type' => "notification-partner",
                                'bot_token' => '8409524834:AAHHiSgv1oEGobkVx3egx_khqBDgEp3nihg',
                                'user_id' => $objUserWithdraw->user_id
                        ])->onQueue('notification');

                        return response()->json($abtractService->setStatusCode(0)->setMessage('')->setData([
                                "url_redirect" => route('backend.confirm-withdraw.index', ["hash" => $hash, "code" => $strCode])
                        ])->setErrors([[__('Cập nhật thành công.')]])->result());

                }
                $objUserWithdraw = UserWithdraw::where('partner_hash_code', $hash)->first();
                return view("backend.".config('app.backend_version').".confirm-withdraw.index")->with(["hash" => $hash, 'code' => $strCode, 'objUserWithdraw' => $objUserWithdraw]);
        }

        public function ajaxCancel()
        {
                $arrParram = request()->all();
                $strCode = $arrParram["code"] ?? "";
                $hash = $arrParram["hash"] ?? "";
                $strReason = $arrParram["partner_transaction_cancel_reason"] ?? "";

                if (request()->isMethod('post')) {
                        $abtractService = new AbstractService();

                        if (empty($arrParram["code"])) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Vui lòng nhập mật khẩu giao dịch.')]])->result());
                        }


                        if (empty($arrParram["hash"])) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Vui lòng nhập mã hash.')]])->result());
                        }
                        /**
                         * Kiểm tra mã có hợp lệ ko 
                         * Kiểm tra mã nhập vào, nếu nhập vào ok thì tạo ra mã hash điều hướng
                         */
                        $objUserWithdraw = UserWithdraw::where('partner_hash_code', $hash)->first();
                        if (!$objUserWithdraw) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Giao dịch không tồn tại.')]])->result());
                        }
                        if ($objUserWithdraw->partner_auth_code != $strCode) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Mã truy cập không đúng.')]])->result());
                        }
                        if (in_array($objUserWithdraw->partner_transaction_status_id, [2, 3])) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Bạn không thể cập nhật giao dịch ở trạng thái này.')]])->result());
                        }
                        if (in_array($objUserWithdraw->status_id, [2, 3])) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Giao dịch đã được xử lý, bạn không cần xử lý lại.')]])->result());
                        }

                        $objUserWithdraw->partner_transaction_status_id = 3;
                        $objUserWithdraw->partner_transaction_cancel_reason = $strReason;
                        if (!$objUserWithdraw->save()) {
                                return response()->json($abtractService->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Xác nhận thất bại.')]])->result());
                        }

                        $userWithdrawService = new UserWithdrawService();
                        $resultChangeStatus = $userWithdrawService->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => $strReason]);
                        if ($resultChangeStatus["error_code"] != 0) {
                                return $resultChangeStatus;
                        }

                        $strMessage = "THÔNG BÁO \nThời gian : " . date('Y-m-d H:i:s') . "\n" .
                                "Loại : XỬ LÝ GIAO DỊCH RÚT TIỀN\n" .
                                "Mã giao dịch: " . $objUserWithdraw->trans_code . "\n\n" .
                                "Trang thái: Bị từ chối ( $strReason) \n";
                        \App\Jobs\TelegramNotificationJob::dispatch([
                                'message' => $strMessage,
                                'type' => "notification-partner",
                                'bot_token' => '8409524834:AAHHiSgv1oEGobkVx3egx_khqBDgEp3nihg',
                                'user_id' => $objUserWithdraw->partner_process_id
                        ])->onQueue('notification');

                        \App\Jobs\TelegramNotificationJob::dispatch([
                                'message' => $strMessage,
                                'type' => "notification-partner",
                                'bot_token' => '8409524834:AAHHiSgv1oEGobkVx3egx_khqBDgEp3nihg',
                                'user_id' => $objUserWithdraw->user_id
                        ])->onQueue('notification');

                        return response()->json($abtractService->setStatusCode(0)->setMessage('')->setData([
                                "url_redirect" => route('backend.confirm-withdraw.index', ["hash" => $hash, "code" => $strCode])
                        ])->setErrors([[__('Cập nhật thành công.')]])->result());

                }
                $objUserWithdraw = UserWithdraw::where('partner_hash_code', $hash)->first();
                return view("backend.".config('app.backend_version').".confirm-withdraw.index")->with(["hash" => $hash, 'code' => $strCode, 'objUserWithdraw' => $objUserWithdraw]);
        }
}