<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Bank;
use App\Services\UserVirtualAccountService;

class UserVirtualAccountController extends BaseController
{

    private $userVirtualAccountService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserVirtualAccountService $userVirtualAccountService)
    {
        $this->userVirtualAccountService = $userVirtualAccountService;
    }

    /**
     * @OA\Post(
     *     path="/api/user-virtual-account/get-list",
     *     summary="Danh sách VA",
     *     tags={"Virtual Account"},
     *      @OA\Parameter(
     *         name="api-token",
     *         in="header",
     *         description="token allow create service",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="sign",
     *                     type="string"
     *                 ),
     *                 example={"sign": "signiture data"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Examples(example="result_false", value={"error_code":401,"system_time":"2023-11-24 11:15:59","message":"access_token hết hạn hoặc không được phép sử dụng","errors":{"access_token hết hạn hoặc không được phép sử dụng"},"data":null,"tranid_tracking":"bc47d534b97f7cdb4c2d5d229376915c"}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code": 200,"message": "success","system_time":"2023-11-24 11:15:59","errors":{},"data":{}}, summary="Result success."),
     *         )
     *     )
     * )
     */
    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["bank_account_number"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }

        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }

        $result = $this->userVirtualAccountService->getList($arrParams);
        if ($result["error_code"] != 0) {
            return $result;
        }

        if (!auth()->user()->full_access) {
            foreach ($result["data"]["user_virtual_accounts"] ?? [] as $objUservirtualAccount) {
                $arrItem = $objUservirtualAccount->toArray();
                if (isset($arrItem["gateway_id"])) {
                    unset($arrItem["gateway_id"]);
                }

                if (isset($arrItem["gateway_name"])) {
                    unset($arrItem["gateway_name"]);
                }
                $resultData[] = $arrItem;
            }
        } else {
            $resultData = $result["data"]["user_virtual_accounts"];
        }
        $result["data"]["user_virtual_accounts"] = $resultData;

        return response()->json($result);
    }

    public function select2GetList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]["name"])) {
            $arrParams["query_or_like"] = [
                "user_virtual_accounts.bank_account_name" => $arrParams["query"]["name"],
                "user_virtual_accounts.bank_account_number" => $arrParams["query"]["name"],
            ];
            unset($arrParams["query"]["name"]);
        }

        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->responseSelect2($this->userVirtualAccountService->getList($arrParams)));
    }
    /**
     * @OA\Post(
     *     path="/api/user-virtual-account/create",
     *     summary="Create virtual account",
     *     description="Creates a virtual account (VA) for the authenticated user. The payment gateway account is resolved from the user's configuration, and the bank must be one of the banks enabled for the user.",
     *     tags={"Virtual Account"},
     *     @OA\Parameter(name="api-token", in="header", description="token allow create service", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"bank_code","bank_account_name"},
     *                 @OA\Property(property="bank_code", type="string", example="MSB", description="Bank code used to create the VA (e.g. MSB, TCB, MB). It must be one of the banks enabled for your account, otherwise an error is returned."),
     *                 @OA\Property(property="bank_account_name", type="string", example="NGUYEN VAN A", description="Account holder name of the VA. It is converted to upper case automatically."),
     *                 @OA\Property(property="account_type", type="string", enum={"O","M"}, default="M", example="M", description="Virtual account type. <b>O</b>: one-time account, closed automatically after receiving a payment. <b>M</b>: multiple-use account, can receive payments repeatedly. Defaults to M when omitted."),
     *                 @OA\Property(property="min_amount", type="integer", minimum=0, example=10000, description="Minimum amount (VND) accepted per transaction. Integer >= 0, must not be greater than max_amount. Omit or set 0 for no limit."),
     *                 @OA\Property(property="max_amount", type="integer", minimum=0, example=5000000, description="Maximum amount (VND) accepted per transaction. Integer >= 0, must not be less than min_amount. Omit or set 0 for no limit."),
     *                 @OA\Property(property="sign", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Examples(example="result_error", value={"error_code":404,"system_time":"2023-11-24 11:15:59","message":"","errors":{"VA gateway has not been configured for this account."},"data":null}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code": 0,"message": "success","system_time":"2023-11-24 11:15:59","errors":{},"data":{}}, summary="Result success."),
     *         )
     *     )
     * )
     */
    public function create()
    {
        $arrParams = request(['bank_code', 'bank_account_name', 'account_type', 'min_amount', 'max_amount']);
        $objUser = auth()->user();

        if (empty($objUser->va_gateway_account_id)) {
            return response()->json([
                "error_code" => 404,
                "message" => "",
                "system_time" => date("Y-m-d H:i:s"),
                "errors" => [[__("Tài khoản chưa được cấu hình cổng tạo VA.")]],
                "data" => [],
            ]);
        }

        $strBankCode = strtoupper(trim((string) ($arrParams["bank_code"] ?? "")));
        if ($strBankCode === "") {
            return response()->json([
                "error_code" => 414,
                "message" => "",
                "system_time" => date("Y-m-d H:i:s"),
                "errors" => [[__("Vui lòng nhập bank_code.")]],
                "data" => [],
            ]);
        }

        $objBank = Bank::where('short_code', $strBankCode)->first();
        if (empty($objBank)) {
            return response()->json([
                "error_code" => 404,
                "message" => "",
                "system_time" => date("Y-m-d H:i:s"),
                "errors" => [[__("Không tìm thấy ngân hàng này.")]],
                "data" => [],
            ]);
        }
        $arrAllowBankCodes = array_filter(explode(",", (string) $objUser->va_bank_codes));
        if (empty($arrAllowBankCodes)) {
            return response()->json([
                "error_code" => 404,
                "message" => "",
                "system_time" => date("Y-m-d H:i:s"),
                "errors" => [[__("Tài khoản chưa được cấu hình ngân hàng tạo VA.")]],
                "data" => [],
            ]);
        }
        if (!in_array($strBankCode, $arrAllowBankCodes)) {
            return response()->json([
                "error_code" => 414,
                "message" => "",
                "system_time" => date("Y-m-d H:i:s"),
                "errors" => [[__("Tài khoản không được phép tạo VA ngân hàng :bank. Ngân hàng hỗ trợ: :banks.", ["bank" => $strBankCode, "banks" => implode(", ", $arrAllowBankCodes)])]],
                "data" => [],
            ]);
        }
        unset($arrParams["bank_code"]);
        $arrParams["bank_id"] = $objBank->id;

        $arrParams["user_id"] = $objUser->id;
        $arrParams["gateway_account_id"] = $objUser->va_gateway_account_id;
        return response()->json($this->userVirtualAccountService->add($arrParams));
    }

    public function add()
    {
        $arrParams = request()->all();
        if (!auth()->user()->full_access) {
            $arrParams["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->add($arrParams));
    }

    public function getDetail()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!auth()->user()->full_access) {
            $arrParams["query"]["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->getDetail($arrParams));
    }

    public function changeStatus()
    {
        $arrParams = request()->all();
        if (!auth()->user()->full_access) {
            $arrParams["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->changeStatus($arrParams));
    }
}