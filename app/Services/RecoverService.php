<?php

namespace App\Services;

use App\Models\User;
use App\Utilities\General;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RecoverService extends AbstractService
{
    public function __construct()
    {
    }

    public function initiate($arrParams = [])
    {

        $validated =    Validator::make(
            $arrParams,
            [
                "email" => "required",
            ],
            [
                "email.required" =>  __("Vui lòng nhập thông tin tài khoản"),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strEmail = $arrParams["email"];
        $objUser = User::where('email', $strEmail)->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy tài khoản.")]
            ])->result();
        }

        if (!$objUser->actived) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản chưa được kích hoạt.")]
            ])->result();
        }
        // generate code để khôi phục mật khẩu
        $intCode = rand(1000000, 9999999);
        $strKey =  md5("initiate_" . $intCode . $strEmail);



        Cache::tags(['initiate'])->put($strKey, ['code' => $intCode, 'email' => $strEmail], 600); // lifetime 10p
        return $this->setStatusCode(0)->setMessage(__("Yêu cầu lấy lại mật khẩu thành công."))->setData([
            "initiate_key" =>   $strKey,
            // "code" =>   $intCode
        ])->result();
    }

    public function validateRecoverCode($arrParams = [])
    {

        $validated =    Validator::make(
            $arrParams,
            [
                "initiate_key" => "required",
                "code" => "required",
            ],
            [
                "initiate_key.required" =>  __("Vui lòng nhập initiate_key."),
                "code.required" =>  __("Vui lòng nhập code."),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strInitiateKey = $arrParams["initiate_key"];
        $intCode = $arrParams["code"];
        if (!Cache::tags(['initiate'])->has($strInitiateKey)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Đường dẫn khôi phục mật khẩu không còn sử dụng được.")]
            ])->result();
        }

        $arrResult = Cache::tags(['initiate'])->get($strInitiateKey);
        $strEmail = $arrResult["email"];
        $intCodeCache = $arrResult["code"];
        if ($intCodeCache != $intCode) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors([
                [__("Mã xác nhận không hợp lệ.")]
            ])->result();
        }

        $objUser = User::where('email', $strEmail)->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy tài khoản.")]
            ])->result();
        }

        if (!$objUser->actived) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản chưa được kích hoạt.")]
            ])->result();
        }

        // generate code để khôi phục mật khẩu
        $strKey = md5("validate_" . $intCode . $strEmail);
        Cache::tags(['validate'])->put($strKey, ['id' => $objUser->id,  'email' => $strEmail], 600); // lifetime 10p
        // remove keycache cũ
        Cache::tags(['initiate'])->forget($strInitiateKey);
        return $this->setStatusCode(0)->setMessage(__("Thành công."))->setData(["recover_key" => $strKey])->result();
    }

    public function validateRecoverKey($arrParams = [])
    {

        $validated =    Validator::make(
            $arrParams,
            [
                "recover_key" => "required",
            ],
            [
                "recover_key.required" =>  __("Vui lòng nhập recover_key."),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strRecoverKey = trim($arrParams["recover_key"]);
        if (!Cache::tags(['validate'])->has($strRecoverKey)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Đường dẫn khôi phục mật khẩu không còn sử dụng được.")]
            ])->result();
        }
        $arrResult = Cache::tags(['validate'])->get($strRecoverKey);

        if (!empty($arrResult["id"])) {
            $intUserId = $arrResult["id"];
            return $this->setStatusCode(0)->setMessage(__("Khôi phục mật khẩu cấp 2 thành công."))->setData(["user_id" => $intUserId, "recover_key" => $strRecoverKey])->result();
        }
        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
            [__("Thay đổi mật khẩu thất bại")]
        ])->result();
    }
}
