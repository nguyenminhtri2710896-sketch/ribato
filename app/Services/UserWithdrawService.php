<?php

namespace App\Services;

use App\Exports\UserWithdrawAccountantExport;
use App\Exports\UserWithdrawExport;
use App\Exports\UserWithdrawFullaccessExport;
use App\Jobs\PayoutCallbackResultJob;
use App\Jobs\WithdrawGPayLogJob;
use App\Jobs\WithdrawGPayLogV2Job;
use App\Jobs\WithdrawIndividualJob;
use App\Jobs\WithdrawPaymenthotV2Job;
use App\Jobs\WithdrawPaymenthotWebJob;
use App\Jobs\WithdrawYoobilLogJob;
use App\Jobs\WithdrawYoobilLogV2Job;
use App\Models\Bank;
use App\Models\Gateway;
use App\Models\GatewayAccount;
use App\Models\GatewayFee;
use App\Models\PaymenthotAccount;
use App\Models\UserGpayConfig;
use App\Models\UserWithdraw;
use App\Models\User;
use App\Models\UserBalance;
use App\Models\UserFee;
use App\Models\UserReferalFee;
use App\Models\UserToken;
use App\Models\UserTransaction;
use App\Models\UserWithdrawCallback;
use App\Models\UserWithdrawLimit;
use App\Models\UserYoobilConfig;
use App\Utilities\General;
use Carbon\Carbon;
use Curl\Curl;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class UserWithdrawService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserWithdraw())->getFillable();
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Yêu cầu rút'
        ],
        4 => [
            'name' => 'Đang xử lý'
        ],
        2 => [
            'name' => 'Đã xử lý'
        ],
        3 => [
            'name' => 'Huỷ'
        ],
        5 => [
            'name' => 'Chờ xác minh giao dịch'
        ]
    ];

    public static $arrTypeId = [
        1 => [
            'name' => 'Nhận từ tài khoản Cty'
        ],
        2 => [
            'name' => 'Nhận từ tài khoản cá nhân'
        ]
    ];

    public function getList($arrParams = [])
    {

        $intPage = $arrParams["page"] ?? 1;
        $intLimit = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;
        $this->arrFillable = array_merge($this->arrFillable, (new User())->getFillable());
        $objUserWithdrawes = UserWithdraw::select(\DB::raw('user_withdraws.*,users.fullname'))
            ->leftJoin('users', 'users.id', 'user_withdraws.user_id');
        $objUserWithdrawes = $this->getListBuilder($objUserWithdrawes, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objUserWithdrawes;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUserWithdrawes = $objUserWithdrawes->orderBy("user_withdraws.id", "DESC");
        }
        $objUserWithdrawes = $objUserWithdrawes->offset($intOffset)->limit($intLimit)->get();

        $objGateways = Gateway::get();
        $arrGateways = [];
        foreach ($objGateways as $objGateway) {
            $arrGateways[$objGateway->id] = [
                "id" => $objGateway->id,
                "name" => $objGateway->name,
            ];
        }

        return $this->setStatusCode(0)->setData([
            'user_withdraws' => $objUserWithdrawes,
            'records_total' => $intTotal,
            'status' => self::$arrStatusId,
            'gateway' => $arrGateways,
            'type' => self::$arrTypeId,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }


    public function responseSelect2($arrResult = [])
    {
        if ($arrResult["error_code"] != 0) {
            return [];
        }

        $intLimit = $arrResult["data"]["limit"] ?? 1;
        $intPage = $arrResult["data"]["page"] ?? 1;

        $objUserWithdrawes = $arrResult["data"]["user_withdraws"];
        $arrData = [];
        foreach ($objUserWithdrawes as $objUserWithdraw) {
            $arrData[] = [
                "id" => $objUserWithdraw->id,
                "text" => $objUserWithdraw->name,
            ];
        }
        return ["results" => $arrData, "pagination" => ["more" => $arrResult["data"]["records_total"] >= ($intLimit * $intPage) ? true : false], 'limit' => $intLimit];
    }

    public function getDetail($arrParams = [])
    {

        $objUserWithdraw = UserWithdraw::select();
        $objUserWithdraw = $this->getListBuilder($objUserWithdraw, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objUserWithdraw = $objUserWithdraw->first();
        if (empty($objUserWithdraw)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        $objUser = User::where('id', $objUserWithdraw->user_id)->first();
        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['user_withdraw' => $objUserWithdraw, 'user' => $objUser])->result();
    }

    public function add($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'bank_id' => "required",
                'bank_account_number' => "required",
                'bank_account_name' => "required",
                'amount' => "required",
                'user_id' => "required",
            ],
            [

                "bank_id.required" => __("Vui lòng chọn ngân hàng."),
                "bank_account_number.required" => __("Vui lòng nhập số tài khoản."),
                "bank_account_name.required" => __("Vui lòng nhập tên tài khoản."),
                "amount.required" => __("Vui lòng nhập số tiền."),
                "user_id.required" => __("Vui lòng nhập user_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intBankId = $arrParams["bank_id"];
        $strBankAccountNumber = $arrParams["bank_account_number"];
        $strBankAccountName = $arrParams["bank_account_name"];
        $intAmount = $arrParams["amount"];
        $intUserId = $arrParams["user_id"];
        $strRemark = $arrParams["remark"] ?? "";
        $strPlatform = $arrParams["platform"] ?? "web";


        // return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
        //     [__("Hệ thống sẽ tạm dừng để bảo trì và đón Tết từ 11/02 đến hết 23/02.")]
        // ])->result();

        /**
         * Lấy bank 
         */

        $objUser = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        $objBank = Bank::where('id', $intBankId)->first();
        if (!$objBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ngân hàng không hợp lệ.")]
            ])->result();
        }

        if ($intAmount < 10000) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tiền yêu cầu rút tối thiểu " . number_format(10000) . "đ.")]
            ])->result();
        }

        if ($intAmount % 10 != 0) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tiền yêu cầu rút phải là bội số của " . number_format(10) . "đ.")]
            ])->result();
        }



        $objUserBalance = UserBalance::where(["user_id" => $objUser->id])->first();
        if ($objUserBalance) {
            $objUserWithdrawLimit = UserWithdrawLimit::where(["user_id" => $objUser->id])->first();
            if ($objUserWithdrawLimit) {
                if ($objUserWithdrawLimit->lock_withdraw == 1) {
                    return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                        [__("Hệ thống đang bảo trì để nâng cấp, thời gian dự kiến hoàn tất " . date('H d-m-Y', time() + (60 * 60 * 5)) . ", vui lòng quay lại sau hoặc liên hệ quản trị để biết thêm chi tiết.")]
                        // [__("Thông báo ghỉ lễ 30/4 & 1/5. Thời gian nghỉ: 30/04/2026 đến hết ngày 03/05/2026; hoạt động trở lại vào 04/05/2026. Chúc quý khách kỳ nghỉ lễ vui vẻ!.")]
                    ])->result();
                }

                if ($objUserBalance->balance - $intAmount <= $objUserWithdrawLimit->retain_balance) {
                    return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                        [__("Số tiền còn lại đang tạm giữ, vui lòng liên hệ quản trị để biết thêm chi tiết.")]
                    ])->result();
                }
            }
        }

        //UserWithdrawLimit

        /**
         * Lấy phí IN 
         */
        $intUserFee = 0;
        $arrUserFee = [];
        $intUserFeeEstimate = 0;
        $arrUserFeeEstimate = [];
        $objUserFee = UserFee::where('user_id', $intUserId)->whereIn('type_id', [2, 4])->first();
        if ($objUserFee) {
            $arrUserFee = [
                "type_id" => $objUserFee->type_id,
                "fee" => $objUserFee->fee,
                "min_fee" => $objUserFee->min_fee
            ];

            $arrUserFeeEstimate = [
                "type_id" => $objUserFee->type_id,
                "fee" => $objUserFee->fee_estimate,
                "min_fee" => $objUserFee->min_fee
            ];
            /**
             * Liểm tra loại để tính phí
             * 1: phí cố định in
             * 2: phí cố định out
             * 3: phí % in
             * 4: phí % out
             */
            $intUserBusiessFeeType = $objUserFee->type_id;
            if ($intUserBusiessFeeType == 2) {
                //1: phí cố định in
                $intUserFee += $objUserFee->fee;
                $intUserFeeEstimate += $objUserFee->fee_estimate;
            } elseif ($intUserBusiessFeeType == 4) {
                // 3: phí % in
                $intUserFee += round($intAmount * $objUserFee->fee / 100, 3);
                $intUserFeeEstimate += round($intAmount * $objUserFee->fee_estimate / 100, 3);
            }

            if ($intUserFee < $objUserFee->min_fee) {
                $intUserFee = $objUserFee->min_fee;
            }
            if ($intUserFeeEstimate < $objUserFee->min_fee) {
                $intUserFeeEstimate = $objUserFee->min_fee;
            }
        }

        $intAmountAfterFee = $intUserFee + $intAmount;
        $intAmountEstimateAfterFee = $intUserFeeEstimate + $intAmount;


        $intGatewayId = $objUser->gateway_id;
        $intGatewayFee = 0;
        $arrGatewayFee = [];
        $objGatewayFee = GatewayFee::where('gateway_id', $intGatewayId)->whereIn('type_id', [2, 4])->first();
        if ($objGatewayFee) {
            $arrGatewayFee = [
                "type_id" => $objGatewayFee->type_id,
                "fee" => $objGatewayFee->fee,
                "min_fee" => $objGatewayFee->min_fee,
            ];
            if ($objGatewayFee->type_id == 2) {
                //1: phí cố định in
                $intGatewayFee += $objGatewayFee->fee;

            } elseif ($objGatewayFee->type_id == 4) {
                // 3: phí % in
                $intGatewayFee += round($intAmount * $objGatewayFee->fee / 100, 3);
            }

            if ($intGatewayFee < $objGatewayFee->min_fee) {
                $intGatewayFee = $objGatewayFee->min_fee;
            }
        }

        $intUserReferalFee = 0;
        $arrUserReferalFee = [];
        $objUserReferalFee = UserReferalFee::where('user_id', $intUserId)->whereIn('type_id', [2, 4])->first();
        if ($objUserReferalFee) {
            $arrUserReferalFee = [
                "type_id" => $objUserReferalFee->type_id,
                "fee" => $objUserReferalFee->fee,
                "min_fee" => $objUserReferalFee->min_fee,
            ];
            if ($objUserReferalFee->type_id == 2) {
                //1: phí cố định in
                $intUserReferalFee += $objUserReferalFee->fee;

            } elseif ($objUserReferalFee->type_id == 4) {
                // 3: phí % in
                $intUserReferalFee += round($intAmount * $objUserReferalFee->fee / 100, 3);
            }

            if ($intUserReferalFee < $objUserReferalFee->min_fee) {
                $intUserReferalFee = $objUserReferalFee->min_fee;
            }
        }




        $userTransaction = new UserTransactionService();
        \DB::beginTransaction();
        $resultWithdraw = $userTransaction->withDrawal([
            "user_id" => $intUserId,
            "amount" => $intAmountAfterFee,
            "note" => "Yêu cầu rút tiền số tiền " . number_format($intAmountAfterFee) . "đ",
            "trans_code" => date('YmdHis', time()) . rand(11111, 99999)
        ]);

        if ($resultWithdraw["error_code"] != 0) {
            \DB::rollBack();
            return $resultWithdraw;
        }

        $objUserTransaction = $resultWithdraw["data"]["user_transaction"];

        $objUserWithDraw = UserWithdraw::create([
            'gateway_id' => $intGatewayId,
            'user_id' => $intUserId,
            'user_email' => $objUser->email,
            'bank_id' => $intBankId,
            'user_transaction_id' => $objUserTransaction->id,
            'remark' => $strRemark,
            'trans_code' => $objUserTransaction->trans_code,
            'bank_short_name' => $objBank->short_name,
            'bank_account_number' => $strBankAccountNumber,
            'bank_account_name' => $strBankAccountName,
            'amount' => $intAmount,
            'fee' => $intUserFee,
            'fee_estimate' => $intUserFeeEstimate,
            'amount_after_fee' => $intAmountAfterFee,
            'amount_estimate_after_fee' => $intAmountEstimateAfterFee,
            "gateway_fee" => $intGatewayFee,
            "referal_fee" => $intUserReferalFee,
            "profit" => $intUserFee - ($intGatewayFee + $intUserReferalFee),
            "user_fee_json" => json_encode($arrUserFee),
            "user_fee_estimate_json" => json_encode($arrUserFeeEstimate),
            "gateway_fee_json" => json_encode($arrGatewayFee),
            "referal_fee_json" => json_encode($arrUserReferalFee),
            'status_id' => 1,
            'platform' => $strPlatform
        ]);

        //$strPlatform

        if (!$objUserWithDraw) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tạo yêu cầu rút tiền thất bại.")]
            ])->result();
        }
        \DB::commit();

        /**
         * Kiểm tra có được cấu hình yoobill ko 
         */



        $objUserYoobilConfig = UserYoobilConfig::where('user_id', $intUserId)->first();
        if ($objUserYoobilConfig) {
            dispatch(new WithdrawYoobilLogJob([
                'id' => $objUserWithDraw->id,
            ]))->onQueue('request');
        } else {
            $objUserGpayConfig = UserGpayConfig::where('user_id', $intUserId)->first();
            if ($objUserGpayConfig) {
                dispatch(new WithdrawGPayLogJob([
                    'id' => $objUserWithDraw->id,
                ]))->onQueue('request');
            } else {
                $objPaymenthotAccount = PaymenthotAccount::where('user_id', $intUserId)->first();
                if ($objPaymenthotAccount) {
                    dispatch(new WithdrawPaymenthotWebJob([
                        'id' => $objUserWithDraw->id,
                    ]))->onQueue('request');
                } else {
                    /**
                     * Kiểm tra user có sử dụng tham chiếu user không
                     */
                    $objUser = User::where('id', $intUserId)->first();
                    if ($objUser) {
                        if (!empty($objUser->withdraw_refer_user_id)) {
                            $objPaymenthotAccount = PaymenthotAccount::where('user_id', $objUser->withdraw_refer_user_id)->first();
                            if ($objPaymenthotAccount) {
                                dispatch(new WithdrawPaymenthotWebJob([
                                    'id' => $objUserWithDraw->id,
                                ]))->onQueue('request');
                            }
                        }
                    }
                }
            }
        }


        $strMsg = "THÔNG BÁO \nThời gian : \nLoại : YÊU CẦU RÚT TIỀN\nSố tiền : " . number_format($intAmount) . "\nNội dung:$strRemark";
        \App\Jobs\TelegramNotificationJob::dispatch([
            'message' => $strMsg,
            'type' => "notification",
            'chat_id' => '',
            'user_id' => $intUserId
        ])->onQueue('notification');
        return $this->setStatusCode(0)->setMessage(__("Tạo yêu cầu rút tiền thành công."))->setData(["user_withdraw" => $objUserWithDraw])->result();
    }


    public function addManual($arrParams = [])
    {
        if (!(auth()->user()->is_admin || auth()->user()->full_access)) {
            return $this->setStatusCode(403)->setMessage(__('Bạn không có quyền thực hiện thao tác này.'))->setData([])->setErrors([
                [__('Không được phép.')]
            ])->result();
        }

        $validated = Validator::make(
            $arrParams,
            [
                'bank_id' => "required",
                'bank_account_number' => "required",
                'bank_account_name' => "required",
                'amount' => "required",
                'user_id' => "required",
                'gateway_id' => "required",
                'status_id' => "required",
                'trans_code' => "nullable",
                'created_at' => "nullable|date",
            ],
            [

                "bank_id.required" => __("Vui lòng chọn ngân hàng."),
                "bank_account_number.required" => __("Vui lòng nhập số tài khoản."),
                "bank_account_name.required" => __("Vui lòng nhập tên tài khoản."),
                "amount.required" => __("Vui lòng nhập số tiền."),
                "user_id.required" => __("Vui lòng nhập user_id."),
                "gateway_id.required" => __("Vui lòng chọn cổng."),
                "status_id.required" => __("Vui lòng chọn trạng thái."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intBankId = $arrParams["bank_id"];
        $strBankAccountNumber = $arrParams["bank_account_number"];
        $strBankAccountName = General::removeSpecialUnicode($arrParams["bank_account_name"]);
        $intAmount = (int) $arrParams["amount"];
        $intUserId = $arrParams["user_id"];
        $strRemark = General::removeSpecialUnicode($arrParams["remark"] ?? "");
        $strPlatform = $arrParams["platform"] ?? "backend_manual";
        $intTypeId = $arrParams["type_id"] ?? 1;
        $strRefCode = $arrParams["ref_code"] ?? "";
        $intGatewayId = (int) $arrParams["gateway_id"];
        $intStatusId = (int) ($arrParams["status_id"] ?? 1);
        $strTransCode = General::removeSpecialUnicode($arrParams["trans_code"] ?? "");
        $strCreatedAt = trim($arrParams["created_at"] ?? "");
        $objCreatedAt = null;
        if ($strCreatedAt !== "") {
            try {
                $objCreatedAt = Carbon::parse($strCreatedAt);
            } catch (\Exception $ex) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Ngày yêu cầu không hợp lệ.")]
                ])->result();
            }
        }

        $objUser = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        $objBank = Bank::where('id', $intBankId)->first();
        if (!$objBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ngân hàng không hợp lệ.")]
            ])->result();
        }

        $objGateway = Gateway::where('id', $intGatewayId)->first();
        if (!$objGateway) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cổng không hợp lệ.")]
            ])->result();
        }

        if (empty(self::$arrStatusId[$intStatusId])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Trạng thái không hợp lệ.")]
            ])->result();
        }


        if ($intAmount % 10 != 0) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tiền yêu cầu rút phải là bội số của " . number_format(10) . "đ.")]
            ])->result();
        }

        /**
         * Lấy phí IN 
         */
        $intUserFee = 0;
        $arrUserFee = [];
        $objUserFee = UserFee::where('user_id', $intUserId)->whereIn('type_id', [2, 4])->first();
        if ($objUserFee) {
            $arrUserFee = [
                "type_id" => $objUserFee->type_id,
                "fee" => $objUserFee->fee,
                "min_fee" => $objUserFee->min_fee
            ];
            /**
             * Liểm tra loại để tính phí
             * 1: phí cố định in
             * 2: phí cố định out
             * 3: phí % in
             * 4: phí % out
             */
            $intUserBusiessFeeType = $objUserFee->type_id;
            if ($intUserBusiessFeeType == 2) {
                //1: phí cố định in
                $intUserFee += $objUserFee->fee;

            } elseif ($intUserBusiessFeeType == 4) {
                // 3: phí % in
                $intUserFee += round($intAmount * $objUserFee->fee / 100, 3);
            }

            if ($intUserFee < $objUserFee->min_fee && !empty($objUserFee->min_fee)) {
                $intUserFee = $objUserFee->min_fee;
            }
        }

        $intAmountAfterFee = $intUserFee + $intAmount;


        $intGatewayId = $objGateway->id;
        $intGatewayFee = 0;
        $arrGatewayFee = [];
        $objGatewayFee = GatewayFee::where('gateway_id', $intGatewayId)->whereIn('type_id', [2, 4])->first();
        if ($objGatewayFee) {
            $arrGatewayFee = [
                "type_id" => $objGatewayFee->type_id,
                "fee" => $objGatewayFee->fee,
                "min_fee" => $objGatewayFee->min_fee,
            ];
            if ($objGatewayFee->type_id == 2) {
                //1: phí cố định in
                $intGatewayFee += $objGatewayFee->fee;

            } elseif ($objGatewayFee->type_id == 4) {
                // 3: phí % in
                $intGatewayFee += round($intAmount * $objGatewayFee->fee / 100, 3);
            }

            if ($intGatewayFee < $objGatewayFee->min_fee) {
                $intGatewayFee = $objGatewayFee->min_fee;
            }
        }

        $intUserReferalFee = 0;
        $arrUserReferalFee = [];
        $objUserReferalFee = UserReferalFee::where('user_id', $intUserId)->whereIn('type_id', [2, 4])->first();
        if ($objUserReferalFee) {
            $arrUserReferalFee = [
                "type_id" => $objUserReferalFee->type_id,
                "fee" => $objUserReferalFee->fee,
                "min_fee" => $objUserReferalFee->min_fee,
            ];
            if ($objUserReferalFee->type_id == 2) {
                //1: phí cố định in
                $intUserReferalFee += $objUserReferalFee->fee;

            } elseif ($objUserReferalFee->type_id == 4) {
                // 3: phí % in
                $intUserReferalFee += round($intAmount * $objUserReferalFee->fee / 100, 3);
            }

            if ($intUserReferalFee < $objUserReferalFee->min_fee) {
                $intUserReferalFee = $objUserReferalFee->min_fee;
            }
        }
        $objWithdrawCheckExist = UserWithdraw::where('trans_code', $strTransCode)->first();
        if ($objWithdrawCheckExist) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mã giao dịch đã tồn tại.")]
            ])->result();
        }


        $objUserTransaction = UserTransaction::where('trans_code', $strTransCode)->first();
        if (!$objUserTransaction) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mã giao dịch không hợp lệ.")]
            ])->result();
        }

        $objUserWithDraw = UserWithdraw::create([
            'gateway_id' => $intGatewayId,
            'user_id' => $intUserId,
            'user_email' => $objUser->email,
            'bank_id' => $intBankId,
            'user_transaction_id' => $objUserTransaction->id,
            'remark' => $strRemark,
            'trans_code' => $strTransCode,
            'bank_short_name' => $objBank->short_name,
            'bank_account_number' => $strBankAccountNumber,
            'bank_account_name' => $strBankAccountName,
            'amount' => $intAmount,
            'fee' => $intUserFee,
            'amount_after_fee' => $intAmountAfterFee,
            "gateway_fee" => $intGatewayFee,
            "referal_fee" => $intUserReferalFee,
            "profit" => $intUserFee - ($intGatewayFee + $intUserReferalFee),
            "user_fee_json" => json_encode($arrUserFee),
            "gateway_fee_json" => json_encode($arrGatewayFee),
            "referal_fee_json" => json_encode($arrUserReferalFee),
            'status_id' => $intStatusId,
            'type_id' => $intTypeId,
            'platform' => $strPlatform,
            'ref_code' => $strRefCode
        ] + ($objCreatedAt ? [
                'created_at' => $objCreatedAt->format('Y-m-d H:i:s'),
                'updated_at' => $objCreatedAt->format('Y-m-d H:i:s'),
            ] : []));

        if (!$objUserWithDraw) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tạo yêu cầu rút tiền thủ công thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Tạo yêu cầu rút tiền thủ công thành công."))->setData(["user_withdraw" => $objUserWithDraw])->result();
    }


    public function addV2($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'bank_id' => "required",
                'bank_account_number' => "required",
                'bank_account_name' => "required",
                'amount' => "required",
                'user_id' => "required",
                'remark' => "required",
            ],
            [

                "bank_id.required" => __("Vui lòng chọn ngân hàng."),
                "bank_account_number.required" => __("Vui lòng nhập số tài khoản."),
                "bank_account_name.required" => __("Vui lòng nhập tên tài khoản."),
                "amount.required" => __("Vui lòng nhập số tiền."),
                "user_id.required" => __("Vui lòng nhập user_id."),
                "remark.required" => __("Vui lòng nhập nội dung chuyển khoản."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intTypeId = $arrParams["type_id"] ?? 1;
        $intBankId = $arrParams["bank_id"];
        $strBankAccountNumber = $arrParams["bank_account_number"];
        $strBankAccountName = General::removeSpecialUnicode($arrParams["bank_account_name"]);
        $intAmount = $arrParams["amount"];
        $intUserId = $arrParams["user_id"];
        $strRemark = General::removeSpecialUnicode($arrParams["remark"] ?? "");
        $strPlatform = $arrParams["platform"] ?? "web";
        $strRefCode = $arrParams["ref_code"] ?? "";


        /**
         * Lấy bank 
         */

        $objUser = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        $objBank = Bank::where('id', $intBankId)->first();
        if (!$objBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ngân hàng không hợp lệ.")]
            ])->result();
        }

        if ($intAmount < 10000) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tiền yêu cầu rút tối thiểu " . number_format(10000) . "đ.")]
            ])->result();
        }

        if ($intAmount >= 300000000) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tiền yêu cầu rút không quá " . number_format(300000000) . "đ.")]
            ])->result();
        }

        if ($intAmount % 10 != 0) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tiền yêu cầu rút phải là bội số của " . number_format(10) . "đ.")]
            ])->result();
        }

        $objUserBalance = UserBalance::where(["user_id" => $objUser->id])->first();
        if (!$objUserBalance) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản của bạn chưa được kích hoạt sử dụng số dư.")]
            ])->result();
        }

        $objUserWithdrawLimit = UserWithdrawLimit::where(["user_id" => $objUser->id])->first();
        if (!$objUserWithdrawLimit) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không lấy được cấu hình rút tiền, vui lòng liên hệ quản trị.")]
            ])->result();
        }

        if ($objUserWithdrawLimit->lock_withdraw == 1) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                // [__("Hệ thống sẽ tạm dừng để bảo trì và đón Tết từ 11/02 đến hết 23/02.")]

                [__("Hệ thống đang bảo trì để nâng cấp, thời gian dự kiến hoàn tất " . date('H d-m-Y', time() + (60 * 60 * 5)) . ", vui lòng quay lại sau hoặc liên hệ quản trị để biết thêm chi tiết.")]
                // [__("Thông báo ghỉ lễ 30/4 & 1/5. Thời gian nghỉ: 30/04/2026 đến hết ngày 03/05/2026; hoạt động trở lại vào 04/05/2026. Chúc quý khách kỳ nghỉ lễ vui vẻ!.")]

            ])->result();
        }

        if ($objUserBalance->balance - $intAmount <= $objUserWithdrawLimit->retain_balance) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tiền còn lại đang tạm khoá, vui lòng liên hệ quản trị để biết thêm chi tiết.")]
            ])->result();
        }

        if (!in_array(date('N') + 1, explode(",", $objUserWithdrawLimit->allow_withdraw_day))) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Hệ thống cho phép rút tiền thứ $objUserWithdrawLimit->allow_withdraw_day.")]
            ])->result();
        }
        /**
         * Nếu có set thứ 7
         */
        // if (!in_array(7, explode(",", $objUserWithdrawLimit->allow_withdraw_day))) {
        //     if (date('N') == 6 && date("H") > 12) {
        //         return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
        //             [__("Hệ thống cho phép rút tiền thứ $objUserWithdrawLimit->allow_withdraw_day, T7 từ 0h đến 12h.")]
        //         ])->result();
        //     }
        // }


        /**
         * Tổng số tiền rút trên 1 số tài khoản trong ngày
         */
        $intTotalWithdrawAccountBankToday = UserWithdraw::where('user_id', $intUserId)
            ->where('bank_account_number', $strBankAccountNumber)
            ->where('bank_id', $intBankId)
            ->whereIn('status_id', [1, 2, 4, 5])
            ->where('created_at', ">", date('Y-m-d H:i:s', time() - (60 * 60 * 24)))
            ->sum('amount');

        if ($intTotalWithdrawAccountBankToday + $intAmount > $objUserWithdrawLimit->withdraw_limit_bank_account_in_day) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tiền chuyển đến tài khoản bị giới bạn 24h, vui lòng thử lại sau, tổng hạn mức hiện tại đã sử dụng là " . number_format($intTotalWithdrawAccountBankToday, 0)) . ". "]
            ])->result();
        }

        /**
         * Tổng số tiền rút trong 1 ngày
         */

        $intTotalWithdrawToday = UserWithdraw::where('user_id', $intUserId)
            ->whereIn('status_id', [1, 2, 4, 5])
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('amount');

        if ($intTotalWithdrawToday + $intAmount > $objUserWithdrawLimit->withdraw_limit_in_day) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tiền rút trong ngày đã vượt quá giới hạn cho phép, vui lòng thử lại sau.")]
            ])->result();
        }


        if ($intTypeId == 1) {
            $objGatewayAccount = GatewayAccount::whereIn('id', explode(",", $objUser->gateway_withdraw_ids))->where('balance', '>', $intAmount)->orderBy('balance', 'DESC')->first();
            if (!$objGatewayAccount) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Lỗi cấu hình, vui lòng liên hệ quản trị.")]
                ])->result();
            }
        } else if ($intTypeId == 2) {
            $objGatewayAccount = GatewayAccount::whereIn('gateway_id', [4, 5, 6])->where('balance', '>', $intAmount)->orderBy('balance', 'DESC')->first();
            if (!$objGatewayAccount) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Lỗi cấu hình, vui lòng liên hệ quản trị.")]
                ])->result();
            }
        } else {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Lỗi cấu hình, vui lòng liên hệ quản trị.")]
            ])->result();
        }

        /**
         * Lấy phí IN 
         */
        $intUserFee = 0;
        $intFeeIndividual = 0;
        $arrUserFee = [];
        $intUserFeeEstimate = 0;
        $arrUserFeeEstimate = [];
        $objUserFee = UserFee::where('user_id', $intUserId)->whereIn('type_id', [2, 4])->first();
        if ($objUserFee) {
            $arrUserFee = [
                "type_id" => $objUserFee->type_id,
                "fee" => $objUserFee->fee,
                "min_fee" => $objUserFee->min_fee
            ];
            $arrUserFeeEstimate = [
                "type_id" => $objUserFee->type_id,
                "fee" => $objUserFee->fee_estimate,
                "min_fee" => $objUserFee->min_fee
            ];
            /**
             * Liểm tra loại để tính phí
             * 1: phí cố định in
             * 2: phí cố định out
             * 3: phí % in
             * 4: phí % out
             */
            $intUserBusiessFeeType = $objUserFee->type_id;
            if ($intUserBusiessFeeType == 2) {
                //1: phí cố định in
                $intUserFee += $objUserFee->fee;
                if ($intTypeId == 2) {
                    $intFeeIndividual = $objUserFee->individual_fee;
                }

                $intUserFee += $intFeeIndividual;

            } elseif ($intUserBusiessFeeType == 4) {
                // 3: phí % in
                $intUserFee += round($intAmount * $objUserFee->fee / 100, 3);
                $intUserFeeEstimate += round($intAmount * $objUserFee->fee_estimate / 100, 3);
                if ($intTypeId == 2) {
                    $intFeeIndividual = round($intAmount * $objUserFee->individual_fee / 100, 3);
                }

                $intUserFee += $intFeeIndividual;
                $intUserFeeEstimate += $intFeeIndividual;
            }

            if ($intUserFee < $objUserFee->min_fee) {
                $intUserFee = $objUserFee->min_fee;
            }
            if ($intUserFeeEstimate < $objUserFee->min_fee) {
                $intUserFeeEstimate = $objUserFee->min_fee;
            }
        }
        $intAmountAfterFee = $intUserFee + $intAmount;
        $intAmountEstimateAfterFee = $intUserFeeEstimate + $intAmount;


        $intGatewayId = $objGatewayAccount->gateway_id;
        $intGatewayFee = 0;
        $arrGatewayFee = [];
        $objGatewayFee = GatewayFee::where('gateway_id', $intGatewayId)->whereIn('type_id', [2, 4])->first();
        if ($objGatewayFee) {
            $arrGatewayFee = [
                "type_id" => $objGatewayFee->type_id,
                "fee" => $objGatewayFee->fee,
                "min_fee" => $objGatewayFee->min_fee,
            ];
            if ($objGatewayFee->type_id == 2) {
                //1: phí cố định in
                $intGatewayFee += $objGatewayFee->fee;

            } elseif ($objGatewayFee->type_id == 4) {
                // 3: phí % in
                $intGatewayFee += round($intAmount * $objGatewayFee->fee / 100, 3);
            }

            if ($intGatewayFee < $objGatewayFee->min_fee) {
                $intGatewayFee = $objGatewayFee->min_fee;
            }
        }

        $intAmountGatewayAfterFee = $intGatewayFee + $intAmount;


        $intUserReferalFee = 0;
        $arrUserReferalFee = [];
        $objUserReferalFee = UserReferalFee::where('user_id', $intUserId)->whereIn('type_id', [2, 4])->first();
        if ($objUserReferalFee) {
            $arrUserReferalFee = [
                "type_id" => $objUserReferalFee->type_id,
                "fee" => $objUserReferalFee->fee,
                "min_fee" => $objUserReferalFee->min_fee,
            ];
            if ($objUserReferalFee->type_id == 2) {
                //1: phí cố định in
                $intUserReferalFee += $objUserReferalFee->fee;

            } elseif ($objUserReferalFee->type_id == 4) {
                // 3: phí % in
                $intUserReferalFee += round($intAmount * $objUserReferalFee->fee / 100, 3);
            }

            if ($intUserReferalFee < $objUserReferalFee->min_fee) {
                $intUserReferalFee = $objUserReferalFee->min_fee;
            }
        }




        $userTransaction = new UserTransactionService();
        \DB::beginTransaction();
        $resultWithdraw = $userTransaction->withDrawal([
            "user_id" => $intUserId,
            "amount" => $intAmountAfterFee,
            "note" => "Yêu cầu rút tiền số tiền " . number_format($intAmountAfterFee) . "đ",
            "trans_code" => date('YmdHis', time()) . rand(11111, 99999)
        ]);

        if ($resultWithdraw["error_code"] != 0) {
            \DB::rollBack();
            return $resultWithdraw;
        }

        $objUserTransaction = $resultWithdraw["data"]["user_transaction"];

        $objUserWithDraw = UserWithdraw::create([
            'gateway_id' => $intGatewayId,
            'gateway_account_id' => $objGatewayAccount->id,
            'user_id' => $intUserId,
            'user_email' => $objUser->email,
            'bank_id' => $intBankId,
            'user_transaction_id' => $objUserTransaction->id,
            'remark' => $strRemark,
            'trans_code' => $objUserTransaction->trans_code,
            'bank_short_name' => $objBank->short_name,
            'bank_account_number' => $strBankAccountNumber,
            'bank_account_name' => $strBankAccountName,
            'amount' => $intAmount,
            'fee' => $intUserFee,
            'amount_after_fee' => $intAmountAfterFee,
            "gateway_fee" => $intGatewayFee,
            "amount_gateway_after_fee" => $intAmountGatewayAfterFee,
            "referal_fee" => $intUserReferalFee,
            "profit" => $intUserFee - ($intGatewayFee + $intUserReferalFee + $intFeeIndividual), // Phí $intFeeIndividual xử lý cho đối tác mình không có nhận nên không tính vào profit
            "user_fee_json" => json_encode($arrUserFee),
            "gateway_fee_json" => json_encode($arrGatewayFee),
            "referal_fee_json" => json_encode($arrUserReferalFee),
            "user_fee_estimate_json" => json_encode($arrUserFeeEstimate),
            "amount_estimate_after_fee" => $intAmountEstimateAfterFee,
            "profit_estimate" => $intUserFeeEstimate - ($intGatewayFee + $intUserReferalFee + $intFeeIndividual), // Phí $intFeeIndividual xử lý cho đối tác mình không có nhận nên không tính vào profit
            'status_id' => 1,
            'type_id' => $intTypeId,
            'platform' => $strPlatform,
            'ref_code' => $strRefCode
        ]);

        if (!$objUserWithDraw) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tạo yêu cầu rút tiền thất bại.")]
            ])->result();
        }
        \DB::commit();

        /**
         * 1 PaymentHot
         * 2 Gpay
         * 3 Yoobill
         */

        if ($intTypeId == 1) {
            if ($intGatewayId == 3) {
                dispatch(new WithdrawYoobilLogV2Job([
                    'id' => $objUserWithDraw->id,
                ]))->onQueue('request');
            } elseif ($intGatewayId == 2) {
                dispatch(new WithdrawGPayLogV2Job([
                    'id' => $objUserWithDraw->id,
                ]))->onQueue('request');
            } elseif ($intGatewayId == 1) {
                dispatch(new WithdrawPaymenthotV2Job([
                    'id' => $objUserWithDraw->id,
                ]))->onQueue('request');
            } elseif (in_array($intGatewayId, [4, 5, 6])) {


            } else {

            }
        } else {
            /**
             * Sử dụng trừ vào tài khoản cá nhân
             */
            dispatch(new WithdrawIndividualJob([
                'id' => $objUserWithDraw->id,
            ]))->onQueue('request');
        }

        dispatch(new \App\Jobs\ToolPushBackupWithdrawlJob([
            'user_withdraw_id' => $objUserWithDraw->id,
        ]))->onQueue('notification');




        $strMsg = "THÔNG BÁO \nThời gian : \nLoại : YÊU CẦU RÚT TIỀN\nSố tiền : " . number_format($intAmount) . "\nNội dung:$strRemark";
        \App\Jobs\TelegramNotificationJob::dispatch([
            'message' => $strMsg,
            'type' => "notification",
            'chat_id' => '',
            'user_id' => $intUserId
        ])->onQueue('notification');
        return $this->setStatusCode(0)->setMessage(__("Tạo yêu cầu rút tiền thành công."))->setData(["user_withdraw" => $objUserWithDraw])->result();
    }

    public function withdrawIndividual($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "user_withdraw_id" => "required",
            ],
            [

                "user_withdraw_id.required" => __("Vui lòng nhập user_withdraw_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserWithdrawId = $arrParams["user_withdraw_id"];
        \DB::beginTransaction();
        $objUserWithdraw = UserWithdraw::where('id', $intUserWithdrawId)->lockForUpdate()->first();
        if (empty($objUserWithdraw)) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy yêu cầu rút tiền.")]
            ])->result();
        }

        if ($objUserWithdraw->status_id == 5) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Giao dịch chờ đối tác xác nhận.")]
            ])->result();
        }

        $intUserWithdrawlId = $objUserWithdraw->user_id;
        $objUser = User::where('id', $intUserWithdrawlId)->first();
        if (!$objUser) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ngừời dùng không tồn tại.")]
            ])->result();
        }

        /**
         * lấy user có số tiền rút
         */
        $intAmount = $objUserWithdraw->amount;
        $objUserBalance = UserBalance::join('users', 'users.id', 'user_balances.user_id')->where('balance', '>=', $intAmount)->where('group_id', 3)->first();
        if (!$objUserBalance) {
            \DB::rollBack();
            $resultChangeStatus = $this->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Đối tác payout không khả dụng"]);
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không đủ số dư để rút tiền.")]
            ])->result();
        }


        $intUserPartnerId = $objUserBalance->user_id;
        $objUserFee = UserFee::where('user_id', $intUserPartnerId)->whereIn('type_id', [2, 4])->first();
        if (!$objUserFee) {
            \DB::rollBack();
            $resultChangeStatus = $this->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Đối tác payout chưa được cấu hình phí"]);
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Đối tác chưa được cấu hình phí.")]
            ])->result();
        }

        $intAmountAfterFee = $intAmount;
        if ($objUserFee->type_id == 2) {
            //1: phí cố định ount
            $intAmountAfterFee += $objUserFee->fee;
        } elseif ($objUserFee->type_id == 4) {
            // 3: phí % out
            $intAmountAfterFee += round($intAmount * $objUserFee->fee / 100, 3);
        }

        $userTransaction = new UserTransactionService();
        $resultWithdraw = $userTransaction->withDrawal([
            "user_id" => $intUserPartnerId,
            "amount" => $intAmountAfterFee,
            "note" => "Trừ ký quỹ đại lý thanh toán cho khách $objUserWithdraw->user_email  " . number_format($intAmountAfterFee) . "đ",
            "trans_code" => date('YmdHis', time()) . rand(11111, 99999)
        ]);

        if ($resultWithdraw["error_code"] != 0) {
            \DB::rollBack();
            $resultChangeStatus = $this->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Đối tác payout có lỗi 01"]);
            return $resultWithdraw;
        }


        $gatewayAccountTransactionService = new GatewayAccountTransactionService();
        $resultGatewayAccountWithdraw = $gatewayAccountTransactionService->withDrawal([
            "gateway_account_id" => $objUserWithdraw->gateway_account_id,
            "user_id" => $intUserPartnerId, // lưu ý user này phải là user đại lý
            "amount" => $objUserWithdraw->amount_gateway_after_fee,
            "note" => "Thanh toán số tiền cho khách $objUserWithdraw->user_email " . number_format($objUserWithdraw->amount_gateway_after_fee) . "đ",
            "trans_code" => date('YmdHis', time()) . rand(11111, 99999)
        ]);

        if ($resultGatewayAccountWithdraw["error_code"] != 0) {
            \DB::rollBack();
            $resultChangeStatus = $this->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Gate lỗi 01 " . $resultGatewayAccountWithdraw["message"]]);
            return $resultWithdraw;
        }

        $objUserTransaction = $resultWithdraw["data"]["user_transaction"];
        $objGatewayAccountTransaction = $resultGatewayAccountWithdraw["data"]["gateway_account_transaction"];

        $objUserWithdraw->partner_process_id = $intUserPartnerId;
        $objUserWithdraw->partner_hash_code = md5($intUserPartnerId . time() . rand(111111111, 999999999));
        $objUserWithdraw->partner_auth_code = rand(111111, 999999);
        $objUserWithdraw->partner_transaction_id = $objUserTransaction->id;
        $objUserWithdraw->partner_transaction_amount = $objUserTransaction->amount;

        /**
         * Ghi nhận gateway account
         */
        $objUserWithdraw->gateway_account_transaction_id = $objGatewayAccountTransaction->id;
        $objUserWithdraw->gateway_account_transaction_amount = $objGatewayAccountTransaction->amount;


        if (!$objUserWithdraw->save()) {
            \DB::rollBack();
            $resultChangeStatus = $this->changeStatus(["id" => $objUserWithdraw->id, "status_id" => 3, "note" => "Gate lỗi ghi nhận thông tin"]);
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận thông tin thất bại.")]
            ])->result();
        }
        \DB::commit();
        /**
         * Gửi thông báo cho đối tác
         */
        $strMessage = "THÔNG BÁO \nThời gian : " . date('Y-m-d H:i:s') . "\n" .
            "Loại : YÊU CẦU RÚT TIỀN\n" .
            "Số tiền : " . number_format($intAmount) . "\n" .
            "Nội dung: " . $objUserWithdraw->remark . "\n" .
            "Mã giao dịch: " . $objUserWithdraw->trans_code . "\n\n" .
            "Mã truy cập: " . $objUserWithdraw->partner_auth_code . "\n" .
            "Truy cập xác nhận: " . route('backend.confirm-withdraw.verify', ["hash" => $objUserWithdraw->partner_hash_code]);


        \App\Jobs\TelegramNotificationJob::dispatch([
            'message' => $strMessage,
            'type' => "notification-partner",
            'bot_token' => '8409524834:AAHHiSgv1oEGobkVx3egx_khqBDgEp3nihg',
            'user_id' => $intUserPartnerId
        ])->onQueue('notification');


        $strMessage = "THÔNG BÁO \nThời gian : " . date('Y-m-d H:i:s') . "\n" .
            "Loại : YÊU CẦU RÚT TIỀN\n" .
            "Số tiền : " . number_format($intAmount) . "\n" .
            "Nội dung: " . $objUserWithdraw->remark . "\n" .
            "Mã giao dịch: " . $objUserWithdraw->trans_code;


        \App\Jobs\TelegramNotificationJob::dispatch([
            'message' => $strMessage,
            'type' => "notification-partner",
            'bot_token' => '8409524834:AAHHiSgv1oEGobkVx3egx_khqBDgEp3nihg',
            'user_id' => $intUserWithdrawlId
        ])->onQueue('notification');

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData([])->result();

    }

    public function addMultibleCheck($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'content' => "required",
            ],
            [

                "content.required" => __("Vui lòng chọn ngân hàng."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strContent = $arrParams["content"];
        $arrLines = explode("\n", $strContent);
        $arrErrors = [];
        $arrResult = [];
        foreach ($arrLines as $num => $strLine) {
            $arrInfo = explode(",", $strLine);
            $strBankcode = trim($arrInfo[0] ?? "");
            $objBank = Bank::where('short_code', strtoupper($strBankcode))->first();
            if (!$objBank) {
                $arrErrors[] = "Dòng $num Mã ngân hàng không hợp lệ";
                continue;
            }


            $strBankAccountCode = trim($arrInfo[1] ?? "");
            $strBankAccountName = strtoupper(trim($arrInfo[2] ?? ""));
            $intAmount = $arrInfo[3] ?? 0;
            $strRemark = trim($arrInfo[4] ?? "");
            $arrResult[] = [
                'bank_id' => $objBank->id,
                'bank_short_name' => $objBank->short_name,
                'bank_account_number' => $strBankAccountCode,
                'bank_account_name' => $strBankAccountName,
                'amount' => $intAmount,
                'remark' => $strRemark
            ];
        }

        if (!empty($arrErrors)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [implode("<br/>", $arrErrors)]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData($arrResult)->result();
    }

    public function addMultible($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'content' => "required",
            ],
            [

                "content.required" => __("Vui lòng chọn ngân hàng."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserId = $arrParams["user_id"];
        $strContent = $arrParams["content"];
        $strPlatform = $arrParams["platform"] ?? "web";
        $strWithdrawVersion = $arrParams["withdraw_version"] ?? "v1";


        $resultCheck = $this->addMultibleCheck(["content" => $strContent]);
        if ($resultCheck["error_code"] != 0) {
            return $resultCheck;
        }

        $arrLists = $resultCheck["data"];
        $arrErrors = [];
        $arrResult = [];
        foreach ($arrLists as $arrList) {
            $arrList["user_id"] = $intUserId;
            $arrList["platform"] = $strPlatform;
            if ($strWithdrawVersion == "v2") {
                $resultAdd = $this->addV2($arrList);
            } else {
                $resultAdd = $this->add($arrList);
            }
            if ($resultAdd["error_code"] != 0) {
                $arrErrors[] = $resultAdd["message"];
                continue;
            }
            $arrResult[] = $resultAdd["data"];
        }
        return $this->setStatusCode(0)->setMessage(__('Thành công ' . count($arrResult) . ' giao dịch<br/>Lỗi: ' . count($arrErrors) . ' giao dịch ' . implode(', ', $arrErrors) . '.'))->setData(["error" => $arrErrors, "data" => $arrResult])->result();
    }

    public function changeStatus($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'id',
                'status_id',
            ],
            [

                "id.required" => __("Vui lòng nhập id."),
                "status_id.required" => __("Vui lòng nhập trạng thais."),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intId = $arrParams["id"];
        $intStatusId = $arrParams["status_id"];
        $strNote = $arrParams["note"] ?? "";
        if (empty(self::$arrStatusId[$intStatusId])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Trạng thái không hợp lệ.")]
            ])->result();
        }

        \DB::beginTransaction();
        $objUserWithdraw = UserWithdraw::where('id', $intId)->lockForUpdate()->first();
        if (empty($objUserWithdraw)) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        if (in_array($objUserWithdraw->status_id, [2, 3])) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không thể đổi trạng thái yêu cầu này.")]
            ])->result();
        }
        $intUserId = $objUserWithdraw->user_id;
        $intAmountAfterFee = $objUserWithdraw->amount_after_fee;

        $objUserWithdraw->status_id = $intStatusId;
        $objUserWithdraw->note = $strNote;
        if (!$objUserWithdraw->save()) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }

        if ($objUserWithdraw->status_id == 3) {
            /**
             * Thực hiện hoàn số dư hiện tại cho khách
             */
            $userTransaction = new UserTransactionService();
            $resultRefund = $userTransaction->refund([
                "user_id" => $intUserId,
                "amount" => $intAmountAfterFee,
                "note" => "Hoàn tiền huỷ yêu cầu rút số dư [$strNote] số tiền " . number_format($intAmountAfterFee) . "đ",
            ]);
            if ($resultRefund["error_code"] != 0) {
                \DB::rollBack();
                return $resultRefund;
            }
            /**
             * Nếu có tồn tại
             */
            if ($objUserWithdraw->partner_process_id) {
                if ($objUserWithdraw->gateway_account_transaction_id) {
                    /**
                     * Hoàn tiền gate
                     */
                    $gatewayAccountTransactionService = new GatewayAccountTransactionService();
                    $resultRefund = $gatewayAccountTransactionService->refund([
                        "gateway_account_id" => $objUserWithdraw->gateway_account_id,
                        "amount" => $objUserWithdraw->gateway_account_transaction_amount,
                        "note" => "Hoàn tiền huỷ yêu cầu rút số dư [$strNote] số tiền " . number_format($objUserWithdraw->gateway_account_transaction_amount) . "đ",
                    ]);
                }


                /**
                 * Hoàn tiền vào ví đại lý
                 */
                if ($objUserWithdraw->partner_transaction_id) {
                    $userTransaction = new UserTransactionService();
                    $resultRefund = $userTransaction->refund([
                        "user_id" => $objUserWithdraw->partner_process_id,
                        "amount" => $objUserWithdraw->partner_transaction_amount,
                        "note" => "Hoàn tiền huỷ yêu cầu rút số dư [$strNote] số tiền " . number_format($objUserWithdraw->partner_transaction_amount) . "đ",
                    ]);
                }
            }
        }

        dispatch(new PayoutCallbackResultJob([
            'id' => $objUserWithdraw->id,
        ]))->onQueue('request');

        \DB::commit();
        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData(["user_withdraw" => $objUserWithdraw])->result();

    }


    public function callbackResult($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                'user_withdraw_id' => "required",
            ],
            [
                "user_withdraw_id.required" => __("Vui lòng nhập user_withdraw_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intWithdrawId = $arrParams["user_withdraw_id"];
        $objUserWithdraw = UserWithdraw::where('id', $intWithdrawId)->first();
        if (!$objUserWithdraw) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mã giao dịch không tồn tại trên hệ thống.")]
            ])->result();
        }

        if ($objUserWithdraw->callback_total_retry > 3) {
            UserWithdrawCallback::create([
                "user_withdraw_id" => $objUserWithdraw->id,
                "user_id" => $objUserWithdraw->user_id,
                "message" => "Giới hạn callback"
            ]);
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Callback quá nhiều lần vẫn thất bại.")]
            ])->result();
        }
        $objUserWithdraw->callback_total_retry += 1;
        /**
         * Lấy token
         */
        $intUserId = $objUserWithdraw->user_id;
        $strWebhookUrl = "";
        $strSystemPrivateKey = "";
        $strToken = "";
        $objToken = UserToken::where('user_id', $objUserWithdraw->user_id)->first();
        if ($objToken) {
            $strWebhookUrl = $objToken->webhook_payout_url;
            $strSystemPrivateKey = $objToken->system_private_key;
            $strToken = $objToken->token;
        }

        if ($strWebhookUrl == "") {
            $objUserWithdraw->callback_status_id = 3;
            $objUserWithdraw->save();
            UserWithdrawCallback::create([
                "user_withdraw_id" => $objUserWithdraw->id,
                "user_id" => $objUserWithdraw->user_id,
                "message" => "Không tìm thấy IPN callback"
            ]);
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không có url để callback.")]
            ])->result();
        }

        $_curl = new Curl();
        $_curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
        $_curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
        $_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $_curl->setOpt(CURLOPT_ENCODING, "");
        $_curl->setHeader('Content-Type', 'application/json');
        $_curl->setHeader('User-agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36');
        $_curl->setTimeout(90);
        $_curl->setConnectTimeout(90);


        $strCodePayout = "PENDING";
        if ($objUserWithdraw->status_id == 2) {
            $strCodePayout = "SUCCESS";
        }

        if ($objUserWithdraw->status_id == 3) {
            $strCodePayout = "FAILED";
        }

        $result = [
            'user_id' => $intUserId,
            'amount' => $objUserWithdraw->amount,
            'fee' => $objUserWithdraw->fee,
            'trans_code' => $objUserWithdraw->trans_code,
            'ref_code' => $objUserWithdraw->ref_code,
            'content' => $objUserWithdraw->remark,
            'status_id' => $objUserWithdraw->status_id,
            'code' => $strCodePayout,
            'message' => $objUserWithdraw->note,
        ];



        $strSign = "";
        if (!empty($strSystemPrivateKey)) {
            try {
                $strSign = General::getSign($result, $strToken, $strSystemPrivateKey);
            } catch (\Exception $ex) {

            }
        }

        $arrPost = [
            'error_code' => 0,
            'msg' => 'Thành công',
            'result' => $result
        ];


        $result["sign"] = $strSign;
        if (env('BACKEND_VERSION') == 'v2' && env('POSTBACK_VERSION') == 'v2') {
            if (isset($result["sign"])) {
                unset($result["sign"]);
            }

            $result = [
                'amount' => $objUserWithdraw->amount,
                'trans_code' => $objUserWithdraw->trans_code,
                'reference' => $objUserWithdraw->ref_code,
                'content' => $objUserWithdraw->remark,
                'status_id' => $objUserWithdraw->status_id,
                'code' => $strCodePayout,
                'message' => $objUserWithdraw->note,
            ];
            $result["checksum"] = md5(General::httpBuildQuery($result) . $strToken);
            $arrPost = [
                'success' => true,
                'message' => 'Thành công',
                'data' => $result
            ];
            $_curl->setProxy('104.250.122.113', 22128, 'admin', 'admin@admin123123');
        }

        $_curl->post($strWebhookUrl, json_encode($arrPost));
        if ($_curl->error) {
            $objUserWithdraw->callback_status_id = 3;
            $objUserWithdraw->save();
            UserWithdrawCallback::create([
                "user_withdraw_id" => $objUserWithdraw->id,
                "user_id" => $objUserWithdraw->user_id,
                "message" => ($_curl->errorCode . ': ' . $_curl->errorMessage),
                "param_request" => json_encode(["url" => $strWebhookUrl, "request" => $arrPost]),
                "param_response" => strip_tags($_curl->rawResponse)
            ]);

            return $this->setStatusCode(809)->setMessage("")->setData([])->setErrors([
                [__($_curl->errorCode . ': ' . $_curl->errorMessage)]
            ])->result();
        }

        $objUserWithdraw->callback_status_id = 2;
        $objUserWithdraw->save();
        UserWithdrawCallback::create([
            "user_withdraw_id" => $objUserWithdraw->id,
            "user_id" => $objUserWithdraw->user_id,
            "message" => "Thành công",
            "param_request" => json_encode($arrPost),
            "param_response" => strip_tags($_curl->rawResponse)
        ]);

        return $this->setStatusCode(0)->setMessage(__("Thành công"))->setData([
            'user_withdraw' => $objUserWithdraw,
            'response' => $_curl->rawResponse,
        ])->result();
    }


    public function exportExcel($arrParams = [])
    {
        $objUserWithdraws = UserWithdraw::select();
        $objUserWithdraws = $this->getListBuilder($objUserWithdraws, $arrParams, $this->arrFillable)->orderBy('id', 'ASC')->get();
        if (!$objUserWithdraws) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([[__("Không tìm thấy sữ liệu để xuất.")]])->result();
        }
        $fileName = 'exports/export_widthdraw_' . time() . '.xlsx';
        $resultExport = \Excel::store(new UserWithdrawExport(['objUserWithdraws' => $objUserWithdraws, 'status' => self::$arrStatusId,]), $fileName, 'export-excel', null);
        if (!$resultExport) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Xuất file thất bại.')]])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xuất giao dịch thành công."))->setData(["url" => url("static/" . $fileName)])->result();
    }

    public function exportExcelFullaccess($arrParams = [])
    {
        $objUserWithdraws = UserWithdraw::select();
        $objUserWithdraws = $this->getListBuilder($objUserWithdraws, $arrParams, $this->arrFillable)->orderBy('id', 'ASC')->get();
        if (!$objUserWithdraws) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([[__("Không tìm thấy sữ liệu để xuất.")]])->result();
        }
        $fileName = 'exports/export_widthdraw_' . time() . '.xlsx';
        $objGateWay = Gateway::get();
        $arrGateWay = [];
        foreach ($objGateWay as $item) {
            $arrGateWay[$item->id] = [
                "name" => $item->name
            ];
        }

        $resultExport = \Excel::store(new UserWithdrawFullaccessExport(['objUserWithdraws' => $objUserWithdraws, 'status' => self::$arrStatusId, 'gateway' => $arrGateWay]), $fileName, 'export-excel', null);
        if (!$resultExport) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Xuất file thất bại.')]])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xuất giao dịch thành công."))->setData(["url" => url("static/" . $fileName)])->result();
    }

    public function exportExcelAccountant($arrParams = [])
    {
        $objUserWithdraws = UserWithdraw::select();
        $objUserWithdraws = $this->getListBuilder($objUserWithdraws, $arrParams, $this->arrFillable)->orderBy('id', 'ASC')->get();
        if (!$objUserWithdraws) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([[__("Không tìm thấy sữ liệu để xuất.")]])->result();
        }
        $fileName = 'exports/export_widthdraw_' . time() . '.xlsx';
        $objGateWay = Gateway::get();
        $arrGateWay = [];
        foreach ($objGateWay as $item) {
            $arrGateWay[$item->id] = [
                "name" => $item->name
            ];
        }

        $resultExport = \Excel::store(new UserWithdrawAccountantExport(['objUserWithdraws' => $objUserWithdraws, 'status' => self::$arrStatusId, 'gateway' => $arrGateWay]), $fileName, 'export-excel', null);
        if (!$resultExport) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Xuất file thất bại.')]])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xuất giao dịch thành công."))->setData(["url" => url("static/" . $fileName)])->result();
    }

    public function createBill($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                'id' => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập id."),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $objUserWithdraw = UserWithdraw::where('id', $arrParams['id'])->first();
        if (!$objUserWithdraw) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([[__("Không tìm thấy sữ liệu để xuất.")]])->result();
        }

        $objBank = Bank::where('id', $objUserWithdraw->bank_id)->first();
        if (!$objBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([[__("Không tìm thấy sữ liệu ngân hàng.")]])->result();
        }

        // $file = base64_encode(file_get_contents(base_path('static/images/mb-template-withdraw-origin.jpg')));

        // echo '<img  height="900"  src="data:image/jpeg;base64,' . $file . '" alt="base64 test">';

        // open an image file
        $img = Image::make(base_path('static/images/mb-template-withdraw.jpg'));
        $centerImage = ($img->getWidth() / 2);
        $img->text(number_format($objUserWithdraw->amount) . " VND", $centerImage, 500, function ($font) {
            $font->file(base_path('static/Fonts/SFProDisplay/SF-Pro-Display-Medium.otf'));
            $font->size(50);
            $font->color('#75e5f9');
            $font->align('center');
        });


        $img->text(date('H:s - d/m/Y', strtotime($objUserWithdraw->created_at)), $centerImage, 550, function ($font) {
            $font->file(base_path('static/Fonts/SFProDisplay/SF-Pro-Display-Regular.otf'));
            $font->size(21);
            $font->color('#a1b8f8');
            $font->align('center');
        });


        $img->text($objUserWithdraw->bank_account_name, $centerImage, 660, function ($font) {
            $font->file(base_path('static/Fonts/SFProDisplay/SF-Pro-Display-Bold.otf'));
            $font->size(28);
            $font->color('#ebebfb');
            $font->align('center');
        });

        $img->text($objBank->short_name . " (" . $objBank->short_code . ")", $centerImage, 710, function ($font) {
            $font->file(base_path('static/Fonts/SFProDisplay/SF-Pro-Display-Regular.otf'));
            $font->size(25);
            $font->color('#ebebfb');
            $font->align('center');
        });

        if (file_exists(base_path('static/images/bank-logos/' . strtolower($objBank->short_code) . '.png'))) {
            $watermark = Image::make(base_path('static/images/bank-logos/' . strtolower($objBank->short_code) . '.png'));
            $watermark->resize(50, 50);
            $img->insert($watermark, 'center-left', $centerImage - 170, 710);
        }

        $img->text($objUserWithdraw->bank_account_number, $centerImage, 750, function ($font) {
            $font->file(base_path('static/Fonts/SFProDisplay/SF-Pro-Display-Regular.otf'));
            $font->size(22);
            $font->color('#ebebfb');
            $font->align('center');
        });

        $img->text($objUserWithdraw->remark, $centerImage, 800, function ($font) {
            $font->file(base_path('static/Fonts/SFProDisplay/SF-Pro-Display-Regular.otf'));
            $font->size(25);
            $font->color('#ebebfb');
            $font->align('center');
        });

        $img->text("CONG TY CO PHAN", $img->getWidth() - 50, 900, function ($font) {
            $font->file(base_path('static/Fonts/SFProDisplay/SF-Pro-Display-Bold.otf'));
            $font->size(21);
            $font->color('#ebebfb');
            $font->align('right');
        });
        $img->text("PAYPAY", $img->getWidth() - 50, 930, function ($font) {
            $font->file(base_path('static/Fonts/SFProDisplay/SF-Pro-Display-Bold.otf'));
            $font->size(21);
            $font->color('#ebebfb');
            $font->align('right');
        });

        $img->text("FT" . substr($objUserWithdraw->trans_code, 0, 14), $img->getWidth() - 50, 965, function ($font) {
            $font->file(base_path('static/Fonts/SFProDisplay/SF-Pro-Display-Bold.otf'));
            $font->size(21);
            $font->color('#ebebfb');
            $font->align('right');
        });

        $img->text(strtoupper(substr(md5($objUserWithdraw->trans_code), 0, 16)), $img->getWidth() - 50, 1020, function ($font) {
            $font->file(base_path('static/Fonts/SFProDisplay/SF-Pro-Display-Bold.otf'));
            $font->size(21);
            $font->color('#ebebfb');
            $font->align('right');
        });
        $strUrl = 'static/uploads/images/tmp-' . md5($objUserWithdraw->id) . '.jpg';
        $pathSave = base_path($strUrl);
        $img->save($pathSave, 90, 'jpg');
        // $imageBase64 = base64_encode(file_get_contents($pathSave));

        // echo '<img height="900" src="data:image/jpeg;base64,' . $imageBase64 . '" alt="base64 test">';
        // now you are able to resize the instance
        // $img->resize(320, 240);

        // // and insert a watermark for example
        // $img->insert('public/watermark.png');
        return $this->setStatusCode(0)->setMessage(__("Xuất giao dịch thành công."))->setData(["url" => url($strUrl)])->result();
    }

}