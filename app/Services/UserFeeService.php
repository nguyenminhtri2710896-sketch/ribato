<?php

namespace App\Services;

use App\Models\UserFee;
use Illuminate\Support\Facades\Validator;

class UserFeeService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserFee())->getFillable();
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Đang bảo trì'
        ],
        2 => [
            'name' => 'Hoạt động'
        ]
    ];

    //1: phí cố định in, 2: phí cố định out, 3: phí % in, 4: phí % out
    public static $arrTypeId = [
        1 => [
            'name' => 'Phí cố định in',
        ],
        2 => [
            'name' => 'Phí cố định out'
        ],
        3 => [
            'name' => 'Phí % in'
        ],
        4 => [
            'name' => 'Phí % out'
        ]
    ];


    public function getList($arrParams = [])
    {

        $intPage = $arrParams["page"] ?? 1;
        $intLimit = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objUserFees = UserFee::select();
        $objUserFees = $this->getListBuilder($objUserFees, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objUserFees;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUserFees = $objUserFees->orderBy("id", "DESC");
        }
        $objUserFees = $objUserFees->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'user_fees' => $objUserFees,
            'records_total' => $intTotal,
            'page' => (int) $intPage,
            'status' => self::$arrStatusId,
            'type' => self::$arrTypeId,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }



    public function getDetail($arrParams = [])
    {

        $objUserFee = UserFee::select(\DB::raw('*'));
        $objUserFee = $this->getListBuilder($objUserFee, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objUserFee = $objUserFee->first();
        if (empty($objUserFee)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['user_fee' => $objUserFee])->result();
    }
    public function update($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "fee" => "required",
                "min_fee" => "required",
                "type_id" => "required",
                "id" => "required",
            ],
            [

                "fee.required" => __("Vui lòng nhập phí."),
                "min_fee.required" => __("Vui lòng nhập phí tối thiểu."),
                "type_id.required" => __("Vui lòng chọn loại."),
                "id.required" => __("Vui lòng nhập id."),
            ]
        );


        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intFee = $arrParams["fee"];
        $intMinFee = $arrParams["min_fee"];
        $intTypeId = $arrParams["type_id"];
        $intId = $arrParams["id"];

        if (empty(self::$arrTypeId[$intTypeId])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Loại phí không hợp lệ.")]
            ])->result();
        }

        $strFeeName = self::$arrTypeId[$intTypeId]["name"];
        $objUserFee = UserFee::where('id', $intId)->first();
        if (empty($objUserFee)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy loại phí này.")]
            ])->result();
        }

        $objUserFee->name = $strFeeName;
        $objUserFee->fee = $intFee;
        $objUserFee->min_fee = $intMinFee;
        $objUserFee->type_id = $intTypeId;
        if (!$objUserFee->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Cập nhật công."))->setData(["user_fee" => $objUserFee])->result();
    }
}
