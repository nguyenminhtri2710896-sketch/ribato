<?php

namespace App\Services;

use App\Models\Gateway;
use App\Models\SubUser;
use App\Models\User;
use App\Models\UserBalance;
use Dolondro\GoogleAuthenticator\GoogleAuthenticator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Dolondro\GoogleAuthenticator\SecretFactory;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class SubUserService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new SubUser())->getFillable();
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Chờ kích hoạt'
        ],
        2 => [
            'name' => 'Hoạt động'
        ]
    ];

    public function getList($arrParams = [])
    {

        $intPage = $arrParams["page"] ?? 1;
        $intLimit = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;
        $objSubUsers = SubUser::select();
        $objSubUsers = $this->getListBuilder($objSubUsers, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objSubUsers;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objSubUsers = $objSubUsers->orderBy("sub_users.id", "DESC");
        }
        $objSubUsers = $objSubUsers->offset($intOffset)->limit($intLimit)->get();
        return $this->setStatusCode(0)->setData([
            'sub_users' => $objSubUsers,
            'records_total' => $intTotal,
            'status' => self::$arrStatusId,
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

        $objSubUsers = $arrResult["data"]["sub_users"];
        $arrData = [];
        foreach ($objSubUsers as $objSubUser) {
            $arrData[] = [
                "id" => $objSubUser->id,
                "text" => $objSubUser->name,
            ];
        }
        return ["results" => $arrData, "pagination" => ["more" => $arrResult["data"]["records_total"] >= ($intLimit * $intPage) ? true : false], 'limit' => $intLimit];
    }

    public function getDetail($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
                "sub_user_id" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập user id."),
                "sub_user_id.required" => __("Vui lòng nhập id người dùng."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["user_id"];
        $intId = $arrParams["sub_user_id"];

        $objSubUser = SubUser::where('id', $intId)->first();
        if (empty($objSubUser)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        if ($intUserId != $objSubUser->user_id) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Bạn không có quyền cập nhật tài khoản này.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['sub_user' => $objSubUser])->result();
    }

    public function add($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
                "first_name" => "required",
                "phone" => "required|max:12|min:9",
                "last_name" => "required",
                "email" => "required|email",
                "password" => "required|max:50|min:6",
            ],
            [
                "user_id.required" => __("Vui lòng nhập user id."),
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

        $strFirstName = $arrParams["first_name"];
        $strLastName = $arrParams["last_name"];
        $arrParams["fullname"] = $strFirstName . " " . $strLastName;
        $arrParams["city_id"] = $arrParams["city_id"] ?? 0;
        $arrParams["district_id"] = $arrParams["district_id"] ?? 0;
        $arrParams["ward_id"] = $arrParams["ward_id"] ?? 0;
        $arrParams["company_name"] = $arrParams["company_name"] ?? "";
        $arrParams["address"] = $arrParams["address"] ?? "";
        $strEmail = $arrParams["email"];


        if (SubUser::where(["email" => $strEmail])->exists() === true) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Địa chỉ email này đã tồn tại trên hệ thống.")]
            ])->result();
        }

        $arrParams["password"] = Hash::make($arrParams["password"] ?? "88888888");
        $arrInsert = self::getFilterParams($arrParams, (new SubUser())->getFillable());
        $objSubUser = SubUser::create($arrInsert);
        if (!$objSubUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Có lỗi xảy ra, vui lòng kiểm tra lại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Tạo tài khoản thành công."))->setData(['sub_user' => $objSubUser])->result();
    }

    public function update($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
                "sub_user_id" => "required",
                "first_name" => "required",
                "last_name" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập user id."),
                "sub_user_id.required" => __("Vui lòng nhập id người dùng."),
                "first_name.required" => __("Vui lòng nhập tên."),
                "last_name.required" => __("Vui lòng nhập họ."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["user_id"];
        $intId = $arrParams["sub_user_id"];
        $strFirstName = $arrParams["first_name"];
        $strLastName = $arrParams["last_name"];
        $arrParams["fullname"] = $strFirstName . " " . $strLastName;
        $objSubUser = SubUser::where("id", $intId)->first();
        if (!$objSubUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("ID người dùng không tồn tại.")]
            ])->result();
        }

        if ($intUserId != $objSubUser->user_id) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Bạn không có quyền cập nhật tài khoản này.")]
            ])->result();
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

        $arrUpdate = self::getFilterParams($arrParams, (new SubUser())->getFillable());
        foreach ($arrUpdate as $key => $value) {
            $objSubUser->{$key} = $value;
        }

        if (!$objSubUser->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật người dùng thất bại.")]
            ])->result();
        }


        return $this->setStatusCode(0)->setMessage(__("Cập nhật người dùng thành công."))->setData(['sub_user' => $objSubUser])->result();
    }
    public function delete($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
                "sub_user_id" => "required",
            ],
            [
                "user_id.required" => __("Vui lòng nhập user id."),
                "sub_user_id.required" => __("Vui lòng nhập id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intId = $arrParams["sub_user_id"];
        $intUserId = $arrParams["user_id"];
        $objSubUser = SubUser::where("id", $intId)->first();
        if (!$objSubUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("ID người dùng không tồn tại.")]
            ])->result();
        }

        if ($intUserId != $objSubUser->user_id) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Bạn không có quyền xoá tài khoản này.")]
            ])->result();
        }

        if (!$objSubUser->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xoá thành công."))->setData([])->result();
    }


    public function getInfo($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "sub_user_id" => "required",
            ],
            [

                "sub_user_id.required" => __("Vui lòng nhập sub_user_id"),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["sub_user_id"];
        $objSubUser = SubUser::where(["id" => $intUserId])->first();
        if (empty($objSubUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setData(["sub_user" => $objSubUser])->result();
    }


    public function changePassword($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "old_password" => "required",
                "password" => "required|max:50|confirmed|min:6",
                "password_confirmation" => "required",
                "sub_user_id" => "required",
            ],
            [

                "old_password.required" => __("Vui lòng nhập mật khẩu cũ"),
                "password.required" => __("Vui lòng nhập mật khẩu mới"),
                "password.max" => __("Mật khẩu phải nhỏ hơn :max ký tự."),
                "password.min" => __("Mật khẩu phải lớn hơn :min ký tự."),
                "password.confirmed" => __("Mật khẩu nhắc lại không khớp."),
                "password_confirmation.required" => __("Vui lòng nhập mật khẩu nhắc lại."),
                "sub_user_id.required" => __("Vui lòng nhập mật user Id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strCurrentPassword = trim($arrParams["old_password"]);
        $strNewPassword = trim($arrParams["password"]);
        $intSubUserId = $arrParams["sub_user_id"];
        $objSubUser = SubUser::where(["id" => $intSubUserId])->first();
        if (empty($objSubUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        if (!Hash::check($strCurrentPassword, $objSubUser->password)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mật khẩu cũ không hợp lệ.")]
            ])->result();
        }

        $objSubUser->password = Hash::make($strNewPassword);
        if ($objSubUser->save()) {
            return $this->setStatusCode(0)->setMessage(__("Thay đổi mật khẩu thành công."))->setData(["sub_user" => $objSubUser])->result();
        }

        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Thay đổi mật khẩu thất bại")]
        ])->result();
    }

    public function updateInfo($arrParams = null)
    {

        $validated = Validator::make(
            $arrParams,
            [
                "first_name" => "required|max:50|min:2",
                "last_name" => "required|max:50|min:2",
                "sub_user_id" => "required",
            ],
            [
                "first_name.required" => __("Vui lòng nhập họ."),
                "first_name.max" => __("Họ không được lớn hơn :max ký tự."),
                "first_name.min" => __("Họ không được nhỏ hơn :min ký tự."),
                "last_name.required" => __("Vui lòng nhập tên."),
                "last_name.max" => __("Tên không được lớn hơn :max ký tự."),
                "last_name.min" => __("Tên không được nhỏ hơn :min ký tự."),
                "sub_user_id.required" => __("Vui lòng nhập sub_user_id"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["sub_user_id"];
        $strFirstName = trim($arrParams["first_name"]);
        $strLastName = trim($arrParams["last_name"]);
        $strFullname = $strFirstName . " " . $strLastName;
        $strCompanyName = trim($arrParams["company_name"] ?? "");

        $objSubUser = SubUser::where(["id" => $intUserId])->first();
        if (empty($objSubUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                "user_not_exist" => [__("Tài khoản không tồn tại.")]
            ])->result();
        }

        $strAddress = trim($arrParams["address"] ?? "");
        if ($strAddress) {
            $objSubUser->address = $strAddress;
        }
        $objSubUser->first_name = $strFirstName;
        $objSubUser->last_name = $strLastName;
        $objSubUser->fullname = $strFullname;
        $objSubUser->company_name = $strCompanyName;

        if ($objSubUser->save()) {
            return $this->setStatusCode(0)->setMessage("Cập nhật thông tin thành công.")->setData(["sub_user" => $objSubUser])->result();
        }

        return $this->setStatusCode(404)->setMessage("Cập nhật thông tin thất bại.")->setData([])->setErrors([
            [__("Có lỗi xảy ra.")]
        ])->result();
    }

    public function authy2Factor($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "sub_user_id" => "required",
            ],
            [
                "sub_user_id.required" => __("Vui lòng nhập user_id"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["sub_user_id"];
        $objSubUser = SubUser::where('id', $intUserId)->first();
        if (!$objSubUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        if ($objSubUser->authy_2factor) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản của bạn đã được kích hoạt 2 lớp, vui lòng không kích hoạt lại.")]
            ])->result();
        }

        $issuer = "MOD " . config('app.name');
        $accountName = $objSubUser->email;
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
                "sub_user_id" => "required",
                "secret_key" => "required",
                "code" => "required",
            ],
            [
                "sub_user_id.required" => __("Vui lòng nhập sub_user_id"),
                "secret_key.required" => __("Vui lòng nhập secret_key"),
                "code.required" => __("Vui lòng nhập code, mã xác thực từ ứng dụng"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intUserId = $arrParams["sub_user_id"];
        $strSecretKey = $arrParams["secret_key"];
        $strCode = $arrParams["code"];
        $objSubUser = SubUser::where('id', $intUserId)->first();
        if (!$objSubUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        if ($objSubUser->authy_2factor) {
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
        $objSubUser->authy_2factor = 1;
        $objSubUser->authy_2factor_secret_key = $strSecretKey;
        if (!$objSubUser->save()) {
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
                "sub_user_id" => "required",
                "password" => "required|max:50|min:6",
                "code" => "required",
            ],
            [
                "sub_user_id.required" => __("Vui lòng nhập user_id"),
                "password.required" => __("Vui lòng nhập mật khẩu mới"),
                "code.required" => __("Vui lòng nhập code, mã xác thực từ ứng dụng"),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strPassword = trim($arrParams["password"]);
        $strCode = $arrParams["code"];
        $intUserId = $arrParams["sub_user_id"];

        $objSubUser = SubUser::where(["id" => $intUserId])->first();
        if (empty($objSubUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }


        if (!Hash::check($strPassword, $objSubUser->password)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mật khẩu không hợp lệ.")]
            ])->result();
        }


        $googleAuth = new GoogleAuthenticator();
        $result = $googleAuth->authenticate($objSubUser->authy_2factor_secret_key, $strCode);
        if (!$result) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mã bảo mật không đúng.")]
            ])->result();
        }
        $objSubUser->authy_2factor = 0;
        $objSubUser->authy_2factor_secret_key = "";
        if (!$objSubUser->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ghi nhận dữ liệu thất bại, vui lòng kiểm tra lại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Huỷ xác thực bảo mật 2 lớp thành công."))->setData([])->result();
    }


    public function getBalance($arrParams = [])
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
        $objUserBalance = UserBalance::where(["user_id" => $intUserId])->first();

        $objUserFees = UserBalance::where(["user_id" => $intUserId])->get();
        $arrUserFees = [];
        foreach ($objUserFees as $objUserFee) {
            $strNote = "$objUserFee->name $objUserFee->fee" . (in_array($objUserFee->type_id, [3, 4]) ? "%" : "đ") . ($objUserFee->min_fee > 0 ? " phí tối thiểu: $objUserFee->min_fee" . "đ" : "");
            $arrUserFees[] = [
                "name" => $objUserFee->name,
                "fee" => $objUserFee->fee,
                "type_id" => $objUserFee->type_id,
                "min_fee" => $objUserFee->min_fee,
                "note" => $strNote
            ];
        }
        if ($objUserBalance) {
            return $this->setStatusCode(0)->setMessage(__("Lấy dữ liệu thành công."))->setData([
                "user_balance" => $objUserBalance,
                "user_fees" => $arrUserFees
            ])->result();
        }
        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Lấy dữ liệu thất bại")]
        ])->result();
    }
}