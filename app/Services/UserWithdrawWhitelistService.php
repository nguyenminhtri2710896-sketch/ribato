<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\User;
use App\Models\UserWithdrawWhitelist;
use Illuminate\Support\Facades\Validator;

class UserWithdrawWhitelistService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserWithdrawWhitelist())->getFillable();
    }

    public function getList($arrParams = [])
    {
        $intPage   = $arrParams["page"] ?? 1;
        $intLimit  = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objWhitelists = UserWithdrawWhitelist::select(\DB::raw('user_withdraw_whitelists.*, banks.logo as bank_logo, banks.name as bank_name, banks.short_code as bank_short_code, users.email as user_email, users.fullname as user_fullname'))
            ->join('users', 'users.id', 'user_withdraw_whitelists.user_id')
            ->join('banks', 'banks.id', 'user_withdraw_whitelists.bank_id');

        $objWhitelists = $this->getListBuilder($objWhitelists, $arrParams, $this->arrFillable);

        $intTotal = $objWhitelists->count();
        if (empty($arrParams["sort"])) {
            $objWhitelists = $objWhitelists->orderBy("user_withdraw_whitelists.id", "DESC");
        }
        $objWhitelists = $objWhitelists->offset($intOffset)->limit($intLimit)->get();

        return $this->setStatusCode(0)->setData([
            'user_withdraw_whitelists' => $objWhitelists,
            'records_total' => $intTotal,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }

    public function getDetail($arrParams = [])
    {
        $objWhitelist = UserWithdrawWhitelist::select();
        $objWhitelist = $this->getListBuilder($objWhitelist, $arrParams, $this->arrFillable)->first();
        if (empty($objWhitelist)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData([
            'user_withdraw_whitelist' => $objWhitelist,
            'user' => User::where('id', $objWhitelist->user_id)->first(),
            'bank' => Bank::where('id', $objWhitelist->bank_id)->first(),
        ])->result();
    }

    private function normalizeParams($arrParams)
    {
        $arrParams["bank_account_number"] = trim($arrParams["bank_account_number"] ?? "");
        $arrParams["daily_limit"] = preg_replace('/[^0-9]/', '', $arrParams["daily_limit"] ?? "");
        return $arrParams;
    }

    private function validateParams($arrParams)
    {
        $validated = Validator::make(
            $arrParams,
            [
                'user_id' => 'required',
                'bank_id' => 'required',
                'bank_account_number' => 'required',
                'daily_limit' => 'required|integer|min:1',
            ],
            [
                "user_id.required" => __("Vui lòng chọn người dùng."),
                "bank_id.required" => __("Vui lòng chọn ngân hàng."),
                "bank_account_number.required" => __("Vui lòng nhập số tài khoản."),
                "daily_limit.required" => __("Vui lòng nhập số tiền rút tối đa trong ngày."),
                "daily_limit.integer" => __("Số tiền rút tối đa trong ngày không hợp lệ."),
                "daily_limit.min" => __("Số tiền rút tối đa trong ngày phải lớn hơn 0."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        if (!User::where('id', $arrParams["user_id"])->exists()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại, vui lòng kiểm tra lại.")]
            ])->result();
        }
        if (!Bank::where('id', $arrParams["bank_id"])->exists()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ngân hàng không hợp lệ.")]
            ])->result();
        }
        return null;
    }

    private function isDuplicate($arrParams, $intExceptId = 0)
    {
        return UserWithdrawWhitelist::where('user_id', $arrParams["user_id"])
            ->where('bank_id', $arrParams["bank_id"])
            ->where('bank_account_number', $arrParams["bank_account_number"])
            ->where('id', '!=', $intExceptId)
            ->exists();
    }

    public function add($arrParams = [])
    {
        $arrParams = $this->normalizeParams($arrParams);
        if ($arrError = $this->validateParams($arrParams)) {
            return $arrError;
        }
        if ($this->isDuplicate($arrParams)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tài khoản đã được thêm vào whitelist của người dùng này rồi.")]
            ])->result();
        }

        $objWhitelist = UserWithdrawWhitelist::create(self::getFilterParams($arrParams, (new UserWithdrawWhitelist())->getFillable()));
        if (empty($objWhitelist)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thêm thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm thành công."))->setData(["user_withdraw_whitelist" => $objWhitelist])->result();
    }

    public function update($arrParams = [])
    {
        $arrParams = $this->normalizeParams($arrParams);
        if (empty($arrParams["id"])) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors([
                [__("Vui lòng nhập id.")]
            ])->result();
        }
        $objWhitelist = UserWithdrawWhitelist::where('id', $arrParams["id"])->first();
        if (empty($objWhitelist)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }
        if ($arrError = $this->validateParams($arrParams)) {
            return $arrError;
        }
        if ($this->isDuplicate($arrParams, $objWhitelist->id)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tài khoản đã được thêm vào whitelist của người dùng này rồi.")]
            ])->result();
        }

        $arrUpdate = self::getFilterParams($arrParams, (new UserWithdrawWhitelist())->getFillable());
        unset($arrUpdate["id"]);
        foreach ($arrUpdate as $key => $value) {
            $objWhitelist->{$key} = $value;
        }
        if (!$objWhitelist->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData(["user_withdraw_whitelist" => $objWhitelist])->result();
    }

    public function delete($arrParams = [])
    {
        if (empty($arrParams["id"])) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors([
                [__("Vui lòng nhập id.")]
            ])->result();
        }
        $objWhitelist = UserWithdrawWhitelist::where('id', $arrParams["id"])->first();
        if (empty($objWhitelist)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }
        if (!$objWhitelist->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Xoá thành công."))->setData([])->result();
    }
}
