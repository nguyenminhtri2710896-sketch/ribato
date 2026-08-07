<?php

namespace App\Services;

use App\Models\UserBalances;
use App\Models\User;
use App\Models\UserBalance;
use App\Models\UserGroup;
use App\Models\UserSigninLog;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Dolondro\GoogleAuthenticator\GoogleAuthenticator;

class AuthService extends AbstractService
{
    public function __construct()
    {
    }


    public function checkEmailLoginInfo($arrParams = [])
    {
        $validated = Validator::make($arrParams, [
            "email" => "required|email",
        ], [
            "email.required" => __("Vui lòng nhập địa chỉ email."),
            "email.email" => __("Địa chỉ email không hợp lệ."),
        ]);

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strEmail = $arrParams["email"];
        $objUser  = User::where('email', $strEmail)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Địa chỉ email hông tồn tại trên hệ thống.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Đăng nhập thành công."))->setData([
            "profile" => [
                'fullname' => $objUser->fullname,
                'authy_2factor' => $objUser->authy_2factor,
                'language' => $objUser->language,
                'image_avatar' => $objUser->image_avatar,
                'first_name' => $objUser->first_name,
                'last_name' => $objUser->last_name
            ]
        ])->result();
    }

    public function signIn($arrParams = [])
    {
        try {
            $validated = Validator::make($arrParams, [
                "email" => "required|email",
                "password" => "required|max:50|min:6",
            ], [
                "email.required" => __("Vui lòng nhập địa chỉ email."),
                "email.email" => __("Địa chỉ email không hợp lệ."),
                "password.required" => __("Vui lòng nhập mật khẩu."),
                "password.max" => __("Mật khẩu không được lớn hơn :max ký tự."),
                "password.min" => __("Mật khẩu không được nhỏ hơn :min ký tự."),
            ]);

            if ($validated->errors()->messages()) {
                return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
            }

            $strEmail           = $arrParams["email"];
            $strPassword        = $arrParams["password"];
            $strAuth2FactorCode = $arrParams["auth_2factor_code"] ?? "";

            $jwtToken = null;
            if (!$jwtToken = auth()->attempt(["email" => $strEmail, "password" => $strPassword])) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Tên đăng nhập hoặc mật khẩu không đúng.")]
                ])->result();
            }

            $objUser = auth()->user();
            if ($objUser->actived == 0) {
                auth()->logout();
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Tài khoản của bạn chưa được kích hoạt, vui lòng liên hệ quản trị viên.")]
                ])->result();
            }

            if ($objUser->locked == 1) {
                auth()->logout();
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Tài khoản của bạn đã bị khoá, vui lòng liên hệ quản trị viên.")]
                ])->result();
            }

            if (empty($strAuth2FactorCode)) {
                if ($objUser->authy_2factor == 1) {
                    return $this->setStatusCode(101)->setMessage("")->setData([])->setErrors([
                        [__("Vui lòng nhập mã bảo mật.")]
                    ])->result();
                }
            } else {
                // nếu có mã rồi thì auth
                if ($objUser->authy_2factor == 1) {
                    $googleAuth = new GoogleAuthenticator();
                    $result     = $googleAuth->authenticate($objUser->authy_2factor_secret_key, $strAuth2FactorCode);
                    if (!$result) {
                        return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                            [__("Mã bảo mật không đúng.")]
                        ])->result();
                    }
                }
            }
            /**
             * Ghi log login lại
             */
            UserSigninLog::create([
                "user_agent" => request()->userAgent(),
                "user_ip" => request()->ip(),
                "user_id" => $objUser->id,
            ]);
            return $this->setStatusCode(0)->setMessage(__("Đăng nhập thành công."))->setData(["access_token" => $jwtToken, 'token_type' => 'bearer', "user" => $objUser])->result();
        } catch (TokenExpiredException $e) {
            return $this->setStatusCode(401)->setMessage("")->setData([])->setErrors([
                [__("Token Expired.")]
            ])->result();
        } catch (TokenInvalidException $e) {
            return $this->setStatusCode(400)->setMessage("")->setData([])->setErrors([
                [__("Token Invalid.")]
            ])->result();
        } catch (JWTException $e) {
            return $this->setStatusCode(404)->setMessage($e->getMessage())->setData([])->setErrors([
                [__("JWT Exception.")]
            ])->result();
        }
    }

    public function refreshToken()
    {
        $objUser = User::where(["id" => auth()->user()->id])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản không tồn tại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Refresh token thành công."))->setData(["access_token" => auth()->refresh(), 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60, "user" => $objUser])->result();
    }

    public function checkToken()
    {
        try {
            $jwtAuth = JWTAuth::parseToken()->authenticate();

            $objBalance = UserBalance::where('user_id', $jwtAuth->id)->first();
            $objGroup   = UserGroup::where('id', $jwtAuth->group_id)->first();
            return $this->setStatusCode(0)->setData([
                'user' => [
                    'id' => $jwtAuth->id,
                    'fullname' => $jwtAuth->fullname,
                    'full_access' => $jwtAuth->full_access,
                    'email' => $jwtAuth->email,
                    'image_avatar' => $jwtAuth->image_avatar,
                    'image_cover' => $jwtAuth->image_cover,
                ],
                'user_balance' => $objBalance,
                'user_group' => $objGroup
            ])->result();
        } catch (TokenExpiredException $e) {
            return $this->setStatusCode(401)->setMessage("")->setData([])->setErrors([
                [__("Token Expired.")]
            ])->result();
        } catch (TokenInvalidException $e) {
            return $this->setStatusCode(400)->setMessage("")->setData([])->setErrors([
                [__("Token Invalid.")]
            ])->result();
        } catch (JWTException $e) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("JWT Exception.")]
            ])->result();
        } catch (Exception $e) {
            return $this->setStatusCode(502)->setMessage("")->setData([])->setErrors([
                [__("Error System (" . $e->getMessage() . ").")]
            ])->result();
        }
    }



    public function signUp($arrParams = null)
    {

        $validated = Validator::make(
            $arrParams,
            [
                "agree" => "required",
                "first_name" => "required",
                "phone" => "required|max:12|min:9",
                "last_name" => "required",
                "email" => "required|email",
                "password" => "required|max:50|confirmed|min:6",
                "password_confirmation" => "required|max:50|min:6",
            ],
            [
                "agree.required" => __("Bạn chưa đồng ý cá điều khoản và điều kiện."),
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
                "password.confirmed" => __("Mật khẩu và mật khẩu xác nhận không giống nhau."),
                "password_confirmation.required" => __("Vui lòng nhập mật khẩu xác nhận."),
                "password_confirmation.max" => __("Mật khẩu xác nhận không được lớn hơn :max ký tự."),
                "password_confirmation.min" => __("Mật khẩu xác nhận không được nhỏ hơn :min ký tự."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strFirstName = $arrParams["first_name"];
        $strPhone     = $arrParams["phone"];
        $strLastName  = $arrParams["last_name"];
        $strFullname  = $strFirstName . " " . $strLastName;
        $strEmail     = $arrParams["email"];
        $strPassword  = $arrParams["password"];

        if (User::where(["email" => $strEmail])->exists() === true) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Địa chỉ email này đã tồn tại trên hệ thống.")]
            ])->result();
        }

        if (User::where(["phone" => $strPhone])->exists() === true) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số điện thoại đã tồn tại trên hệ thống.")]
            ])->result();
        }

        \DB::beginTransaction();
        $objUser = User::create([
            "first_name" => $strFirstName,
            "last_name" => $strLastName,
            "fullname" => $strFullname,
            "group_id" => 8,
            "email" => $strEmail,
            "phone" => $strPhone,
            "password" => Hash::make($strPassword),
            "active_code" => md5(time() . $strEmail),
            "is_actived" => 1
        ]);


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
        \DB::commit();

        if ($objUser) {
            // add job send mail active account
            return $this->setStatusCode(0)->setMessage(__("Chúc mừng bạn đã đăng ký tài khoản thành công."))->setData(['user' => $objUser])->result();
        }
    }

    public function signOut()
    {
        try {
            auth()->logout();
            return $this->setStatusCode(0)->setData([])->result();
        } catch (Exception $e) {
            return $this->setStatusCode(502)->setMessage("")->setData([])->setErrors([
                [__("Error System (" . $e->getMessage() . ").")]
            ])->result();
        } catch (TokenExpiredException $e) {
            return $this->setStatusCode(401)->setMessage("")->setData([])->setErrors([
                [__("Token Expired.")]
            ])->result();
        } catch (TokenInvalidException $e) {
            return $this->setStatusCode(400)->setMessage("")->setData([])->setErrors([
                [__("Token Invalid.")]
            ])->result();
        } catch (JWTException $e) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("JWT Exception.")]
            ])->result();
        }
    }



    public function accessSignIn($arrParams = [])
    {
        try {
            $validated = Validator::make($arrParams, [
                "id" => "required",
            ], [
                "id.required" => __("Vui lòng nhập user_id."),
            ]);

            if ($validated->errors()->messages()) {
                return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
            }


            $intId           = $arrParams["id"];
            $intUserParentId = $arrParams["user_parent_id"] ?? 0;

            $objUser = User::find($intId);
            if (!$objUser) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Người dùng không tồn tại.")]
                ])->result();
            }

            if (!empty($intUserParentId)) {
                if ($objUser->user_parent_id != $intUserParentId) {
                    return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                        [__("Bạn không có quyền đăng nhập vào tài khoản này.")]
                    ])->result();
                }
            }

            $jwtToken = JWTAuth::fromUser($objUser);
            auth()->loginUsingId($intId);
            return $this->setStatusCode(0)->setMessage(__("Đăng nhập thành công."))->setData(["token" => $jwtToken, "user" => $objUser])->result();
        } catch (TokenExpiredException $e) {
            return $this->setStatusCode(401)->setMessage("")->setData([])->setErrors([
                [__("Token Expired.")]
            ])->result();
        } catch (TokenInvalidException $e) {
            return $this->setStatusCode(400)->setMessage("")->setData([])->setErrors([
                [__("Token Invalid.")]
            ])->result();
        } catch (JWTException $e) {
            return $this->setStatusCode(404)->setMessage($e->getMessage())->setData([])->setErrors([
                [__("JWT Exception.")]
            ])->result();
        }
    }
}