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
     *             @OA\Property(property="error_code", type="integer", example=0, description="<b>0</b>: success. Other values: error (404 not found / not configured, 414 invalid payload, 401 unauthorized)."),
     *             @OA\Property(property="message", type="string", example="success", description="Result message."),
     *             @OA\Property(property="system_time", type="string", example="2026-10-20 10:00:00", description="Server time."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), description="Error messages. Empty on success."),
     *             @OA\Property(
     *                 property="data", type="object", description="Response payload.",
     *                 @OA\Property(
     *                     property="user_virtual_accounts", type="array", description="List of virtual accounts.",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1, description="Virtual account ID."),
     *                         @OA\Property(property="user_id", type="integer", example=10, description="Owner user ID."),
     *                         @OA\Property(property="bank_account_name", type="string", example="NGUYEN VAN A", description="Account holder name (upper case)."),
     *                         @OA\Property(property="bank_account_number", type="string", example="SBXMSB12345678901", description="Virtual account number. Customers transfer money to this number."),
     *                         @OA\Property(property="bank_id", type="integer", example=5, description="Bank ID."),
     *                         @OA\Property(property="bank_short_name", type="string", example="MSB", description="Bank code."),
     *                         @OA\Property(property="bank_short_code", type="string", example="MSB", description="Bank code."),
     *                         @OA\Property(property="status_id", type="integer", example=2, description="Account status. <b>2</b>: active, <b>1</b>: inactive (deactivated / under maintenance)."),
     *                         @OA\Property(property="account_type", type="string", example="M", description="Account type. <b>O</b>: one-time, <b>M</b>: multiple-use."),
     *                         @OA\Property(property="min_amount", type="integer", example=10000, description="Minimum amount (VND) per transaction. null = no limit."),
     *                         @OA\Property(property="max_amount", type="integer", example=5000000, description="Maximum amount (VND) per transaction. null = no limit."),
     *                         @OA\Property(property="order_code", type="string", example="K3JD92LQ0XPA7TVM", description="Order code generated by the system when the account is created. Use it to update the account."),
     *                         @OA\Property(property="created_at", type="string", example="2026-10-20T10:00:00.000000Z", description="Created time."),
     *                         @OA\Property(property="updated_at", type="string", example="2026-10-20T10:00:00.000000Z", description="Last updated time.")
     *                     )
     *                 ),
     *                 @OA\Property(property="records_total", type="integer", example=0, description="Total records matching the filters."),
     *                 @OA\Property(property="page", type="integer", example=1, description="Current page."),
     *                 @OA\Property(property="limit", type="integer", example=10, description="Records per page.")
     *             ),
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

        $resultData = [];
        foreach ($result["data"]["user_virtual_accounts"] ?? [] as $objUservirtualAccount) {
            $arrItem = $objUservirtualAccount->toArray();
            // order_no là mã nội bộ của cổng, đối tác dùng order_code
            unset($arrItem["order_no"]);
            if (!auth()->user()->full_access) {
                unset($arrItem["gateway_id"], $arrItem["gateway_name"]);
            }
            $resultData[] = $arrItem;
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
     *             @OA\Property(property="error_code", type="integer", example=0, description="<b>0</b>: success. Other values: error (404 not found / not configured, 414 invalid payload, 401 unauthorized)."),
     *             @OA\Property(property="message", type="string", example="success", description="Result message."),
     *             @OA\Property(property="system_time", type="string", example="2026-10-20 10:00:00", description="Server time."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), description="Error messages. Empty on success."),
     *             @OA\Property(
     *                 property="data", type="object", description="Response payload. null / empty on error.",
     *                 @OA\Property(
     *                     property="user_virtual_account", type="object", description="Created virtual account. Use <b>bank_account_number</b> to receive payments.",
     *                         @OA\Property(property="id", type="integer", example=1, description="Virtual account ID."),
     *                         @OA\Property(property="user_id", type="integer", example=10, description="Owner user ID."),
     *                         @OA\Property(property="bank_account_name", type="string", example="NGUYEN VAN A", description="Account holder name (upper case)."),
     *                         @OA\Property(property="bank_account_number", type="string", example="SBXMSB12345678901", description="Virtual account number. Customers transfer money to this number."),
     *                         @OA\Property(property="bank_id", type="integer", example=5, description="Bank ID."),
     *                         @OA\Property(property="bank_short_name", type="string", example="MSB", description="Bank code."),
     *                         @OA\Property(property="bank_short_code", type="string", example="MSB", description="Bank code."),
     *                         @OA\Property(property="status_id", type="integer", example=2, description="Account status. <b>2</b>: active, <b>1</b>: inactive (deactivated / under maintenance)."),
     *                         @OA\Property(property="account_type", type="string", example="M", description="Account type. <b>O</b>: one-time, <b>M</b>: multiple-use."),
     *                         @OA\Property(property="min_amount", type="integer", example=10000, description="Minimum amount (VND) per transaction. null = no limit."),
     *                         @OA\Property(property="max_amount", type="integer", example=5000000, description="Maximum amount (VND) per transaction. null = no limit."),
     *                         @OA\Property(property="order_no", type="string", example="", description="Reference code of the account on the payment gateway."),
     *                         @OA\Property(property="order_code", type="string", example="K3JD92LQ0XPA7TVM", description="Order code generated by the system when the account is created. Use it to update the account."),
     *                         @OA\Property(property="created_at", type="string", example="2026-10-20T10:00:00.000000Z", description="Created time."),
     *                         @OA\Property(property="updated_at", type="string", example="2026-10-20T10:00:00.000000Z", description="Last updated time.")
     *                 )
     *             ),
     *             @OA\Examples(example="result_error", value={"error_code":404,"system_time":"2023-11-24 11:15:59","message":"","errors":{"VA gateway has not been configured for this account."},"data":null}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code": 0,"message": "Added successfully.","system_time":"2023-11-24 11:15:59","errors":{},"data":{"user_virtual_account":{"id":1,"user_id":10,"bank_account_name":"NGUYEN VAN A","bank_account_number":"SBXMSB12345678901","bank_id":5,"bank_short_name":"MSB","bank_short_code":"MSB","status_id":2,"account_type":"M","min_amount":10000,"max_amount":5000000,"order_no":"","order_code":"K3JD92LQ0XPA7TVM","created_at":"2026-10-20T10:00:00.000000Z","updated_at":"2026-10-20T10:00:00.000000Z"}}}, summary="Result success."),
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
     *     description="Updates the account type (one-time / multiple-use) and the minimum / maximum amount of a virtual account, identified by the order_code returned at creation (the account must belong to the authenticated user). At least one of account_type, min_amount, max_amount is required.",
     *     tags={"Virtual Account"},
     *     @OA\Parameter(name="api-token", in="header", description="token allow create service", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"order_code"},
     *                 @OA\Property(property="order_code", type="string", example="K3JD92LQ0XPA7TVM", description="Order code returned when the account was created. The account must belong to the authenticated user."),
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
     *             @OA\Property(property="error_code", type="integer", example=0, description="<b>0</b>: success. Other values: error (404 not found / not configured, 414 invalid payload, 401 unauthorized)."),
     *             @OA\Property(property="message", type="string", example="success", description="Result message."),
     *             @OA\Property(property="system_time", type="string", example="2026-10-20 10:00:00", description="Server time."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), description="Error messages. Empty on success."),
     *             @OA\Property(
     *                 property="data", type="object", description="Response payload. null / empty on error.",
     *                 @OA\Property(
     *                     property="user_virtual_account", type="object", description="Updated virtual account.",
     *                         @OA\Property(property="id", type="integer", example=1, description="Virtual account ID."),
     *                         @OA\Property(property="user_id", type="integer", example=10, description="Owner user ID."),
     *                         @OA\Property(property="bank_account_name", type="string", example="NGUYEN VAN A", description="Account holder name (upper case)."),
     *                         @OA\Property(property="bank_account_number", type="string", example="SBXMSB12345678901", description="Virtual account number. Customers transfer money to this number."),
     *                         @OA\Property(property="bank_id", type="integer", example=5, description="Bank ID."),
     *                         @OA\Property(property="bank_short_name", type="string", example="MSB", description="Bank code."),
     *                         @OA\Property(property="bank_short_code", type="string", example="MSB", description="Bank code."),
     *                         @OA\Property(property="status_id", type="integer", example=2, description="Account status. <b>2</b>: active, <b>1</b>: inactive (deactivated / under maintenance)."),
     *                         @OA\Property(property="account_type", type="string", example="M", description="Account type. <b>O</b>: one-time, <b>M</b>: multiple-use."),
     *                         @OA\Property(property="min_amount", type="integer", example=10000, description="Minimum amount (VND) per transaction. null = no limit."),
     *                         @OA\Property(property="max_amount", type="integer", example=5000000, description="Maximum amount (VND) per transaction. null = no limit."),
     *                         @OA\Property(property="order_no", type="string", example="", description="Reference code of the account on the payment gateway."),
     *                         @OA\Property(property="order_code", type="string", example="K3JD92LQ0XPA7TVM", description="Order code generated by the system when the account is created. Use it to update the account."),
     *                         @OA\Property(property="created_at", type="string", example="2026-10-20T10:00:00.000000Z", description="Created time."),
     *                         @OA\Property(property="updated_at", type="string", example="2026-10-20T10:00:00.000000Z", description="Last updated time.")
     *                 )
     *             ),
     *             @OA\Examples(example="result_error", value={"error_code":404,"system_time":"2023-11-24 11:15:59","message":"","errors":{"Virtual account not found."},"data":null}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code": 0,"message": "Updated successfully.","system_time":"2023-11-24 11:15:59","errors":{},"data":{"user_virtual_account":{"id":1,"user_id":10,"bank_account_name":"NGUYEN VAN A","bank_account_number":"SBXMSB12345678901","bank_id":5,"bank_short_name":"MSB","bank_short_code":"MSB","status_id":2,"account_type":"M","min_amount":10000,"max_amount":5000000,"order_no":"","order_code":"K3JD92LQ0XPA7TVM","created_at":"2026-10-20T10:00:00.000000Z","updated_at":"2026-10-20T10:00:00.000000Z"}}}, summary="Result success."),
     *         )
     *     )
     * )
     */
    public function update()
    {
        $arrParams = request(['order_code', 'account_type', 'min_amount', 'max_amount']);
        if (!auth()->user()->full_access) {
            $arrParams["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->updateByOrderCode($arrParams));
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
     *                 required={"order_code"},
     *                 @OA\Property(property="order_code", type="string", example="K3JD92LQ0XPA7TVM", description="Order code returned when the account was created. The account must belong to the authenticated user."),
     *                 @OA\Property(property="sign", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="error_code", type="integer", example=0, description="<b>0</b>: success. Other values: error (404 not found / not configured, 414 invalid payload, 401 unauthorized)."),
     *             @OA\Property(property="message", type="string", example="success", description="Result message."),
     *             @OA\Property(property="system_time", type="string", example="2026-10-20 10:00:00", description="Server time."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), description="Error messages. Empty on success."),
     *             @OA\Property(property="data", type="object", description="Empty object on success."),
     *             @OA\Examples(example="result_error", value={"error_code":404,"system_time":"2023-11-24 11:15:59","message":"","errors":{"Virtual account not found."},"data":null}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code": 0,"message": "success","system_time":"2023-11-24 11:15:59","errors":{},"data":{}}, summary="Result success."),
     *         )
     *     )
     * )
     */
    public function active()
    {
        $arrParams = request(['order_code']);
        if (!auth()->user()->full_access) {
            $arrParams["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->changeStatusByOrderCode($arrParams, 2));
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
     *                 required={"order_code"},
     *                 @OA\Property(property="order_code", type="string", example="K3JD92LQ0XPA7TVM", description="Order code returned when the account was created. The account must belong to the authenticated user."),
     *                 @OA\Property(property="sign", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="error_code", type="integer", example=0, description="<b>0</b>: success. Other values: error (404 not found / not configured, 414 invalid payload, 401 unauthorized)."),
     *             @OA\Property(property="message", type="string", example="success", description="Result message."),
     *             @OA\Property(property="system_time", type="string", example="2026-10-20 10:00:00", description="Server time."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), description="Error messages. Empty on success."),
     *             @OA\Property(property="data", type="object", description="Empty object on success."),
     *             @OA\Examples(example="result_error", value={"error_code":404,"system_time":"2023-11-24 11:15:59","message":"","errors":{"Virtual account not found."},"data":null}, summary="Result error."),
     *             @OA\Examples(example="result_success", value={"error_code": 0,"message": "success","system_time":"2023-11-24 11:15:59","errors":{},"data":{}}, summary="Result success."),
     *         )
     *     )
     * )
     */
    public function deactivate()
    {
        $arrParams = request(['order_code']);
        if (!auth()->user()->full_access) {
            $arrParams["user_id"] = auth()->user()->id;
        }
        return response()->json($this->userVirtualAccountService->changeStatusByOrderCode($arrParams, 1));
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