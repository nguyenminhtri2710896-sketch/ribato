<?php

namespace App\Services;

use App\Models\Gateway;
use Illuminate\Support\Facades\Validator;

class GatewayService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new Gateway())->getFillable();
    }


    public static $arrStatusId = [
        1 => [
            'name' => 'Đang bảo trì'
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

        $objGateways = Gateway::select();
        $objGateways = $this->getListBuilder($objGateways, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objGateways;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objGateways = $objGateways->orderBy("id", "DESC");
        }
        $objGateways = $objGateways->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'gateways' => $objGateways,
            'status' => self::$arrStatusId,
            'records_total' => $intTotal,
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

        $objBanks = $arrResult["data"]["gateways"];
        $arrData = [];
        foreach ($objBanks as $objBank) {
            $arrData[] = [
                "id" => $objBank->id,
                "text" => $objBank->name,
            ];
        }
        return ["results" => $arrData, "pagination" => ["more" => $arrResult["data"]["records_total"] >= ($intLimit * $intPage) ? true : false], 'limit' => $intLimit];
    }


    public function add($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "name" => "required",
            ],
            [

                "name.required" => __("Vui lòng nhập tên nhóm."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $strGroupName = $arrParams["name"];
        $intStatusId = !empty($arrParams["status_id"]) ? 1 : 0;
        $objGateway = Gateway::create([
            "name" => $strGroupName,
            "status_id" => $intStatusId
        ]);

        if (empty($objGateway)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thêm Nhóm thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm Nhóm thành công."))->setData(["gateway" => $objGateway])->result();
    }

    public function update($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "name" => "required",
                "id" => "required",
            ],
            [

                "name.required" => __("Vui lòng nhập tên nhóm."),
                "id.required" => __("Vui lòng nhập mã nhóm."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $strGroupName = $arrParams["name"];
        $intId = $arrParams["id"];
        $intStatusId = !empty($arrParams["status_id"]) ? 2 : 1;
        $objGateway = Gateway::where('id', $intId)->first();
        if (empty($objGateway)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        $objGateway->name = $strGroupName;
        $objGateway->status_id = $intStatusId;
        if (!$objGateway->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData(["gateway" => $objGateway])->result();
    }

    public function delete($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập mã nhóm."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intId = $arrParams["id"];
        $objGateway = Gateway::where('id', $intId)->first();
        if (empty($objGateway)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        if (!$objGateway->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xoá thành công."))->setData([])->result();
    }




    public function getDetail($arrParams = [])
    {

        $objGateway = Gateway::select(\DB::raw('*'));
        $objGateway = $this->getListBuilder($objGateway, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objGateway = $objGateway->first();
        if (empty($objGateway)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['gateway' => $objGateway])->result();
    }
}