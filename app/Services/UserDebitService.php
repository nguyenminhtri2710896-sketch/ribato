<?php

namespace App\Services;

use App\Models\UserDebit;
use App\Models\User;
use App\Models\UserBalance;
use App\Models\UserFee;
use App\Models\UserReferalFee;
use App\Models\UserToken;
use App\Models\UserYoobilConfig;
use App\Utilities\General;
use Curl\Curl;
use Illuminate\Support\Facades\Validator;

class UserDebitService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserDebit())->getFillable();
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Chờ xủa lý'
        ],
        2 => [
            'name' => 'Thành công'
        ],
        3 => [
            'name' => 'Huỷ'
        ]
    ];

    public static $arrTypeId = [
        1 => [
            'name' => 'Chuyển ngoài hệ thống'
        ],
        2 => [
            'name' => 'Chuyển trong hệ thống'
        ],
        3 => [
            'name' => 'Lập trích quỹ'
        ]
    ];


    public function getList($arrParams = [])
    {

        $intPage = $arrParams["page"] ?? 1;
        $intLimit = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;
        $this->arrFillable = array_merge($this->arrFillable, (new User())->getFillable());
        $objUserDebits = UserDebit::select(\DB::raw('user_debits.*,users.fullname as user_fullname'))
            ->leftJoin('users', 'users.id', 'user_debits.user_id');
        $objUserDebits = $this->getListBuilder($objUserDebits, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objUserDebits;
        $intTotal = $objTotal->count();

        $objSumAmount = $objUserDebits;

        if (empty($arrParams["sort"])) {
            $objUserDebits = $objUserDebits->orderBy("user_debits.id", "DESC");
        }
        $objUserDebits = $objUserDebits->offset($intOffset)->limit($intLimit)->get();

        return $this->setStatusCode(intStatusCode: 0)->setData([
            'user_debits' => $objUserDebits,
            'user_debit_sum_amount' => $objSumAmount->sum('user_debits.amount'),
            'records_total' => $intTotal,
            'status' => self::$arrStatusId,
            'type' => self::$arrTypeId,
            'page' => (int) $intPage,
            'limit' => (int) $intLimit,
            "params" => $arrParams,
        ])->result();
    }


    public function getDetail($arrParams = [])
    {

        $objUserDebit = UserDebit::select();
        $objUserDebit = $this->getListBuilder($objUserDebit, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objUserDebit = $objUserDebit->first();
        if (empty($objUserDebit)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        $objUser = User::where('id', $objUserDebit->user_id)->first();
        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['user_debit' => $objUserDebit, 'user' => $objUser])->result();
    }

    public function add($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                'user_id' => "required",
                'type_id' => "required",
                'amount' => "required",
                'debit_at' => "required",
            ],
            [

                "user_id.required" => __("Vui lòng chọn người dùng."),
                "type_id.required" => __("Vui lòng chọn loại công nợ."),
                "amount.required" => __("Vui lòng nhập số tiền."),
                "debit_at.required" => __("Vui lòng nhập ngày ghi nợ."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserId = $arrParams["user_id"];
        $intTypeId = $arrParams["type_id"];
        $intAmount = $arrParams["amount"];
        $strNote = $arrParams["note"] ?? "";
        $strDebitAt = General::formatInputDay($arrParams["debit_at"]);
        /**
         * Lấy bank 
         */

        $objUser = User::where('id', $intUserId)->first();
        if (!$objUser) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Người dùng không tồn tại.")]
            ])->result();
        }

        $objUserDebit = UserDebit::create(attributes: [
            'user_create_id' => $arrParams["user_create_id"] ?? 0,
            'user_id' => $intUserId,
            'type_id' => $intTypeId,
            'type_name' => self::$arrTypeId[$intTypeId]["name"] ?? "",
            'amount' => $intAmount,
            'note' => $strNote,
            'debit_at' => $strDebitAt,
            'status_id' => 2,
        ]);

        if (!$objUserDebit) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Tạo công nợ thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Tạo công nợ thành công."))->setData(["user_debit" => $objUserDebit])->result();
    }

    public function delete($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                "id" => "required",
            ],
            [

                "id.required" => __("Vui lòng nhập mã."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intId = $arrParams["id"];
        $objUserDebit = UserDebit::where('id', $intId)->first();
        if (empty($objUserDebit)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        $objUserDebit->user_delete_id = $arrParams["user_delete_id"] ?? 0;
        $objUserDebit->save();


        if (!$objUserDebit->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá thất bại.")]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Xoá thành công."))->setData([])->result();
    }

}