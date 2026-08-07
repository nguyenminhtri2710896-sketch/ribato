<?php

namespace App\Services;

use App\Models\Trash;
use App\Models\UserToken;
use Illuminate\Support\Facades\Validator;

class UserTokenService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserToken())->getFillable();
    }




    public function getPersonalTokensList($arrParams = [])
    {
        $intPage   = $arrParams["page"] ?? 1;
        $intLimit  = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objUserTokens = UserToken::where('user_id', $arrParams['user_id']);
        
        $intTotal = $objUserTokens->count();
        $objUserTokens = $objUserTokens->orderBy("id", "DESC")->offset($intOffset)->limit($intLimit)->get();

        return $this->setStatusCode(0)->setData([
            'user_tokens' => $objUserTokens,
            'records_total' => $intTotal,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }

    public function addPersonalToken($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "name" => "required|max:255",
                "permission" => "required|in:read,write",
                "user_id" => "required",
            ],
            [
                "name.required" => __("Vui lòng nhập tên token."),
                "permission.required" => __("Vui lòng chọn quyền."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $objUserToken = UserToken::create([
            "user_id" => $arrParams["user_id"],
            "name" => $arrParams["name"],
            "token" => md5(time() . rand(10000000, 99999999)),
            "permission" => $arrParams["permission"],
            "expired_at" => date('Y-m-d H:i:s', time() + 315360000)
        ]);

        if (empty($objUserToken)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thêm token thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm token thành công."))->setData(["user_token" => $objUserToken])->result();
    }

    public function deletePersonalToken($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
                "user_id" => "required",
            ],
            [
                "id.required" => __("Vui lòng nhập mã token."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intId = $arrParams["id"];
        $intUserId = $arrParams["user_id"];
        $objUserToken = UserToken::where('id', $intId)->where('user_id', $intUserId)->first();
        if (empty($objUserToken)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy token này.")]
            ])->result();
        }

        if (!$objUserToken->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá token thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xoá token thành công."))->setData([])->result();
    }




    public function getDetail($arrParams = [])
    {

        $objUserToken = UserToken::select(\DB::raw('*'));
        $objUserToken = $this->getListBuilder($objUserToken, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objUserToken = $objUserToken->first();
        if (empty($objUserToken)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['user_token' => $objUserToken])->result();
    }

    public function updatePublicKey($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "public_key" => "required",
                "plaintext" => "required",
                "signiture" => "required",
                "user_id" => "required",
            ],
            [

                "name.required" => __("Vui lòng nhập public key."),
                "plaintext.required" => __("Vui lòng nhập nội dung."),
                "signiture.required" => __("Vui lòng nhập chữ ký."),
                "user_id.required" => __("Vui lòng nhập user_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $strPublicKey = $arrParams["public_key"];
        $strPlaintext = $arrParams["plaintext"];
        $strSigniture = $arrParams["signiture"];
        $intUserId    = $arrParams["user_id"];
        $objUserToken = UserToken::where('user_id', $intUserId)->first();
        if (!$objUserToken) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy thông tin token.')]
            ])->result();
        }

        $resultVerifySign = $this->verifySign($strPlaintext, $strSigniture, $objUserToken->token, $strPublicKey);
        if ($resultVerifySign == false) {
            return $this->setStatusCode(404)->setMessage('')->setData([
                $strPlaintext,
                $strSigniture,
                $objUserToken->token,
                $strPublicKey
            ])->setErrors([
                        [__('Xác thực chữ ký không hợp lệ, vui lòng kiểm tra lại.')]
                    ])->result();
        }

        $objUserToken->public_key = $strPublicKey;
        if (!$objUserToken->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật public key thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData(["user_token" => $objUserToken])->result();
    }

    public function updateWebhookUrl($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "webhook_url" => "required",
                "user_id" => "required",
            ],
            [

                "webhook_url.required" => __("Vui lòng nhập chữ ký."),
                "user_id.required" => __("Vui lòng nhập user_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $strWebhookUrl = $arrParams["webhook_url"];
        $strWebhookPayoutUrl = $arrParams["webhook_payout_url"];
        $intUserId     = $arrParams["user_id"];
        $objUserToken  = UserToken::where('user_id', $intUserId)->first();
        if (!$objUserToken) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy thông tin token.')]
            ])->result();
        }

        $objUserToken->webhook_url = $strWebhookUrl;
        $objUserToken->webhook_payout_url = $strWebhookPayoutUrl;
        if (!$objUserToken->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật webhook thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData(["user_token" => $objUserToken])->result();
    }

    private function verifySign($strPlaintext, $strSigniture, $strSecretKey, $strPublicKey)
    {

        $verified = openssl_verify($strPlaintext . $strSecretKey, base64_decode($strSigniture), $strPublicKey, OPENSSL_ALGO_SHA256);
        if ($verified === 1) {
            return true;
        }
        return false;
    }

}