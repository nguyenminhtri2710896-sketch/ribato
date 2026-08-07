<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\Trash;
use Illuminate\Support\Facades\Validator;

class BankService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new Bank())->getFillable();
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

        $intPage   = $arrParams["page"] ?? 1;
        $intLimit  = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objBanks = Bank::select();
        $objBanks = $this->getListBuilder($objBanks, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objBanks;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objBanks = $objBanks->orderBy("id", "DESC");
        }
        $objBanks = $objBanks->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'banks' => $objBanks,
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

        $objBanks = $arrResult["data"]["banks"];
        $arrData  = [];
        foreach ($objBanks as $objBank) {
            $arrData[] = [
                "id" => $objBank->id,
                "text" => $objBank->name,
            ];
        }
        return ["results" => $arrData, "pagination" => ["more" => $arrResult["data"]["records_total"] >= ($intLimit * $intPage) ? true : false], 'limit' => $intLimit];
    }

    public function getDetail($arrParams = [])
    {

        $objBank = Bank::select();
        $objBank = $this->getListBuilder($objBank, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objBank = $objBank->first();
        if (empty($objBank)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['bank' => $objBank])->result();
    }

    public function add($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'napas_code',
                'name',
                'short_name',
                'short_code'
            ],
            [

                "napas_code.required" => __("Vui lòng nhập image."),
                "name.required" => __("Vui lòng nhập tiêu đề."),
                "short_name.required" => __("Vui lòng nhập image."),
                "short_code.required" => __("Vui lòng nhập tiêu đề."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $intStatusId = $arrParams["status_id"] ?? 1;
        if (empty(self::$arrStatusId[$intStatusId])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Trạng thái không hợp lệ.")]
            ])->result();
        }

        $arrInsert = self::getFilterParams($arrParams, (new Bank())->getFillable());
        $objBank   = Bank::create($arrInsert);

        if (empty($objBank)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thêm thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm thành công."))->setData(["bank" => $objBank])->result();
    }

    public function update($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'id' => "required",
                'napas_code' => "required",
                'name' => "required",
                'short_name' => "required",
                'short_code' => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập id."),
                "napas_code.required" => __("Vui lòng nhập napas_code."),
                "name.required" => __("Vui lòng nhập tên."),
                "short_name.required" => __("Vui lòng nhập short_name."),
                "short_code.required" => __("Vui lòng nhập tiêu đề short_code."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intId       = $arrParams["id"];
        $intStatusId = $arrParams["status_id"] ?? 1;

        if (empty(self::$arrStatusId[$intStatusId])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Trạng thái không hợp lệ.")]
            ])->result();
        }

        $objBank = Bank::where('id', $intId)->first();
        if (empty($objBank)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }
        /**
         * Lấy những params cho phép update 
         */
        $arrUpdate = self::getFilterParams($arrParams, (new Bank())->getFillable());
        foreach ($arrUpdate as $value) {
            $objBank->{$value} = $arrParams[$value];
        }

        if (!$objBank->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData(["bank" => $objBank])->result();
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
        $intId   = $arrParams["id"];
        $objBank = Bank::where('id', $intId)->first();
        if (empty($objBank)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        if (!$objBank->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá thất bại.")]
            ])->result();
        }

        Trash::create([
            'table' => 'banks',
            'data' => json_encode($objBank->toArray())
        ]);

        return $this->setStatusCode(0)->setMessage(__("Xoá thành công."))->setData([])->result();
    }
}