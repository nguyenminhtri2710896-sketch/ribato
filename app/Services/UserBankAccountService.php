<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Trash;
use App\Models\User;
use App\Models\UserBankAccount;
use Illuminate\Support\Facades\Validator;

class UserBankAccountService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserBankAccount())->getFillable();
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Tạm đóng'
        ],
        2 => [
            'name' => 'Hoạt động'
        ]
    ];



    public function getList($arrParams = [])
    {

        $this->arrFillable = array_merge($this->arrFillable, (new Bank())->getFillable());
        $this->arrFillable = array_merge($this->arrFillable, (new BankAccount())->getFillable());

        $intPage   = $arrParams["page"] ?? 1;
        $intLimit  = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objUserBanks = UserBankAccount::select(\DB::raw('user_bank_accounts.*,banks.logo as bank_logo, banks.name as bank_name, banks.short_code as bank_short_code, users.email as user_email, users.fullname as user_fullname,bank_accounts.bank_account_name as bank_account_name,bank_accounts.bank_account_number as bank_account_number'))->join('users', 'users.id', 'user_bank_accounts.user_id')
            ->join('bank_accounts', 'bank_accounts.id', 'user_bank_accounts.bank_account_id')
            ->join('banks', 'banks.id', 'bank_accounts.bank_id');
           
        $objUserBanks = $this->getListBuilder($objUserBanks, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objUserBanks;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUserBanks = $objUserBanks->orderBy("user_bank_accounts.id", "DESC");
        }
        $objUserBanks = $objUserBanks->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'user_bank_accounts' => $objUserBanks,
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
        $intPage  = $arrResult["data"]["page"] ?? 1;

        $objUserBankAccounts = $arrResult["data"]["user_bank_accounts"];
        $arrData             = [];
        foreach ($objUserBankAccounts as $objUserBankAccount) {
            $arrData[] = [
                "id" => $objUserBankAccount->id,
                "text" => "($objUserBankAccount->bank_short_code) ".$objUserBankAccount->bank_account_name." - " . $objUserBankAccount->bank_account_number,
            ];
        }
        return ["results" => $arrData, "pagination" => ["more" => $arrResult["data"]["records_total"] >= ($intLimit * $intPage) ? true : false], 'limit' => $intLimit];
    }
    public function getDetail($arrParams = [])
    {

        $objUserBankAccount = UserBankAccount::select();
        $objUserBankAccount = $this->getListBuilder($objUserBankAccount, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objUserBankAccount = $objUserBankAccount->first();
        if (empty($objUserBankAccount)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }
        $objUser        = User::where('id', $objUserBankAccount->user_id)->first();
        $objBankAccount = BankAccount::where('id', $objUserBankAccount->bank_account_id)->first();
        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData([
            'user_bank_account' => $objUserBankAccount,
            'user' => $objUser,
            'bank_account' => $objBankAccount
        ])->result();
    }

    public function add($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                'user_id',
                'bank_account_id',
            ],
            [

                "user_id.required" => __("Vui lòng nhập user_id."),
                "bank_account_id.required" => __("Vui lòng nhập bank_account_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intStatusId            = $arrParams["status_id"] ?? 1;
        $arrParams["status_id"] = $intStatusId;

        if (empty(self::$arrStatusId[$intStatusId])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Trạng thái không hợp lệ.")]
            ])->result();
        }


        $intUserId = $arrParams["user_id"] ?? 0;
        $objUser   = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại, vui lòng kiểm tra lại.")]
            ])->result();
        }

        $intBankAccountId = $arrParams["bank_account_id"] ?? 0;
        $objBankAccount   = BankAccount::where('id', $intBankAccountId)->first();
        if (!$objBankAccount) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản ngân hàng này không tồn tại.")]
            ])->result();
        }
        /**
         * Kiểm tra tồn tại 
         */
        $objBankAccount = UserBankAccount::where('user_id', $intUserId)->where('bank_account_id', $intBankAccountId)->first();
        if ($objBankAccount) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản ngân hàng đã được cấu hình cho người dùng này rồi.")]
            ])->result();
        }


        $arrInsert          = self::getFilterParams($arrParams, (new UserBankAccount())->getFillable());
        $objUserBankAccount = UserBankAccount::create($arrInsert);

        if (empty($objUserBankAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thêm thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm thành công."))->setData(["user_bank_account" => $objUserBankAccount])->result();
    }

    public function update($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'id',
                'user_id',
                'bank_account_id',
            ],
            [

                "id.required" => __("Vui lòng nhập id."),
                "user_id.required" => __("Vui lòng nhập user_id."),
                "bank_account_id.required" => __("Vui lòng nhập bank_account_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intStatusId            = $arrParams["status_id"] ?? 1;
        $arrParams["status_id"] = $intStatusId;
        $intId                  = $arrParams["id"];


        if (empty(self::$arrStatusId[$intStatusId])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Trạng thái không hợp lệ.")]
            ])->result();
        }

        $objUserBankAccount = UserBankAccount::where('id', $intId)->first();
        if (empty($objUserBankAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        $intUserId = $arrParams["user_id"] ?? 0;
        $objUser   = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại, vui lòng kiểm tra lại.")]
            ])->result();
        }

        $intBankAccountId = $arrParams["bank_account_id"] ?? 0;
        $objBankAccount   = BankAccount::where('id', $intBankAccountId)->first();
        if (!$objBankAccount) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản ngân hàng này không tồn tại.")]
            ])->result();
        }
        /**
         * Kiểm tra tồn tại 
         */
        $objBankAccount = UserBankAccount::where('user_id', $intUserId)->where('bank_account_id', $intBankAccountId)->where('id', '!=', $objUserBankAccount->id)->first();
        if ($objBankAccount) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản ngân hàng đã được cấu hình cho người dùng này rồi.")]
            ])->result();
        }

        /**
         * Lấy những params cho phép update 
         */
        $arrUpdate = self::getFilterParams($arrParams, (new UserBankAccount())->getFillable());
        foreach ($arrUpdate as $key => $value) {
            $objUserBankAccount->{$key} = $value;
        }

        if (!$objUserBankAccount->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData(["user_bank_account" => $objBankAccount])->result();
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
        $intId              = $arrParams["id"];
        $objUserBankAccount = UserBankAccount::where('id', $intId)->first();
        if (empty($objUserBankAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        $intUserId = $arrParams["user_id"] ?? 0;
        if (!empty($intUserId)) {
            if ($intUserId != $objUserBankAccount->user_id) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Bạn không có quyền cập nhật.")]
                ])->result();
            }

            $objUser = User::where('id', $intUserId)->first();
            if (!$objUser) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Người dùng không tồn tại, vui lòng kiểm tra lại.")]
                ])->result();
            }
        }

        if (!$objUserBankAccount->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá thất bại.")]
            ])->result();
        }

        Trash::create([
            'table' => 'user_bank_accounts',
            'data' => json_encode($objUserBankAccount->toArray())
        ]);

        return $this->setStatusCode(0)->setMessage(__("Xoá thành công."))->setData([])->result();
    }
}