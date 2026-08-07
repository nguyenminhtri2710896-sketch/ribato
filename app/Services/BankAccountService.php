<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Trash;
use Illuminate\Support\Facades\Validator;

class BankAccountService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new BankAccount())->getFillable();
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Đang bảo trì'
        ],
        2 => [
            'name' => 'Hoạt động'
        ],
        3 => [
            'name' => 'Tạm dừng'
        ]
    ];

    public function getList($arrParams = [])
    {
        $this->arrFillable = array_merge($this->arrFillable, (new Bank())->getFillable());

        $intPage = $arrParams["page"] ?? 1;
        $intLimit = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objBankAccounts = BankAccount::select(\DB::raw('bank_accounts.*,banks.short_name as bank_short_name'))->leftJoin('banks', 'banks.id', 'bank_accounts.bank_id');
        $objBankAccounts = $this->getListBuilder($objBankAccounts, $arrParams, $this->arrFillable);

        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objBankAccounts;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objBankAccounts = $objBankAccounts->orderBy("bank_accounts.id", "DESC");
        }
        $objBankAccounts = $objBankAccounts->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'bank_accounts' => $objBankAccounts,
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

        $objBankAccounts = $arrResult["data"]["bank_accounts"];
        $arrData = [];
        foreach ($objBankAccounts as $objBankAccount) {
            $arrData[] = [
                "id" => $objBankAccount->id,
                "text" => $objBankAccount->bank_account_name . " " . $objBankAccount->bank_account_number,
            ];
        }
        return ["results" => $arrData, "pagination" => ["more" => $arrResult["data"]["records_total"] >= ($intLimit * $intPage) ? true : false], 'limit' => $intLimit];
    }

    public function getDetail($arrParams = [])
    {

        $objBankAccount = BankAccount::select();
        $objBankAccount = $this->getListBuilder($objBankAccount, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objBankAccount = $objBankAccount->first();
        if (empty($objBankAccount)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        $objBank = Bank::where('id', $objBankAccount->bank_id)->first();
        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['bank_account' => $objBankAccount, 'bank' => $objBank])->result();
    }



    public function add($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'bank_account_name' => "required",
                'bank_account_number' => "required",
                'bank_id' => "required",
            ],
            [

                "bank_account_name.required" => __("Vui lòng nhập tên chủ khoản."),
                "bank_account_number.required" => __("Vui lòng nhập số tài khoản."),
                "bank_id.required" => __("Vui lòng chọn ngân hàng."),
            ]
        );


        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strBankAccountNumber = $arrParams["bank_account_number"];
        $intBankId = $arrParams["bank_id"];
        $arrParams["status_id"] = $intStatusId = $arrParams["status_id"] ?? 1;
        $arrParams["sorting"] = $intSorting = $arrParams["sorting"] ?? 0;


        if (empty(self::$arrStatusId[$intStatusId])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Trạng thái không hợp lệ.")]
            ])->result();
        }

        $objBank = Bank::where('id', $intBankId)->where('status_id', 2)->first();
        if (!$objBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ngân hàng không tồn tại hoặc chưa được kích hoạt.")]
            ])->result();
        }

        /**
         * Kiểm tra đã tồn tại trong hệ thống chưa
         */
        $objBankAccount = BankAccount::where('bank_account_number', strtolower($strBankAccountNumber))->where('bank_id', $objBank->id)->first();
        if ($objBankAccount) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tài khoản đã tồn tại trên hệ thống.")]
            ])->result();
        }

        $arrInsert = self::getFilterParams($arrParams, (new BankAccount())->getFillable());
        $objBankAccount = BankAccount::create($arrInsert);

        if (empty($objBankAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thêm thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm thành công."))->setData(["bank_account" => $objBankAccount])->result();
    }

    public function update($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'id' => "required",
                'bank_account_name' => "required",
                'bank_account_number' => "required",
                'bank_id' => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập id."),
                "bank_account_name.required" => __("Vui lòng nhập tên chủ khoản."),
                "bank_account_number.required" => __("Vui lòng nhập số tài khoản."),
                "bank_id.required" => __("Vui lòng chọn ngân hàng."),
            ]
        );


        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intId = $arrParams["id"];
        $intBankId = $arrParams["bank_id"];
        $arrParams["status_id"] = $intStatusId = $arrParams["status_id"] ?? 1;
        $arrParams["sorting"] = $intSorting = $arrParams["sorting"] ?? 0;
        $strBankAccountNumber = $arrParams["bank_account_number"];


        if (empty(self::$arrStatusId[$intStatusId])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Trạng thái không hợp lệ.")]
            ])->result();
        }

        $objBankAccount = BankAccount::where('id', $intId)->first();
        if (!$objBankAccount) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tài khoản ngân hàng không tồn tại.")]
            ])->result();
        }


        $objBank = Bank::where('id', $intBankId)->where('status_id', 2)->first();
        if (!$objBank) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Ngân hàng không tồn tại hoặc chưa được kích hoạt.")]
            ])->result();
        }


        $objBankAccountCheckExist = BankAccount::where('bank_account_number', strtolower($strBankAccountNumber))->where('id', '!=', $intId)->first();
        if ($objBankAccountCheckExist) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Số tài khoản đã tồn tại trên hệ thống.")]
            ])->result();
        }

        $arrUpdate = self::getFilterParams($arrParams, (new BankAccount())->getFillable());
        foreach ($arrUpdate as $key => $value) {
            $objBankAccount->{$key} = $value;
        }

        if (!$objBankAccount->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("cập nhật thành công."))->setData(["bank_account" => $objBankAccount])->result();
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
        $objBankAccount = BankAccount::where('id', $intId)->first();
        if (empty($objBankAccount)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        if (!$objBankAccount->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá thất bại.")]
            ])->result();
        }

        Trash::create([
            'table' => 'bank_accounts',
            'data' => json_encode($objBankAccount->toArray())
        ]);

        return $this->setStatusCode(0)->setMessage(__("Xoá thành công."))->setData([])->result();
    }
}