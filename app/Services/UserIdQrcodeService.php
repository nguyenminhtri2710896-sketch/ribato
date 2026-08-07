<?php

namespace App\Services;

use App\Models\UserBankAccount;
use App\Models\UserIdQrcode;
use Illuminate\Support\Facades\Validator;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use tttran\viet_qr_generator\Generator;

class UserIdQrcodeService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new UserIdQrcode())->getFillable();
    }



    public function getList($arrParams = [])
    {

        $intPage   = $arrParams["page"] ?? 1;
        $intLimit  = $arrParams["limit"] ?? 10;
        $intOffset = ($intPage - 1) * $intLimit;

        $objUserIdQrcodes = UserIdQrcode::select();
        $objUserIdQrcodes = $this->getListBuilder($objUserIdQrcodes, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objTotal = $objUserIdQrcodes;
        $intTotal = $objTotal->count();
        if (empty($arrParams["sort"])) {
            $objUserIdQrcodes = $objUserIdQrcodes->orderBy("id", "DESC");
        }
        $objUserIdQrcodes = $objUserIdQrcodes->offset($intOffset)->limit($intLimit)->get();


        return $this->setStatusCode(0)->setData([
            'user_id_qrcodes' => $objUserIdQrcodes,
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

        $objUserIdQrcodes = $arrResult["data"]["UserIdQrcodes"];
        $arrData          = [];
        foreach ($objUserIdQrcodes as $objUserIdQrcode) {
            $arrData[] = [
                "id" => $objUserIdQrcode->id,
                "text" => $objUserIdQrcode->name,
            ];
        }
        return ["results" => $arrData, "pagination" => ["more" => $arrResult["data"]["records_total"] >= ($intLimit * $intPage) ? true : false], 'limit' => $intLimit];
    }

    public function getDetail($arrParams = [])
    {

        $objUserIdQrcode = UserIdQrcode::select();
        $objUserIdQrcode = $this->getListBuilder($objUserIdQrcode, $arrParams, $this->arrFillable);
        /**
         * Có thể thêm bất kì j vô đây nếu có trường hợp đặc biệt
         */

        $objUserIdQrcode = $objUserIdQrcode->first();
        if (empty($objUserIdQrcode)) {
            return $this->setStatusCode(404)->setMessage('')->setData([])->setErrors([
                [__('Không tìm thấy dữ liệu.')]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__('Thành công.'))->setData(['user_id_qrcode' => $objUserIdQrcode])->result();
    }

    public function add($arrParams = [])
    {

        $validated = Validator::make(
            $arrParams,
            [
                'name',
                'code',
                'user_bank_account_id'
            ],
            [

                "name.required" => __("Vui lòng nhập tên."),
                "code.required" => __("Vui lòng nhập mã."),
                "user_bank_account_id.required" => __("Vui lòng chọn tài khoản VA."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }
        $intUserId            = $arrParams["user_id"] ?? 0;
        $strCode              = $arrParams["code"];
        $intUserBankAccountId = $arrParams["user_bank_account_id"];

        if (strlen($strCode) < 3 || strlen($strCode) > 10) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors([
                [__("Chiều dài mã không hợp lệ ( tối thiểu 4 ký tự,tối đa 10 ký tự).")]
            ])->result();
        }

        $objUserIdQrcode = UserIdQrcode::where('code', $strCode)->first();
        if ($objUserIdQrcode) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Mã code này đã được sử dụng.")]
            ])->result();
        }

        /**
         * Lấy tài khoản VA
         */

        $objUserBankAccount = UserBankAccount::select(\DB::raw('user_bank_accounts.*,banks.logo as bank_logo, banks.name as bank_name,banks.id as bank_id, banks.short_code as bank_short_code, users.email as user_email, users.fullname as user_fullname,bank_accounts.bank_account_name as bank_account_name,bank_accounts.bank_account_number as bank_account_number'))->join('users', 'users.id', 'user_bank_accounts.user_id')
            ->join('bank_accounts', 'bank_accounts.id', 'user_bank_accounts.bank_account_id')
            ->join('banks', 'banks.id', 'bank_accounts.bank_id')
            ->where('user_bank_accounts.id', $intUserBankAccountId);
        if (!empty($intUserId)) {
            $objUserBankAccount = $objUserBankAccount->where('user_bank_accounts.user_id', $intUserId);
        }
        $objUserBankAccount = $objUserBankAccount->first();
        if (!$objUserBankAccount) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tồn tại tài khoản VA, vui lòng kiểm tra lại.")]
            ])->result();
        }



        $strInfoRemark = $strCode;
        $generator     = (new Generator())->create()
            ->bankId($objUserBankAccount->bank_short_code)
            ->accountNo($objUserBankAccount->bank_account_number)// Account number
            ->amount(0)// Money
            ->info($strInfoRemark) // Ref
            ->generate();

        $arrGenerator = json_decode($generator, true);
        if ($arrGenerator["code"] != 200) {
            return $this->setStatusCode(404)->setMessage("")->setData($arrGenerator)->setErrors([
                [__("Có lỗi khi tạo mã QR.")]
            ])->result();
        }


        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $arrGenerator["data"] ?? "",
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $arrParams["size"] ?? 200,
            margin: 3,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoResizeToWidth: 50,
            logoPunchoutBackground: true,
            labelText: $arrParams["label"] ?? "",
            labelFont: new OpenSans(20),
            labelAlignment: LabelAlignment::Center
        );
        $result  = $builder->build();
        $strUrl  = 'uploads/qrcode/' . md5(time() . rand(11111, 99999)) . ".png";
        if (!file_exists(base_path("static/uploads/qrcode"))) {
            mkdir(base_path("static/uploads/qrcode"), 0755, true);
        }

        file_put_contents(base_path("static/" . $strUrl), $result->getString());

        $arrParams["bank_id"]             = $objUserBankAccount->bank_id;
        $arrParams["bank_account_name"]   = $objUserBankAccount->bank_account_name;
        $arrParams["bank_account_number"] = $objUserBankAccount->bank_account_number;
        $arrParams["path_qrcode"]         = $strUrl;
        // $arrParams["base64"]         = $result->getString();
        // $result->getString()

        $arrInsert       = self::getFilterParams($arrParams, (new UserIdQrcode())->getFillable());
        $objUserIdQrcode = UserIdQrcode::create($arrInsert);

        if (empty($objUserIdQrcode)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Thêm thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Thêm thành công."))->setData(["user_id_qrcode" => $objUserIdQrcode->toArray()+["qr_base64"=> base64_encode($result->getString())]])->result();
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
        $intId           = $arrParams["id"];
        $intUserId       = $arrParams["user_id"] ?? 1;
        $objUserIdQrcode = UserIdQrcode::where('id', $intId)->first();
        if (empty($objUserIdQrcode)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Không tìm thấy thông tin.")]
            ])->result();
        }

        if (!empty($intUserId)) {
            if ($intUserId != $objUserIdQrcode->user_id) {
                return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                    [__("Bạn không có quyền xoá mã này.")]
                ])->result();
            }
        }

        if (!$objUserIdQrcode->delete()) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Xoá thất bại.")]
            ])->result();
        }
        return $this->setStatusCode(0)->setMessage(__("Xoá thành công."))->setData([])->result();
    }



    // public function qrcode($arrParams = [])
    // {

    //     $validated = Validator::make(
    //         $arrParams,
    //         [
    //             "id" => "required",
    //         ],
    //         [

    //             "id.required" => __("Vui lòng nhập id."),
    //         ]
    //     );
    //     if ($validated->errors()->messages()) {
    //         return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
    //     }
    //     $intId           = $arrParams["id"];
    //     $intUserId       = $arrParams["user_id"] ?? 1;
    //     $objUserIdQrcode = UserIdQrcode::where('id', $intId)->first();
    //     if (empty($objUserIdQrcode)) {
    //         return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
    //             [__("Không tìm thấy thông tin.")]
    //         ])->result();
    //     }

    //     if (!empty($intUserId)) {
    //         if ($intUserId != $objUserIdQrcode->user_id) {
    //             return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
    //                 [__("Bạn không có quyền xoá mã này.")]
    //             ])->result();
    //         }
    //     }


    //     $builder = new Builder(
    //         writer: new PngWriter(),
    //         writerOptions: [],
    //         validateResult: false,
    //         data: $arrGenerator["data"] ?? "",
    //         encoding: new Encoding('UTF-8'),
    //         errorCorrectionLevel: ErrorCorrectionLevel::High,
    //         size: $arrParams["size"] ?? 200,
    //         margin: 3,
    //         roundBlockSizeMode: RoundBlockSizeMode::Margin,
    //         logoResizeToWidth: 50,
    //         logoPunchoutBackground: true,
    //         labelText: $arrParams["label"] ?? "",
    //         labelFont: new OpenSans(20),
    //         labelAlignment: LabelAlignment::Center
    //     );
    //     $result  = $builder->build();
    //     return $this->setStatusCode(0)->setMessage(__("Xoá thành công."))->setData([])->result();
    // }
}