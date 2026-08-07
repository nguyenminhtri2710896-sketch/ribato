<?php

namespace App\Services;

use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class UserNotificationService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserNotification())->getFillable();
    }


    public function getList($arrParams = [])
    {

        $intPage   = $arrParams["page"] ?? 1;
        $intLimit  = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objUserNotifications = UserNotification::select();
        $objUserNotifications = $this->getListBuilder($objUserNotifications, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objUserNotifications;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUserNotifications = $objUserNotifications->orderBy("id", "DESC");
        }
        $objUserNotifications = $objUserNotifications->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'user_notifications' => $objUserNotifications,
            'records_total' => $intTotal,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }


    public function add($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "user_id" => "required",
                "title" => "required",
                "content" => "required",
            ],
            [

                "user_id.required" => __("Vui lòng nhập Id người dùng."),
                "title.required" => __("Vui lòng nhập tiêu đề."),
                "content.required" => __("Vui lòng nhập nội dung."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $arrData    = $arrParams["data"] ?? [];
        $intUserId  = $arrParams["user_id"];
        $strTitle   = $arrParams["title"];
        $strContent = $arrParams["content"];

        $objUser = Users::where(["id" => $intUserId])->first();
        if (empty($objUser)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng này không tồn tại.")]
            ])->result();
        }

        $objUserNotification = UserNotification::create([
            "user_id" => $intUserId,
            "title" => $strTitle,
            "content" => $strContent,
            "data_json" => json_encode($arrData)
        ]);

        if (empty($objUserNotification)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thêm thông báo thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm thông báo thành công."))->setData(["user_notification" => $objUserNotification])->result();
    }

    public function delete($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập mã Tỉnh, Thành Phố."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intId               = $arrParams["id"];
        $objUserNotification = UserNotification::where('id', $intId)->first();
        if (empty($objUserNotification)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông báo này.")]
            ])->result();
        }

        if (!$objUserNotification->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá thông báo thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xoá thông báo thành công."))->setData([])->result();
    }


    public function getDetail($arrParams = [])
    {

        $objUserNotification = UserNotification::select(\DB::raw('*'));
        $objUserNotification = $this->getListBuilder($objUserNotification, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objUserNotification = $objUserNotification->first();
        if (empty($objUserNotification)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['user_notification' => $objUserNotification])->result();
    }

    public function readed($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập mã thông báo."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intId               = $arrParams["id"];
        $intUserId           = $arrParams["user_id"] ?? 0;
        $objUserNotification = UserNotification::where('id', $intId)->first();
        if (empty($objUserNotification)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông báo này.")]
            ])->result();
        }

        if (!empty($intUserId)) {
            if ($intUserId != $objUserNotification->user_id) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Bạn không có quyền đọc thông báo này.")]
                ])->result();
            }
        }

        $objUserNotification->readed_at = Carbon::now();
        $objUserNotification->is_readed = 1;
        if (!$objUserNotification->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Đọc thông báo thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Đọc thông báo thành công."))->setData(["user_notification" => $objUserNotification])->result();
    }

    public function readAll($arrParams = [])
    {
        $intUserId = $arrParams["user_id"] ?? 0;
        if (!empty($intUserId)) {
            UserNotification::where('is_readed', 2)->where('user_id', $intUserId)->update([
                "readed_at" => Carbon::now(),
                "is_readed" => 1
            ]);
        } else {
            UserNotification::where('is_readed', 2)->update([
                "readed_at" => Carbon::now(),
                "is_readed" => 1
            ]);
        }
        return $this->setStatusCode(0)->setMessage(__("Cập nhật thành công."))->setData([])->result();
    }

    public function readShower($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
                "user_id" => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập mã thông báo."),
                "user_id.required" => __("Vui lòng nhập user_id."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserId           = $arrParams["user_id"];
        $intId               = $arrParams["id"];
        $objUserNotification = UserNotification::where('id', $intId)->first();
        if (empty($objUserNotification)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông báo này.")]
            ])->result();
        }

        if ($intUserId != $objUserNotification->user_id) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Bạn không có quyền đọc thông báo này.")]
            ])->result();
        }

        $objUserNotification->is_shower = 1;
        if (!$objUserNotification->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Đọc thông báo thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Đọc thông báo thành công."))->setData([])->result();
    }
}