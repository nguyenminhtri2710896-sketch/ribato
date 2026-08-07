<?php

namespace App\Services;

use App\Models\UserReferalFee;
use Illuminate\Support\Facades\Validator;

class UserReferalFeeService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserReferalFee())->getFillable();
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
            'name' => 'Phí cố định in'
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

        $objUserReferalFees = UserReferalFee::select();
        $objUserReferalFees = $this->getListBuilder($objUserReferalFees, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objUserReferalFees;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUserReferalFees = $objUserReferalFees->orderBy("id", "DESC");
        }
        $objUserReferalFees = $objUserReferalFees->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'user_referal_fees' => $objUserReferalFees,
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

        $objUserReferalFee = UserReferalFee::select(\DB::raw('*'));
        $objUserReferalFee = $this->getListBuilder($objUserReferalFee, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objUserReferalFee = $objUserReferalFee->first();
        if (empty($objUserReferalFee)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['user_referal_fee' => $objUserReferalFee])->result();
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
        $objUserReferalFee = UserReferalFee::where('id', $intId)->first();
        if (empty($objUserReferalFee)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy loại phí này.")]
            ])->result();
        }

        $objUserReferalFee->name = $strFeeName;
        $objUserReferalFee->fee = $intFee;
        $objUserReferalFee->min_fee = $intMinFee;
        $objUserReferalFee->type_id = $intTypeId;
        if (!$objUserReferalFee->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Cập nhật công."))->setData(["user_referal_fee" => $objUserReferalFee])->result();
    }
}
