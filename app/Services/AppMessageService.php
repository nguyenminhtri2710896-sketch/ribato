<?php

namespace App\Services;

use App\Models\AppMessage;
use Illuminate\Support\Facades\Validator;

class AppMessageService extends AbstractService
{
    // 1: notification
// 2: sms
    public static $arrTypeId = [
        1 => [
            'name' => "Notification",
        ],
        2 => [
            'name' => "Sms",
        ],
        3 => [
            "name" => "Vbill"
        ]
    ];

    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new AppMessage())->getFillable();
    }


    public function getList($arrParams = [])
    {

        $intPage   = $arrParams["page"] ?? 1;
        $intLimit  = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objAppMessages = AppMessage::select();
        $objAppMessages = $this->getListBuilder($objAppMessages, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objAppMessages;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objAppMessages = $objAppMessages->orderBy("id", "DESC");
        }
        $objAppMessages = $objAppMessages->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'app_messages' => $objAppMessages,
            'type' => self::$arrTypeId,
            'records_total' => $intTotal,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }

    public function getDetail($arrParams = [])
    {

        $objAppMessage = AppMessage::select();
        $objAppMessage = $this->getListBuilder($objAppMessage, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objAppMessage = $objAppMessage->first();
        if (empty($objAppMessage)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['app_message' => $objAppMessage])->result();
    }
    public function add($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'sender' => "required",
                // 'receiver' => "required"
            ],
            [

                "sender.required" => __("Vui lòng nhập sender."),
                // "receiver.required" => __("Vui lòng nhập ."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $arrParams["type_id"] = $intTypeId = $arrParams["type_id"] ?? 1;
        if (empty(self::$arrTypeId[$intTypeId])) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Loại không hợp lệ.")]
            ])->result();
        }

        $arrInsert     = self::getFilterParams($arrParams, (new AppMessage())->getFillable());
        $objAppMessage = AppMessage::create($arrInsert);

        if (empty($objAppMessage)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thêm thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm thành công."))->setData(["app_message" => $objAppMessage])->result();
    }

}