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
     *     summary="Virtual account list",
     *     description="Returns the paginated list of virtual accounts of the authenticated user. Use the <b>query</b> object to filter and the <b>sort</b> object to order the result. All filters are optional and are combined with AND.",
     *     tags={"Virtual Account"},
     *     @OA\Parameter(name="api-token", in="header", description="token allow create service", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="page", type="integer", minimum=1, default=1, example=1, description="Page number."),
     *                 @OA\Property(property="limit", type="integer", minimum=1, default=10, example=10, description="Number of records per page."),
     *                 @OA\Property(
     *                     property="query",
     *                     type="object",
     *                     description="Filter conditions. Unknown keys are ignored.",
     *                     @OA\Property(property="name", type="string", example="SBXMSB", description="Search by account number (partial match)."),
     *                     @OA\Property(property="bank_account_number", type="string", description="Exact account number."),
     *                     @OA\Property(property="bank_short_code", type="string", example="MSB", description="Filter by bank code (e.g. MSB, TCB, MB)."),
     *                     @OA\Property(property="status_id", type="integer", enum={1,2}, example=2, description="Account status. <b>2</b>: active, <b>1</b>: inactive (deactivated / under maintenance)."),
     *                     @OA\Property(property="account_type", type="string", enum={"O","M"}, example="M", description="Account type. <b>O</b>: one-time, <b>M</b>: multiple-use."),
     *                     @OA\Property(property="min_amount", type="integer", description="Exact minimum amount configured on the account."),
     *                     @OA\Property(property="max_amount", type="integer", description="Exact maximum amount configured on the account."),
     *                     @OA\Property(property="created_at_from", type="string", example="2026-10-01", description="Created from date (inclusive, 00:00:00). Format YYYY-MM-DD."),
     *                     @OA\Property(property="created_at_to", type="string", example="2026-10-31", description="Created to date (inclusive, 23:59:59). Format YYYY-MM-DD.")
     *                 ),
     *                 @OA\Property(
     *                     property="sort",
     *                     type="object",
     *                     description="Sort order, as {column: direction}. Direction is asc or desc. Default is id desc.",
     *                     example={"created_at": "desc"}
     *                 ),
     *                 @OA\Property(property="sign", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Examples(example="result_false", value={"error_code":401,"system_time":"2023-11-24 11:15:59","message":"access_token expired or not allowed","errors":{"access_token expired or not allowed"},"data":null,"tranid_tracking":"bc47d534b97f7cdb4c2d5d229376915c"}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code": 0,"message": "success","system_time":"2023-11-24 11:15:59","errors":{},"data":{"user_virtual_accounts":{},"records_total":0,"page":1,"limit":10}}, summary="Result success."),
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

    /**
     * @OA\Post(
     *     path="/api/user-virtual-account/update",
     *     summary="Update virtual account",
     *     description="Updates the account type (one-time / multiple-use) and the minimum / maximum amount of a virtual account. At least one of account_type, min_amount, max_amount is required.",
     *     tags={"Virtual Account"},
     *     @OA\Parameter(name="api-token", in="header", description="token allow create service", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"account_number"},
     *                 @OA\Property(property="account_number", type="string", example="SBXMSB12345678901", description="Virtual account number returned when the account was created."),
     *                 @OA\Property(property="account_type", type="string", enum={"O","M"}, example="M", description="Virtual account type. <b>O</b>: one-time account, closed automatically after receiving a payment. <b>M</b>: multiple-use account. Omit to keep the current value."),
     *                 @OA\Property(property="min_amount", type="integer", minimum=0, example=10000, description="Minimum amount (VND) accepted per transaction. Integer >= 0, must not be greater than max_amount. Omit to keep the current value."),
     *                 @OA\Property(property="max_amount", type="integer", minimum=0, example=5000000, description="Maximum amount (VND) accepted per transaction. Integer >= 0, must not be less than min_amount. Omit to keep the current value."),
     *                 @OA\Property(property="sign", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Examples(example="result_error", value={"error_code":404,"system_time":"2023-11-24 11:15:59","message":"","errors":{"Virtual account not found."},"data":null}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code": 0,"message": "success","system_time":"2023-11-24 11:15:59","errors":{},"data":{}}, summary="Result success."),
     *         )
     *     )
     * )
     */
    public function update()
    {
        $arrParams = request(['account_number', 'account_type', 'min_amount', 'max_amount']);
        if (!auth()->user()->full_access) {
            $arrParams["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->updateByAccountNumber($arrParams));
    }

    /**
     * @OA\Post(
     *     path="/api/user-virtual-account/active",
     *     summary="Activate virtual account",
     *     description="Re-opens a virtual account so that it can receive payments again.",
     *     tags={"Virtual Account"},
     *     @OA\Parameter(name="api-token", in="header", description="token allow create service", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"account_number"},
     *                 @OA\Property(property="account_number", type="string", example="SBXMSB12345678901", description="Virtual account number returned when the account was created."),
     *                 @OA\Property(property="sign", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Examples(example="result_error", value={"error_code":404,"system_time":"2023-11-24 11:15:59","message":"","errors":{"Virtual account not found."},"data":null}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code": 0,"message": "success","system_time":"2023-11-24 11:15:59","errors":{},"data":{}}, summary="Result success."),
     *         )
     *     )
     * )
     */
    public function active()
    {
        $arrParams = request(['account_number']);
        if (!auth()->user()->full_access) {
            $arrParams["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->changeStatusByAccountNumber($arrParams, 2));
    }

    /**
     * @OA\Post(
     *     path="/api/user-virtual-account/deactivate",
     *     summary="Deactivate virtual account",
     *     description="Closes a virtual account so that it can no longer receive payments.",
     *     tags={"Virtual Account"},
     *     @OA\Parameter(name="api-token", in="header", description="token allow create service", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"account_number"},
     *                 @OA\Property(property="account_number", type="string", example="SBXMSB12345678901", description="Virtual account number returned when the account was created."),
     *                 @OA\Property(property="sign", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Examples(example="result_error", value={"error_code":404,"system_time":"2023-11-24 11:15:59","message":"","errors":{"Virtual account not found."},"data":null}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code": 0,"message": "success","system_time":"2023-11-24 11:15:59","errors":{},"data":{}}, summary="Result success."),
     *         )
     *     )
     * )
     */
    public function deactivate()
    {
        $arrParams = request(['account_number']);
        if (!auth()->user()->full_access) {
            $arrParams["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->changeStatusByAccountNumber($arrParams, 1));
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