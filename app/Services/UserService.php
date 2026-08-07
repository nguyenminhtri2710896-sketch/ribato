<?php

namespace App\Services;

use App\Exports\UserExport;

use App\Models\GatewayAccount;
use App\Models\Trash;
use App\Models\UserBalance;
use App\Models\UserGroup;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserDebit;
use App\Models\UserFee;
use App\Models\UserReferalFee;
use App\Models\UserSigninLog;
use App\Models\UserToken;
use App\Models\UserWithdrawLimit;
use Dolondro\GoogleAuthenticator\GoogleAuthenticator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Dolondro\GoogleAuthenticator\SecretFactory;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use tttran\viet_qr_generator\Generator;

class UserService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new User())->getFillable();
    }

    public function updatePassword($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "password" => "required|max:50|confirmed|min:6",
                "password_confirmation" => "required",
                "user_id" => "required",
            ],
            [

                "password.required" => __("Vui lòng nhập mật khẩu mới"),
                "password.max" => __("Mật khẩu phải nhỏ hơn :max ký tự."),
                "password.min" => __("Mật khẩu phải lớn hơn :min ký tự."),
                "password.confirmed" => __("Mật khẩu nhắc lại không khớp."),
                "password_confirmation.required" => __("Vui lòng nhập mật khẩu nhắc lại."),
                "user_id.required" => __("Vui lòng nhập mật user Id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strNewPassword = trim($arrParams["password"]);
        $intUserId = $arrParams["user_id"];
        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        $objUser->password = Hash::make($strNewPassword);
        if ($objUser->save()) {
            return $this->setStatusCode(0)->setMessage(__("Thay đổi mật khẩu thành công."))->setData(["user" => $objUser])->result();
        }

        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Thay đổi mật khẩu thất bại")]
        ])->result();
    }




    public function updatePasswordSales($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "password" => "required|max:50|confirmed|min:6",
                "password_confirmation" => "required",
                "user_id" => "required",

            ],
            [

                "password.required" => __("Vui lòng nhập mật khẩu mới"),
                "password.max" => __("Mật khẩu phải nhỏ hơn :max ký tự."),
                "password.min" => __("Mật khẩu phải lớn hơn :min ký tự."),
                "password.confirmed" => __("Mật khẩu nhắc lại không khớp."),
                "password_confirmation.required" => __("Vui lòng nhập mật khẩu nhắc lại."),
                "user_id.required" => __("Vui lòng nhập mật user Id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strNewPassword = trim($arrParams["password"]);
        $intUserId = $arrParams["user_id"];

        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        $objUser->password_sales = Hash::make($strNewPassword);
        if ($objUser->save()) {
            return $this->setStatusCode(0)->setMessage(__("Thay đổi mật khẩu bán hàng thành công."))->setData(["user" => $objUser])->result();
        }

        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Thay đổi mật khẩu thất bại")]
        ])->result();
    }

    public function changePassword($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "old_password" => "required",
                "password" => "required|max:50|confirmed|min:6",
                "password_confirmation" => "required",
                "user_id" => "required",
            ],
            [

                "old_password.required" => __("Vui lòng nhập mật khẩu cũ"),
                "password.required" => __("Vui lòng nhập mật khẩu mới"),
                "password.max" => __("Mật khẩu phải nhỏ hơn :max ký tự."),
                "password.min" => __("Mật khẩu phải lớn hơn :min ký tự."),
                "password.confirmed" => __("Mật khẩu nhắc lại không khớp."),
                "password_confirmation.required" => __("Vui lòng nhập mật khẩu nhắc lại."),
                "user_id.required" => __("Vui lòng nhập mật user Id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strCurrentPassword = trim($arrParams["old_password"]);
        $strNewPassword = trim($arrParams["password"]);
        $intUserId = $arrParams["user_id"];
        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        if (!Hash::check($strCurrentPassword, $objUser->password)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mật khẩu cũ không hợp lệ.")]
            ])->result();
        }

        $objUser->password = Hash::make($strNewPassword);
        if ($objUser->save()) {
            return $this->setStatusCode(0)->setMessage(__("Thay đổi mật khẩu thành công."))->setData(["user" => $objUser])->result();
        }

        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Thay đổi mật khẩu thất bại")]
        ])->result();
    }

    public function resetPassword($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "password" => "required|max:50|confirmed|min:6",
                "password_confirmation" => "required",
                "user_id" => "required",
            ],
            [

                "password.required" => __("Vui lòng nhập mật khẩu mới"),
                "password.max" => __("Mật khẩu phải nhỏ hơn :max ký tự."),
                "password.min" => __("Mật khẩu phải lớn hơn :min ký tự."),
                "password.confirmed" => __("Mật khẩu nhắc lại không khớp."),
                "password_confirmation.required" => __("Vui lòng nhập mật khẩu nhắc lại."),
                "user_id.required" => __("Vui lòng nhập mật user Id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strNewPassword = trim($arrParams["password"]);
        $intUserId = $arrParams["user_id"];
        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        $objUser->password = Hash::make($strNewPassword);
        if ($objUser->save()) {
            return $this->setStatusCode(0)->setMessage(__("Thay đổi mật khẩu thành công."))->setData(["user" => $objUser])->result();
        }

        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Thay đổi mật khẩu thất bại")]
        ])->result();
    }


    public function changePasswordSales($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "old_password" => "required",
                "password" => "required|max:50|confirmed|min:6",
                "password_confirmation" => "required",
                "user_id" => "required",

            ],
            [

                "old_password.required" => __("Vui lòng nhập mật khẩu cũ"),
                "password.required" => __("Vui lòng nhập mật khẩu mới"),
                "password.max" => __("Mật khẩu phải nhỏ hơn :max ký tự."),
                "password.min" => __("Mật khẩu phải lớn hơn :min ký tự."),
                "password.confirmed" => __("Mật khẩu nhắc lại không khớp."),
                "password_confirmation.required" => __("Vui lòng nhập mật khẩu nhắc lại."),
                "user_id.required" => __("Vui lòng nhập mật user Id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strCurrentPassword = trim($arrParams["old_password"]);
        $strNewPassword = trim($arrParams["password"]);

        $intUserId = $arrParams["user_id"];
        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        if (!Hash::check($strCurrentPassword, $objUser->password_sales)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mật khẩu cũ không hợp lệ.")]
            ])->result();
        }

        $objUser->password_sales = Hash::make($strNewPassword);
        if ($objUser->save()) {
            return $this->setStatusCode(0)->setMessage(__("Thay đổi mật khẩu bán hàng thành công."))->setData(["user" => $objUser])->result();
        }

        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Thay đổi mật khẩu thất bại")]
        ])->result();
    }

    public function createPasswordSales($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "password" => "required",
                "password_sale" => "required|max:50|confirmed|min:6",
                "password_sale_confirmation" => "required",
                "user_id" => "required",
            ],
            [

                "password.required" => __("Vui lòng nhập mật khẩu"),
                "password_sale.required" => __("Vui lòng nhập mật khẩu cấp 2"),
                "password_sale.max" => __("Mật khẩu phải nhỏ hơn :max ký tự."),
                "password_sale.min" => __("Mật khẩu phải lớn hơn :min ký tự."),
                "password_sale.confirmed" => __("Mật khẩu nhắc lại không khớp."),
                "password_sale_confirmation.required" => __("Vui lòng nhập nhắc lại mật khẩu cấp 2."),
                "user_id.required" => __("Vui lòng nhập mật user Id."),

            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strPassword = trim($arrParams["password"]);
        $strPasswordSale = trim($arrParams["password_sale"]);
        $intUserId = $arrParams["user_id"];

        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        if (!empty($objUser->password_sales)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mật khẩu cấp 2 đã tồn tại, bạn không cần phải tạo lại.")]
            ])->result();
        }

        if (!Hash::check($strPassword, $objUser->password)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mật khẩu cũ không hợp lệ.")]
            ])->result();
        }

        $objUser->password_sales = Hash::make($strPasswordSale);
        if ($objUser->save()) {
            return $this->setStatusCode(0)->setMessage(__("Tạo mật khẩu cấp 2 thành công."))->setData(["user" => $objUser])->result();
        }

        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Tạo mật khẩu cấp 2 thất bại")]
        ])->result();
    }


    public function cancelPasswordSales($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "password" => "required",
                "password_sale" => "required",
                "user_id" => "required",
            ],
            [

                "password.required" => __("Vui lòng nhập mật khẩu"),
                "password_sale.required" => __("Vui lòng nhập mật khẩu cấp 2"),
                "user_id.required" => __("Vui lòng nhập mật user Id."),

            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strPassword = trim($arrParams["password"]);
        $strPasswordSale = trim($arrParams["password_sale"]);
        $intUserId = $arrParams["user_id"];


        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        if (!Hash::check($strPassword, $objUser->password)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mật khẩu không hợp lệ.")]
            ])->result();
        }

        if (!Hash::check($strPasswordSale, $objUser->password_sales)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mật khẩu cấp 2 không hợp lệ.")]
            ])->result();
        }

        $objUser->password_sales = "";
        if ($objUser->save()) {
            return $this->setStatusCode(0)->setMessage(__("Huỷ mật khẩu cấp 2 thành công."))->setData(["user" => $objUser])->result();
        }

        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Huỷ mật khẩucấp 2 thất bại")]
        ])->result();
    }


    public function createOrUpdateApiToken($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
            ],
            [

                "user_id.required" => __("Vui lòng nhập mật user Id."),

            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserId = $arrParams["user_id"];
        $objUserToken = UserToken::where(["user_id" => $intUserId])->first();
        if (empty($objUserToken)) {
            $objUserToken = UserToken::create([
                "user_id" => $intUserId,
                "token" => md5(time() . rand(10000000, 99999999)),
                "expired_at" => date('Y-m-d H:i:s', time() + 10368000)
            ]);
        } else {
            $objUserToken->token = md5(time() . rand(10000000, 99999999));
            $objUserToken->save();
        }

        if ($objUserToken) {
            return $this->setStatusCode(0)->setMessage(__("Tạo token thành công."))->setData(["user_token" => $objUserToken])->result();
        }

        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Tạo token thất bại")]
        ])->result();
    }


    public function getBalance($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
            ],
            [

                "user_id.required" => __("backend.enter_user_id"),

            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserId = $arrParams["user_id"];
        $objUserBalance = UserBalance::where(["user_id" => $intUserId])->first();

        $objUserFees = UserFee::where(["user_id" => $intUserId])->get();
        $arrUserFees = [];
        foreach ($objUserFees as $objUserFee) {
            $strNote = __("backend." . $objUserFee->name) . " " . $objUserFee->fee . (in_array($objUserFee->type_id, [3, 4]) ? "%" : "đ") . ($objUserFee->min_fee > 0 ? " " . __('backend.min_fee') . ": $objUserFee->min_fee" . "đ" : "");
            $arrUserFees[] = [
                "name" => __("backend." . $objUserFee->name),
                "fee" => $objUserFee->fee,
                "type_id" => $objUserFee->type_id,
                "min_fee" => $objUserFee->min_fee,
                "note" => $strNote
            ];
        }
        /**
         * Lấy công nợ
         */

        if ($objUserBalance) {
            $objUserDebit = UserDebit::select(\DB::raw('SUM(amount) as amount'))->where("user_id", $intUserId)->first();
            return $this->setStatusCode(0)->setMessage(__('backend.data_retrieved_successfully'))->setData([
                "user_debit" => $objUserDebit,
                "user_balance" => $objUserBalance,
                "user_fees" => $arrUserFees
            ])->result();
        }
        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Lấy dữ liệu thất bại")]
        ])->result();
    }


    public function changeLanguage($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "lang" => "required",
                "user_id" => "required",
            ],
            [
                "lang.required" => __("Vui lòng chọn ngôn ngữ"),
                "user_id.required" => __("Vui lòng nhập mật user Id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserId = $arrParams["user_id"];
        $strLang = trim($arrParams["lang"]);
        $arrLangAllow = ["vi", "en", "jp", "de"];
        if (!in_array($strLang, $arrLangAllow)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ngôn ngữ không cho phép lang phải là (:lang).", ["lang" => implode(",", $arrLangAllow)])]
            ])->result();
        }

        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        $objUser->language = $strLang;
        if ($objUser->save()) {
            return $this->setStatusCode(0)->setMessage(__("Thay đổi ngôn ngữ thành công."))->setData(["user" => $objUser])->result();
        }

        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Thay đổi ngôn ngữ thất bại.")]
        ])->result();
    }



    public function getInfo($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
            ],
            [

                "user_id.required" => __("Vui lòng nhập user_id"),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["user_id"];
        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }
        $objUserGroup = UserGroup::where('id', $objUser->group_id)->first();


        $objUserBalance = UserBalance::where(["user_id" => $intUserId])->first();

        $objUserToken = UserToken::where(["user_id" => $intUserId])->first();
        return $this->setStatusCode(0)->setData(["user" => $objUser, "user_token" => $objUserToken, "user_group" => $objUserGroup, "user_balance" => $objUserBalance])->result();
    }


    public function updateInfo($arrParams = null)
    {

        $validated = Validator::make(
            $arrParams,
            [
                "first_name" => "required|max:50|min:2",
                "last_name" => "required|max:50|min:2",
                "user_id" => "required",
            ],
            [
                "first_name.required" => __("Vui lòng nhập họ."),
                "first_name.max" => __("Họ không được lớn hơn :max ký tự."),
                "first_name.min" => __("Họ không được nhỏ hơn :min ký tự."),
                "last_name.required" => __("Vui lòng nhập tên."),
                "last_name.max" => __("Tên không được lớn hơn :max ký tự."),
                "last_name.min" => __("Tên không được nhỏ hơn :min ký tự."),
                "user_id.required" => __("Vui lòng nhập user_id"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["user_id"];
        $strFirstName = trim($arrParams["first_name"]);
        $strLastName = trim($arrParams["last_name"]);
        $strFullname = $strFirstName . " " . $strLastName;
        $strCompanyName = trim($arrParams["company_name"] ?? "");

        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                "user_not_exist" => [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        $strAddress = trim($arrParams["address"] ?? "");
        if ($strAddress) {
            $objUser->address = $strAddress;
        }
        $objUser->first_name = $strFirstName;
        $objUser->last_name = $strLastName;
        $objUser->fullname = $strFullname;
        $objUser->company_name = $strCompanyName;

        if ($objUser->save()) {
            return $this->setStatusCode(0)->setMessage("Cập nhật thông tin thành công.")->setData(["user" => $objUser])->result();
        }

        return $this->setStatusCode(404)->setMessage("Cập nhật thông tin thất bại.")->setData([])->setErrors([
            [__("Có lỗi xảy ra.")]
        ])->result();
    }


    public function transferBalance($arrParams = null)
    {

        $validated = Validator::make(
            $arrParams,
            [
                "email" => "required|email",
                "amount" => "required",
                "user_id" => "required",
            ],
            [
                "email.required" => __("Vui lòng nhập địa chỉ email"),
                "email.email" => __("Địa chỉ email không hợp lệ."),
                "amount.required" => __("Vui lòng nhập số tiền."),
                "user_id.required" => __("Vui lòng nhập user_id"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["user_id"];
        $strEmail = trim($arrParams["email"]);
        $intAmount = trim($arrParams["amount"]);
        if ($intAmount % 10 !== 0) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tiền phải là bội số của :amountđ.", ["amount" => number_format(10)])]
            ])->result();
        }

        $objUser = User::where(["email" => $strEmail])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        $objFromUser = User::where(["id" => $intUserId])->first();
        if (empty($objFromUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản người chuyển không tồn tại.")]
            ])->result();
        }

        $objToUser = $objUser;
        $userTransactionService = new UserTransactionService();
        $resultUserTransactionService = $userTransactionService->transfer([
            'from_user_id' => $objFromUser->id,
            'to_user_id' => $objToUser->id,
            'amount' => $intAmount,
            'note' => $arrParams["note"] ?? ""
        ]);

        if (empty($resultUserTransactionService["success"])) {
            return $resultUserTransactionService;
        }
        return $resultUserTransactionService;
    }


    public function updateImageAvatar($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "image_base64" => "required",
                "user_id" => "required",
            ],
            [
                "image_base64.required" => __("Vui lòng nhập image_base64."),
                "user_id.required" => __("Vui lòng nhập user_id"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["user_id"];
        $strBase64 = $arrParams["image_base64"];
        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                "user_not_exist" => [__("Tài khoản không tồn tại.")]
            ])->result();
        }


        if (!is_dir(base_path("static/uploads/avatars"))) {
            mkdir(base_path("static/uploads/avatars"), 0755, true);
        }


        $image = base64_decode($strBase64);
        $infoImages = getimagesizefromstring(base64_decode($strBase64));
        if (!$infoImages) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Hình ảnh không đúng định dạng.")]
            ])->result();
        }
        $intWidth = $infoImages[0];
        $intHeight = $infoImages[1];
        $strType = ".jpg";
        switch ($infoImages["mime"]) {
            case 'image/png':
                $strType = ".png";
                break;
            case 'image/gif':
                $strType = ".gif";
                break;
            default:
                break;
        }

        $strUrlImage = "uploads/avatars/$intUserId-" . md5(time()) . $strType;
        $strPathImage = base_path("static/" . $strUrlImage);
        $resultWriteFile = file_put_contents($strPathImage, $image);
        if (!$resultWriteFile) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }
        $objUser->image_avatar = $strUrlImage;
        if ($objUser->save()) {
            return $this->setStatusCode(0)->setMessage(__("Thành công."))->setData(["user" => $objUser])->result();
        }
        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            "server_error" => [__("Có lỗi xảy ra.")]
        ])->result();
    }


    public function updateImageCover($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "image_base64" => "required",
                "user_id" => "required",
            ],
            [
                "image_base64.required" => __("Vui lòng nhập image_base64."),
                "user_id.required" => __("Vui lòng nhập user_id"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserId = $arrParams["user_id"];
        $strBase64 = $arrParams["image_base64"];
        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                "user_not_exist" => [__("Tài khoản không tồn tại.")]
            ])->result();
        }


        if (!is_dir(base_path("static/uploads/covers"))) {
            mkdir(base_path("static/uploads/covers"), 0755, true);
        }


        $image = base64_decode($strBase64);
        $infoImages = getimagesizefromstring(base64_decode($strBase64));
        if (!$infoImages) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Hình ảnh không đúng định dạng.")]
            ])->result();
        }
        $intWidth = $infoImages[0];
        $intHeight = $infoImages[1];
        $strType = ".jpg";
        switch ($infoImages["mime"]) {
            case 'image/png':
                $strType = ".png";
                break;
            case 'image/gif':
                $strType = ".gif";
                break;
            default:
                break;
        }

        $strUrlImage = "uploads/covers/$intUserId-" . md5(time()) . $strType;
        $strPathImage = base_path("static/" . $strUrlImage);
        $resultWriteFile = file_put_contents($strPathImage, $image);
        if (!$resultWriteFile) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }

        $objUser->image_cover = $strUrlImage;
        if ($objUser->save()) {
            return $this->setStatusCode(0)->setMessage(__("Thành công."))->setData(["user" => $objUser])->result();
        }
        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            "server_error" => [__("Có lỗi xảy ra.")]
        ])->result();
    }

    public function getList($arrParams = [])
    {
        $this->arrFillable = array_merge($this->arrFillable, (new UserGroup())->getFillable());
        $this->arrFillable = array_merge($this->arrFillable, (new UserBalance())->getFillable());
        $this->arrFillable = array_merge($this->arrFillable, (new User())->getFillableParent());

        $intPage = $arrParams["page"] ?? 1;
        $intLimit = $arrParams["limit"] ?? 20;
        $intOffset = ($intPage - 1) * $intLimit;
        $objUsers = User::select(\DB::raw('users.*,users.created_at as created_at,user_groups.name as "user_group_name",user_balances.balance,user_parents.fullname as parent_fullname'))
            ->leftJoin('users as user_parents', 'users.user_parent_id', 'user_parents.id')
            ->leftJoin('user_groups', 'user_groups.id', 'users.group_id')
            ->leftJoin('user_balances', 'user_balances.user_id', 'users.id');

        $objUsers = $this->getListBuilder($objUsers, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */
        $objTotal = $objUsers;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUsers = $objUsers->orderBy("users.id", "DESC");
        }
        $objUsers = $objUsers->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'users' => $objUsers,
            'records_total' => $intTotal,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }

    public function exportExcel($arrParams = [])
    {
        $this->arrFillable = array_merge($this->arrFillable, (new UserGroup())->getFillable());
        $this->arrFillable = array_merge($this->arrFillable, (new UserBalance())->getFillable());
        $this->arrFillable = array_merge($this->arrFillable, (new User())->getFillableParent());

        $objUsers = User::select(\DB::raw('users.*,users.created_at as created_at,user_groups.name as "user_group_name",user_balances.balance,user_parents.fullname as parent_fullname'))
            ->leftJoin('users as user_parents', 'users.user_parent_id', 'user_parents.id')
            ->leftJoin('user_groups', 'user_groups.id', 'users.group_id')
            ->leftJoin('user_balances', 'user_balances.user_id', 'users.id');

        $objUsers = $this->getListBuilder($objUsers, $arrParams, $this->arrFillable)->orderBy('users.id', 'DESC')->get();

        if ($objUsers->isEmpty()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([[__("Không tìm thấy dữ liệu để xuất.")]])->result();
        }
        $fileName = 'exports/export_user_' . time() . '.xlsx';
        $resultExport = \Excel::store(new UserExport(['objUsers' => $objUsers]), $fileName, 'export-excel', null);
        if (!$resultExport) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([[__('Xuất file thất bại.')]])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xuất danh sách người dùng thành công."))->setData(["url" => url("static/" . $fileName)])->result();
    }



    public function responseSelect2($arrResult = [])
    {
        if ($arrResult["error_code"] != 0) {
            return [];
        }

        $intLimit = $arrResult["data"]["limit"] ?? 1;
        $intPage = $arrResult["data"]["page"] ?? 1;

        $objUsers = $arrResult["data"]["users"];
        $arrData = [];
        foreach ($objUsers as $objUser) {
            $arrData[] = [
                "id" => $objUser->id,
                "text" => $objUser->fullname . " " . $objUser->email,
            ];
        }
        return ["results" => $arrData, "pagination" => ["more" => $arrResult["data"]["records_total"] >= ($intLimit * $intPage) ? true : false], 'limit' => $intLimit];
    }


    public function getDetail($arrParams = [])
    {

        $objUser = User::select(\DB::raw('*'));
        $objUser = $this->getListBuilder($objUser, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objUser = $objUser->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        $objUserParent = User::where('id', $objUser->user_parent_id)->first();
        $objUserGroup = UserGroup::where('id', $objUser->group_id)->first();
        $objUserFees = UserFee::where('user_id', $objUser->id)->get();
        $objUserBalance = UserBalance::where('user_id', $objUser->id)->first();
        $objGatewayAccounts = GatewayAccount::whereIn('id', explode(",", $objUser->gateway_withdraw_ids))->get();
        $arrGatewayAccountWithdraws = [];
        foreach ($objGatewayAccounts as $objGatewayAccount) {
            $arrGatewayAccountWithdraws[] = [
                "id" => $objGatewayAccount->id,
                "name" => $objGatewayAccount->name,
            ];
        }

        $objGatewayAccounts = GatewayAccount::whereIn('id', explode(",", $objUser->gateway_collection_ids))->get();
        $arrGatewayAccountCollections = [];
        foreach ($objGatewayAccounts as $objGatewayAccount) {
            $arrGatewayAccountCollections[] = [
                "id" => $objGatewayAccount->id,
                "name" => $objGatewayAccount->name,
            ];
        }

        $objUserWithdrawLimit = UserWithdrawLimit::where('user_id', $objUser->id)->first();
        if (empty($objUserWithdrawLimit)) {
            $objUserWithdrawLimit = UserWithdrawLimit::create([
                'user_id' => $objUser->id,
                'retain_balance' => 0,
                'withdraw_limit_in_day' => 1000000000,
                'withdraw_limit_bank_account_in_day' => 500000000,
                'lock_withdraw' => 0,
                'allow_withdraw_day' => '2,3,4,5,6,7,8',
            ]);
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['user' => $objUser, 'gateway_account_withdraws' => $arrGatewayAccountWithdraws, 'gateway_account_collections' => $arrGatewayAccountCollections, 'user_parent' => $objUserParent, 'user_group' => $objUserGroup, 'user_fees' => $objUserFees, 'user_balance' => $objUserBalance, 'user_withdraw_limit' => $objUserWithdrawLimit])->result();
    }

    public function add($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "first_name" => "required",
                "phone" => "required|max:12|min:9",
                "last_name" => "required",
                "email" => "required|email",
                "password" => "required|max:50|min:6",
            ],
            [
                "first_name.required" => __("Vui lòng nhập tên."),
                "last_name.required" => __("Vui lòng nhập họ."),
                "phone.required" => __("Vui lòng nhập số điện thoại."),
                "phone.max" => __("Số điện thoại không được lớn hơn :max ký tự."),
                "phone.min" => __("Số điện thoại không được nhỏ hơn :min ký tự."),
                "email.required" => __("Vui lòng nhập địa chỉ email"),
                "email.email" => __("Địa chỉ email không hợp lệ."),
                "password.required" => __("Vui lòng nhập mật khẩu."),
                "password.max" => __("Mật khẩu không được lớn hơn :max ký tự."),
                "password.min" => __("Mật khẩu không được nhỏ hơn :min ký tự."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $arrParams["password"] = $arrParams["password"] ?? Str::random(5);
        $arrParams["user_parent_id"] = $arrParams["user_parent_id"] ?? 0;
        $strFirstName = $arrParams["first_name"];
        $strLastName = $arrParams["last_name"];
        $arrParams["fullname"] = $strFirstName . " " . $strLastName;
        $arrParams["city_id"] = $arrParams["city_id"] ?? 0;
        $arrParams["district_id"] = $arrParams["district_id"] ?? 0;
        $arrParams["ward_id"] = $arrParams["ward_id"] ?? 0;
        $arrParams["company_name"] = $arrParams["company_name"] ?? "";
        $arrParams["address"] = $arrParams["address"] ?? "";
        $strEmail = $arrParams["email"];
        $arrWithdrawId = $arrParams["gateway_withdraw_ids"] ?? [];
        $arrParams["gateway_withdraw_ids"] = implode(",", $arrWithdrawId);
        $arrCollectionId = $arrParams["gateway_collection_ids"] ?? [];
        $arrParams["gateway_collection_ids"] = implode(",", $arrCollectionId);


        if (User::where(["email" => $strEmail])->exists() === true) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Địa chỉ email này đã tồn tại trên hệ thống.")]
            ])->result();
        }

        \DB::beginTransaction();
        $arrParams["password"] = Hash::make($arrParams["password"] ?? "88888888");
        $arrInsert = self::getFilterParams($arrParams, (new User())->getFillable());
        $arrInsert["payment_code"] = $this->createPaymentCode();

        /**
         * Mặc định N+1
         */

        if (empty($arrInsert["number_day_for_control"])) {
            $arrInsert["number_day_for_control"] = 0;
            $arrInsert["for_control_type"] = 3;
        } else {
            $arrInsert["number_day_for_control"] = $arrInsert["number_day_for_control"] ?? 1;
            $arrInsert["for_control_type"] = $arrInsert["for_control_type"] ?? 4;
        }
        // $arrInsert["number_day_for_control"] = 1;

        $objUser = User::create($arrInsert);
        if (!$objUser) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Có lỗi xảy ra, vui lòng kiểm tra lại.")]
            ])->result();
        }

        /**
         * Khởi tạo tài khoản chính
         */
        $objCreateUserBalance = UserBalance::create([
            'user_id' => $objUser->id,
            'balance' => 0,
        ]);

        if (!$objCreateUserBalance) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Có lỗi xảy ra, vui lòng kiểm tra lại.")]
            ])->result();
        }

        $res = openssl_pkey_new();
        openssl_pkey_export($res, $strPrivateKey);
        $strPublicKey = openssl_pkey_get_details($res)['key'];
        // $strPublicKey = openssl_pkey_get_public($publicKeyPem);

        $objUserToken = UserToken::create([
            'user_id' => $objUser->id,
            'system_private_key' => $strPrivateKey,
            'system_public_key' => $strPublicKey,
            'token' => md5(time() . $objUser->id),
            'token_gateway' => md5(time() . $objUser->id . "." . rand(1111, 9999)),
            'expired_at' => date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 365 * 5))
        ]);

        if (!$objUserToken) {
            \DB::rollBack();
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Có lỗi xảy ra khi tạo token.")]
            ])->result();
        }

        UserFee::create([
            'user_id' => $objUser->id,
            'name' => "Phí % in",
            'status_id' => 2,
            'type_id' => 3,
            'fee' => 0.6,
            'min_fee' => 3000,
        ]);

        UserFee::create([
            'user_id' => $objUser->id,
            'name' => "Phí % out",
            'status_id' => 2,
            'type_id' => 4,
            'fee' => 0.2,
            'min_fee' => 3000,
        ]);

        UserReferalFee::create([
            'user_id' => $objUser->id,
            'name' => "Phí % in",
            'status_id' => 2,
            'type_id' => 3,
            'fee' => 0,
            'min_fee' => 0,
        ]);

        UserReferalFee::create([
            'user_id' => $objUser->id,
            'name' => "Phí % out",
            'status_id' => 2,
            'type_id' => 4,
            'fee' => 0,
            'min_fee' => 0,
        ]);

        UserWithdrawLimit::create([
            'user_id' => $objUser->id,
            'retain_balance' => 0,
            'withdraw_limit_in_day' => 1000000000,
            'withdraw_limit_bank_account_in_day' => 500000000,
        ]);

        \DB::commit();
        return $this->setStatusCode(0)->setMessage(__("Tạo tài khoản thành công."))->setData(['user' => $objUser])->result();
    }


    public function createPaymentCode()
    {
        $strPaymentCode = strtoupper(\Str::random(6));
        $objUser = User::where('payment_code', $strPaymentCode)->first();
        if ($objUser) {
            return $this->createPaymentCode();
        }
        return $strPaymentCode;
    }
    // payment_code
    public function update($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
                "first_name" => "required",
                "last_name" => "required",
            ],
            [
                "id.required" => __("Vui lòng nhập id người dùng."),
                "first_name.required" => __("Vui lòng nhập tên."),
                "last_name.required" => __("Vui lòng nhập họ."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserParentId = $arrParams["user_parent_id"] = $arrParams["user_parent_id"] ?? 0;
        $intId = $arrParams["id"];
        $strFirstName = $arrParams["first_name"];
        $strLastName = $arrParams["last_name"];
        $arrParams["fullname"] = $strFirstName . " " . $strLastName;
        $arrWithdrawId = $arrParams["gateway_withdraw_ids"] ?? [];
        $arrParams["gateway_withdraw_ids"] = implode(",", $arrWithdrawId);
        $arrCollectionId = $arrParams["gateway_collection_ids"] ?? [];
        $arrParams["gateway_collection_ids"] = implode(",", $arrCollectionId);


        $objUser = User::where("id", $intId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("ID người dùng không tồn tại.")]
            ])->result();
        }

        if (!empty($intUserParentId)) {
            if ($intUserParentId != $objUser->user_parent_id) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Cấp cha không được là chính bạn.")]
                ])->result();
            }
        }

        if (!empty($arrParams["password"])) {
            $validated = Validator::make(
                $arrParams,
                [
                    "password" => "required|max:50|min:6",
                ],
                [
                    "password.required" => __("Vui lòng nhập mật khẩu."),
                    "password.max" => __("Mật khẩu không được lớn hơn :max ký tự."),
                    "password.min" => __("Mật khẩu không được nhỏ hơn :min ký tự."),
                ]
            );
            if ($validated->errors()->messages()) {
                return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
            }

            $arrParams["password"] = Hash::make($arrParams["password"]);
        } else {
            unset($arrParams["password"]);
        }


        if (isset($arrParams["number_day_for_control"])) {
            if (empty($arrParams["number_day_for_control"])) {
                $arrParams["number_day_for_control"] = 0;
                $arrParams["for_control_type"] = 3;
            } else {
                $arrParams["number_day_for_control"] = $arrParams["number_day_for_control"] ?? 1;
                $arrParams["for_control_type"] = $arrParams["for_control_type"] ?? 4;
            }
        }
        $arrUpdate = self::getFilterParams($arrParams, (new User())->getFillable());
        foreach ($arrUpdate as $key => $value) {
            $objUser->{$key} = $value;
        }

        if (!$objUser->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật người dùng thất bại.")]
            ])->result();
        }

        $objToken = UserToken::where('user_id', $intId)->first();
        if ($objToken) {
            if (empty($objToken->system_private_key)) {
                $res = openssl_pkey_new();
                openssl_pkey_export($res, $strPrivateKey);
                $strPublicKey = openssl_pkey_get_details($res)['key'];
                // $strPublicKey                 = openssl_pkey_get_public($publicKeyPem);
                $objToken->system_private_key = $strPrivateKey;
                $objToken->system_public_key = $strPublicKey;
                $objToken->save();
            }
        }
        return $this->setStatusCode(0)->setMessage(__("Cập nhật người dùng thành công."))->setData(['user' => $objUser->toArray()])->result();
    }

    public function updateWithdrawLimit($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập id người dùng."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["user_id"];
        $objUser = User::where("id", $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("ID người dùng không tồn tại.")]
            ])->result();
        }

        $objWithdrawLimit = UserWithdrawLimit::where('user_id', $intUserId)->first();
        if (empty($objWithdrawLimit)) {
            $objWithdrawLimit = new UserWithdrawLimit();
            $objWithdrawLimit->user_id = $intUserId;
            $objWithdrawLimit->retain_balance = 0;
        }

        $intWithdrawLimitInDay = (int) preg_replace('/[^0-9]/', '', $arrParams["withdraw_limit_in_day"] ?? 0);
        $intWithdrawLimitBankAccountInDay = (int) preg_replace('/[^0-9]/', '', $arrParams["withdraw_limit_bank_account_in_day"] ?? 0);
        $intLockWithdraw = !empty($arrParams["lock_withdraw"]) ? 1 : 0;
        $arrAllowWithdrawDay = $arrParams["allow_withdraw_day"] ?? [];
        if (!is_array($arrAllowWithdrawDay)) {
            $arrAllowWithdrawDay = array_filter(explode(",", (string) $arrAllowWithdrawDay));
        }
        $strAllowWithdrawDay = implode(",", array_map('intval', $arrAllowWithdrawDay));

        $objWithdrawLimit->withdraw_limit_in_day = $intWithdrawLimitInDay;
        $objWithdrawLimit->withdraw_limit_bank_account_in_day = $intWithdrawLimitBankAccountInDay;
        $objWithdrawLimit->lock_withdraw = $intLockWithdraw;
        $objWithdrawLimit->allow_withdraw_day = $strAllowWithdrawDay;

        if (!$objWithdrawLimit->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật hạn mức rút tiền thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Cập nhật hạn mức rút tiền thành công."))->setData(['user_withdraw_limit' => $objWithdrawLimit])->result();
    }

    public function updateActiveAndDeactive($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
                "actived" => "required"
            ],
            [

                "id.required" => __("Vui lòng nhập mã thiết bị."),
                "actived.required" => __("Vui lòng nhập trạng thái."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intActived = !empty($arrParams["actived"]) ? 1 : 0;
        $intId = $arrParams["id"];
        $objUser = User::where('id', $intId)->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy người dùng này.")]
            ])->result();
        }

        $objUser->actived = $intActived;
        $objUser->locked = $intActived == 1 ? 0 : 1;
        if (!$objUser->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData(["user" => $objUser])->result();
    }

    public function getListSignInLogs($arrParams = [])
    {
        $intPage = $arrParams["page"] ?? 1;
        $intLimit = $arrParams["limit"] ?? 20;
        $intOffset = ($intPage - 1) * $intLimit;

        $objUserSigninLogs = UserSigninLog::select();
        $objUserSigninLogs = $this->getListBuilder($objUserSigninLogs, $arrParams, (new UserSigninLog())->getFillable());

        $objTotal = $objUserSigninLogs;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUserSigninLogs = $objUserSigninLogs->orderBy("id", "DESC");
        }
        $objUserSigninLogs = $objUserSigninLogs->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            "user_signin_logs" => $objUserSigninLogs,
            'records_total' => $intTotal,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams
        ])->result();
    }


    public function delete($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intId = $arrParams["id"];
        $objUser = User::where('id', $intId)->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }
        \DB::beginTransaction();
        if (!$objUser->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá thất bại.")]
            ])->result();
        }

        Trash::create([
            'table' => 'users',
            'data' => json_encode($objUser->toArray())
        ]);


        /**
         * Xoá Phí
         */


        // $objUserFees    = UserFee::where("user_id", $intId)->get();
        // $userFeeService = new UserFeeService();
        // foreach ($objUserFees as $objUserFee) {
        //     $resultDeleteUserFee = $userFeeService->delete(["id" => $objUserFee->id]);
        //     if ($resultDeleteUserFee["error_code"] != 0) {
        //         \DB::rollBack();
        //         return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
        //             [__("Có lỗi xảy ra khi xoá phí.")]
        //         ])->result();
        //     }
        // }
        \DB::commit();
        return $this->setStatusCode(0)->setMessage(__("Xoá thành công."))->setData([])->result();
    }


    public function authy2Factor($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập user_id"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["user_id"];
        $objUser = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        if ($objUser->authy_2factor) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản của bạn đã được kích hoạt 2 lớp, vui lòng không kích hoạt lại.")]
            ])->result();
        }

        $issuer = config('app.name');
        $accountName = $objUser->email;
        $secretFactory = new SecretFactory();
        $secret = $secretFactory->create($issuer, $accountName);
        $strSecretKey = $secret->getSecretKey();


        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: "otpauth://totp/$accountName?secret=$strSecretKey&issuer=$issuer",
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $arrParams["size"] ?? 200,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoResizeToWidth: 50,
            logoPunchoutBackground: true,
            labelText: $arrParams["label"] ?? "",
            labelFont: new OpenSans(20),
            labelAlignment: LabelAlignment::Center
        );
        $result = $builder->build();

        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData([
            "authy_2factor" => [
                'secret_key' => $strSecretKey,
                'qrcode_base64' => base64_encode($result->getString())
            ]
        ])->result();
    }

    public function validateAuthy2Factor($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
                "secret_key" => "required",
                "code" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập user_id"),
                "secret_key.required" => __("Vui lòng nhập secret_key"),
                "code.required" => __("Vui lòng nhập code, mã xác thực từ ứng dụng"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["user_id"];
        $strSecretKey = $arrParams["secret_key"];
        $strCode = $arrParams["code"];
        $objUser = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        if ($objUser->authy_2factor) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản của bạn đã được kích hoạt 2 lớp, vui lòng không kích hoạt lại.")]
            ])->result();
        }

        $googleAuth = new GoogleAuthenticator();
        $result = $googleAuth->authenticate($strSecretKey, $strCode);
        if (!$result) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xác nhận mã đăng nhập 2 lớp thất bại.")]
            ])->result();
        }
        $objUser->authy_2factor = 1;
        $objUser->authy_2factor_secret_key = $strSecretKey;
        if (!$objUser->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận dữ liệu thất bại, vui lòng kiểm tra lại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Cập nhật xác thực bảo mật 2 lớp thành công."))->setData([])->result();
    }


    public function cancelAuthy2Factor($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
                "password" => "required|max:50|min:6",
                "code" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập user_id"),
                "password.required" => __("Vui lòng nhập mật khẩu mới"),
                "code.required" => __("Vui lòng nhập code, mã xác thực từ ứng dụng"),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strPassword = trim($arrParams["password"]);
        $strCode = $arrParams["code"];
        $intUserId = $arrParams["user_id"];

        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }


        if (!Hash::check($strPassword, $objUser->password)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mật khẩu không hợp lệ.")]
            ])->result();
        }


        $googleAuth = new GoogleAuthenticator();
        $result = $googleAuth->authenticate($objUser->authy_2factor_secret_key, $strCode);
        if (!$result) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mã bảo mật không đúng.")]
            ])->result();
        }
        $objUser->authy_2factor = 0;
        $objUser->authy_2factor_secret_key = "";
        if (!$objUser->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận dữ liệu thất bại, vui lòng kiểm tra lại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Huỷ xác thực bảo mật 2 lớp thành công."))->setData([])->result();
    }

    public function requestOtpWithdraw($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
                "otp" => "required",
                "password" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập user_id"),
                "otp.required" => __("Vui lòng nhập code, mã xác thực từ ứng dụng"),
                "password.required" => __("Vui lòng nhập mật khẩu đăng nhập"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["user_id"];
        $strOtp = $arrParams["otp"];
        $strPassword = $arrParams["password"];
        $objUser = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        if ($objUser->otp_widthdraw) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản của bạn đã được kích otp chuyển khoản.")]
            ])->result();
        }


        if (!Hash::check($strPassword, $objUser->password)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mật khẩu không đúng.")]
            ])->result();
        }

        $objUser->otp_widthdraw = 1;
        $objUser->otp_widthdraw_key = $strOtp;
        if (!$objUser->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận dữ liệu thất bại, vui lòng kiểm tra lại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Cập nhật xác thực otp chuyển khoản thành công."))->setData([])->result();
    }

    public function checkAuthenticator($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
                "otp" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập user_id"),
                "otp.required" => __("Vui lòng nhập code, mã xác thực từ ứng dụng"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserId = $arrParams["user_id"];
        $strOtp = $arrParams["otp"];
        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        $googleAuth = new GoogleAuthenticator();
        $result = $googleAuth->authenticate($objUser->authy_2factor_secret_key, $strOtp);
        if (!$result) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mã xác thực không đúng.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Mã xác thực hợp lệ."))->setData([])->result();
    }

    public function createQrPayment($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập user_id"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserId = $arrParams["user_id"];
        $intAmount = $arrParams["amount"] ?? 0;
        $strRemark = $arrParams["remark"] ?? "";
        $objUser = User::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        /**
         * Lấy cấu hình ngân hàng
         */

        $objUserBank = UserBankAccount::select()
            ->join('bank_accounts', 'bank_accounts.id', 'user_bank_accounts.bank_account_id')
            ->join('banks', 'banks.id', 'bank_accounts.bank_id')->where('user_bank_accounts.user_id', $intUserId)->where('user_bank_accounts.status_id', 2)->first();
        if (!$objUserBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Bạn chưa được cấu hình ngân hàng nào.")]
            ])->result();
        }


        $strInfoRemark = "RBT$objUser->payment_code" . (!empty($strRemark) ? " " . $strRemark : "");
        $generator = (new Generator())->create()
            ->bankId(trim($objUserBank->short_code))
            ->accountNo(trim($objUserBank->bank_account_number)) // Account number
            ->amount($intAmount) // Money
            ->info($strInfoRemark) // Ref
            ->generate();

        $arrGenerator = json_decode($generator, true);
        if ($arrGenerator["code"] != 200) {
            return $this->setStatusCode(404)->setMessage("")->setData($arrGenerator)->setErrors([
                [__("Có lỗi khi tạo mã QR.")]
            ])->result();
        }


        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $arrGenerator["data"] ?? "",
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $arrParams["size"] ?? 200,
            margin: 3,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoResizeToWidth: 50,
            logoPunchoutBackground: true,
            labelText: $arrParams["label"] ?? "",
            labelFont: new OpenSans(20),
            labelAlignment: LabelAlignment::Center
        );
        $result = $builder->build();
        $arrPayment = [
            "bank" => [
                "bank_name" => $objUserBank->name,
                "short_name" => $objUserBank->short_name,
                "bank_account_name" => $objUserBank->bank_account_name,
                "bank_account_number" => $objUserBank->bank_account_number
            ],
            "user_payment_code" => $objUser->payment_code,
            "remark" => $strInfoRemark,
            'payment_qrcode_base64' => base64_encode($result->getString()),
            'payment_qrcode_plantext' => $arrGenerator["data"] ?? "",
        ];
        return $this->setStatusCode(0)->setMessage(__("Lấy thông tin thành công."))->setData($arrPayment)->result();
    }
}
