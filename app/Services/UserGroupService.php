<?php

namespace App\Services;

use App\Models\Trash;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Validator;

class UserGroupService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserGroup())->getFillable();
    }




    public function getList($arrParams = [])
    {

        $intPage   = $arrParams["page"] ?? 1;
        $intLimit  = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objUserGroups = UserGroup::select();
        $objUserGroups = $this->getListBuilder($objUserGroups, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objUserGroups;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUserGroups = $objUserGroups->orderBy("id", "DESC");
        }
        $objUserGroups = $objUserGroups->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'user_groups' => $objUserGroups,
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
        $intPage  = $arrResult["data"]["page"] ?? 1;

        $objBanks = $arrResult["data"]["user_groups"];
        $arrData  = [];
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
                "description" => "required",
            ],
            [

                "name.required" => __("Vui lòng nhập tên nhóm."),
                "description.required" => __("Vui lòng nhập mô tả nhỏm."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $strGroupName   = $arrParams["name"];
        $strDescription = $arrParams["description"];
        $intActived     = !empty($arrParams["actived"]) ? 1 : 0;
        $objUserGroup   = UserGroup::create([
            "name" => $strGroupName,
            "description" => $strDescription,
            "actived" => $intActived
        ]);

        if (empty($objUserGroup)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thêm Nhóm thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm Nhóm thành công."))->setData(["user_group" => $objUserGroup])->result();
    }

    public function update($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "name" => "required",
                "description" => "required",
                "id" => "required",
            ],
            [

                "name.required" => __("Vui lòng nhập tên nhóm."),
                "description.required" => __("Vui lòng nhập mô tả nhỏm."),
                "id.required" => __("Vui lòng nhập mã nhóm."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $strGroupName   = $arrParams["name"];
        $strDescription = $arrParams["description"];
        $intId          = $arrParams["id"];
        $intActived     = !empty($arrParams["actived"]) ? 1 : 0;
        $objUserGroup   = UserGroup::where('id', $intId)->first();
        if (empty($objUserGroup)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy nhóm này.")]
            ])->result();
        }

        $objUserGroup->name        = $strGroupName;
        $objUserGroup->description = $strDescription;
        $objUserGroup->actived     = $intActived;
        if (!$objUserGroup->save()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Cập nhật nhóm thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Cập nhật nhóm công."))->setData(["user_group" => $objUserGroup])->result();
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
        $intId        = $arrParams["id"];
        $objUserGroup = UserGroup::where('id', $intId)->first();
        if (empty($objUserGroup)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy nhóm này.")]
            ])->result();
        }

        if (!$objUserGroup->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá nhóm thất bại.")]
            ])->result();
        }

        Trash::create([
            'table' => 'user_groups',
            'data' => json_encode($objUserGroup->toArray())
        ]);

        return $this->setStatusCode(0)->setMessage(__("Xoá nhóm thành công."))->setData([])->result();
    }




    public function getDetail($arrParams = [])
    {

        $objUserGroup = UserGroup::select(\DB::raw('*'));
        $objUserGroup = $this->getListBuilder($objUserGroup, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objUserGroup = $objUserGroup->first();
        if (empty($objUserGroup)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['user_group' => $objUserGroup])->result();
    }
}