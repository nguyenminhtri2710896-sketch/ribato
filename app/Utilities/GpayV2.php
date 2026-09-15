<?php

namespace App\Utilities;

use Curl\Curl;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class GpayV2
 * 
 * Thư viện tích hợp TOÀN DIỆN 3 dịch vụ chính của GPAY (Open API V1):
 * 1. Cổng thanh toán All-in-one: https://docs.g-pay.vn/vi/api-docs/payment-gateway
 * 2. Dịch vụ hỗ trợ thu hộ (Virtual Account - VA): https://docs.g-pay.vn/vi/api-docs/virtual-account
 * 3. Dịch vụ hỗ trợ chi hộ (Fund Transfer / Payout): https://docs.g-pay.vn/vi/api-docs/fund-transfer
 * 4. Quản lý Token & Bảo mật: https://docs.g-pay.vn/vi/api-docs/token
 * 
 * @package App\Utilities
 */
class GpayV2
{
    /**
     * URL Endpoint môi trường
     */
    const ENV_SANDBOX    = 'sandbox';
    const ENV_PRODUCTION = 'production';

    const URL_SANDBOX    = 'https://openapi-sandbox.g-pay.vn/v1';
    const URL_PRODUCTION = 'https://openapi.g-pay.vn/v1';

    /**
     * Phương thức thanh toán cổng Payment Gateway
     */
    const METHOD_ALL                = '';
    const METHOD_BANK_ATM           = 'BANK_ATM';
    const METHOD_BANK_INTERNATIONAL = 'BANK_INTERNATIONAL';
    const METHOD_QR_PAYMENT         = 'QR_PAYMENT';

    /**
     * Loại tài khoản Virtual Account (VA)
     * - O: Tài khoản dùng 1 lần (One-time, tự đóng sau khi nhận tiền)
     * - M: Tài khoản dùng nhiều lần (Multiple)
     */
    const VA_TYPE_ONE_TIME = 'O';
    const VA_TYPE_MULTIPLE = 'M';

    /**
     * Loại định danh Map Type cho VA
     */
    const MAP_TYPE_CMND        = 'CMND';
    const MAP_TYPE_CCCD        = 'CCCD';
    const MAP_TYPE_PASSPORT    = 'PASSPORT';
    const MAP_TYPE_MHD         = 'MHD';
    const MAP_TYPE_CUSTOMER_ID = 'CUSTOMER_ID';
    const MAP_TYPE_EMAIL       = 'EMAIL';
    const MAP_TYPE_PHONE       = 'PHONE_NUMBER';

    /**
     * Loại đối tượng nhận chi hộ (Payout Target Type)
     */
    const PAYOUT_TYPE_ACCOUNT_NUMBER = 'ACCOUNT_NUMBER';
    const PAYOUT_TYPE_CARD_NUMBER    = 'CARD_NUMBER';

    /**
     * Loại chuyển tiền chi hộ (Transfer Type)
     * - w2b: Chuyển từ ví/ngăn cash sang tài khoản ngân hàng / số thẻ
     * - w2w: Chuyển từ ví/ngăn cash sang ví Gpay
     */
    const TRANSFER_TYPE_W2B = 'w2b';
    const TRANSFER_TYPE_W2W = 'w2w';

    /**
     * Ngân hàng hỗ trợ mở tài khoản ảo VA
     */
    const BANK_BIDV = 'BIDV';
    const BANK_TCB  = 'TCB';
    const BANK_MSB  = 'MSB';
    const BANK_VCCB = 'VCCB';
    const BANK_VPB  = 'VPB';
    const BANK_WOO  = 'WOO';

    /**
     * Loại thanh toán
     */
    const PAYMENT_TYPE_IMMEDIATE = 'IMMEDIATE';

    /**
     * Trạng thái đơn hàng / Chi hộ / VA
     */
    const STATUS_ORDER_SUCCESS    = 'ORDER_SUCCESS';
    const STATUS_ORDER_PENDING    = 'ORDER_PENDING';
    const STATUS_ORDER_FAILED     = 'ORDER_FAILED';
    const STATUS_ORDER_VERIFYING  = 'ORDER_VERIFYING';
    const STATUS_ORDER_PROCESSING = 'ORDER_PROCESSING';

    const VA_STATUS_OPEN          = 'OPEN';
    const VA_STATUS_CLOSE         = 'CLOSE';

    const ACTION_CHANGE_BALANCE   = 'CHANGE_BALANCE';

    /**
     * @var string Môi trường kết nối (sandbox / production)
     */
    private $environment = self::ENV_SANDBOX;

    /**
     * @var string Base API URL
     */
    private $baseUrl = self::URL_SANDBOX;

    /**
     * @var string Merchant Code do Gpay cấp (dùng cho VA / VietQR / Chi hộ)
     */
    private $merchantCode = '';

    /**
     * @var string Client ID (API Key do Gpay cấp)
     */
    private $clientId = '';

    /**
     * @var string Client Secret do Gpay cấp
     */
    private $clientSecret = '';

    /**
     * @var string XCertificate (Certificate định danh của Merchant, không chứa header/footer/khoảng trắng)
     */
    private $certificate = '';

    /**
     * @var string Merchant Private Key (dùng ký số RSA-SHA256)
     */
    private $privateKey = '';

    /**
     * @var string Gpay Public Key (dùng verify chữ ký số từ Gpay)
     */
    private $gpayPublicKey = '';

    /**
     * @var int Thời gian timeout request (giây)
     */
    private $timeout = 60;

    /**
     * @var bool Bật/tắt ghi log debug
     */
    private $enableLog = true;

    /**
     * @var string|null Access Token được cache tạm thời
     */
    private $accessToken = null;

    /**
     * @var Curl|null Instance cURL client
     */
    private $curl = null;

    /**
     * GpayV2 constructor.
     *
     * @param array $config Cấu hình khởi tạo tùy chọn
     */
    public function __construct(array $config = [])
    {
        $this->initCurl();

        if (!empty($config)) {
            $this->configure($config);
        } else {
            // Mặc định nạp từ config Laravel / .env nếu có
            $this->setEnvironment(config('services.gpay.environment', env('GPAY_ENVIRONMENT', self::ENV_SANDBOX)));
            $this->setMerchantCode(config('services.gpay.merchant_code', env('GPAY_MERCHANT_CODE', '')));
            $this->setClientId(config('services.gpay.client_id', env('GPAY_CLIENT_ID', '')));
            $this->setClientSecret(config('services.gpay.client_secret', env('GPAY_CLIENT_SECRET', '')));
            $this->setCertificate(config('services.gpay.certificate', env('GPAY_CERTIFICATE', '')));
            $this->setPrivateKey(config('services.gpay.private_key', env('GPAY_PRIVATE_KEY', '')));
            $this->setGpayPublicKey(config('services.gpay.public_key', env('GPAY_PUBLIC_KEY', '')));
        }
    }

    /**
     * Khởi tạo instance mới (Fluent Static Factory)
     *
     * @param array $config
     * @return static
     */
    public static function make(array $config = []): self
    {
        return new static($config);
    }

    /**
     * Nạp cấu hình từ mảng
     *
     * @param array $config
     * @return $this
     */
    public function configure(array $config): self
    {
        if (isset($config['environment'])) {
            $this->setEnvironment($config['environment']);
        }
        if (isset($config['merchant_code'])) {
            $this->setMerchantCode($config['merchant_code']);
        }
        if (isset($config['client_id'])) {
            $this->setClientId($config['client_id']);
        }
        if (isset($config['client_secret'])) {
            $this->setClientSecret($config['client_secret']);
        }
        if (isset($config['certificate'])) {
            $this->setCertificate($config['certificate']);
        }
        if (isset($config['private_key'])) {
            $this->setPrivateKey($config['private_key']);
        }
        if (isset($config['gpay_public_key'])) {
            $this->setGpayPublicKey($config['gpay_public_key']);
        }
        if (isset($config['timeout'])) {
            $this->setTimeout((int)$config['timeout']);
        }
        if (isset($config['enable_log'])) {
            $this->setEnableLog((bool)$config['enable_log']);
        }

        return $this;
    }

    /* =========================================================================
     * GETTER / SETTER METHODS
     * ========================================================================= */

    public function setEnvironment(string $environment): self
    {
        $this->environment = strtolower($environment);
        if ($this->environment === self::ENV_PRODUCTION) {
            $this->baseUrl = self::URL_PRODUCTION;
        } else {
            $this->environment = self::ENV_SANDBOX;
            $this->baseUrl = self::URL_SANDBOX;
        }
        return $this;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function setBaseUrl(string $url): self
    {
        $this->baseUrl = rtrim($url, '/');
        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setMerchantCode(string $code): self
    {
        $this->merchantCode = trim($code);
        return $this;
    }

    public function getMerchantCode(): string
    {
        return $this->merchantCode;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = trim($clientId);
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientSecret(string $clientSecret): self
    {
        $this->clientSecret = trim($clientSecret);
        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setCertificate(string $certificate): self
    {
        $this->certificate = $this->cleanCertificate($certificate);
        return $this;
    }

    public function getCertificate(): string
    {
        return $this->certificate;
    }

    public function setPrivateKey(string $privateKey): self
    {
        $this->privateKey = $this->formatPrivateKey($privateKey);
        return $this;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function setGpayPublicKey(string $publicKey): self
    {
        $this->gpayPublicKey = $this->formatPublicKey($publicKey);
        return $this;
    }

    public function getGpayPublicKey(): string
    {
        return $this->gpayPublicKey;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        if ($this->curl) {
            $this->curl->setTimeout($timeout);
            $this->curl->setConnectTimeout($timeout);
        }
        return $this;
    }

    public function setEnableLog(bool $enable): self
    {
        $this->enableLog = $enable;
        return $this;
    }

    public function setAccessToken(?string $token): self
    {
        $this->accessToken = $token;
        return $this;
    }

    /* =========================================================================
     * CORE SECURITY & AUTHENTICATION (RSA-SHA256, TOKEN, HEADERS)
     * ========================================================================= */

    /**
     * Lấy Access Token từ Gpay API (POST /v1/auth/token)
     * Token được tự động lưu vào Cache Laravel để tái sử dụng theo TTL
     *
     * @param bool $forceRefresh Bắt buộc lấy mới không dùng cache
     * @return array
     */
    public function getAccessToken(bool $forceRefresh = false): array
    {
        if (!$forceRefresh && !empty($this->accessToken)) {
            return [
                'success'      => true,
                'access_token' => $this->accessToken,
                'token_type'   => 'Bearer',
                'message'      => 'Token from instance property'
            ];
        }

        $cacheKey = 'gpay_v2_token_' . md5($this->clientId . '_' . $this->environment);

        if (!$forceRefresh && Cache::has($cacheKey)) {
            $cachedToken = Cache::get($cacheKey);
            if (!empty($cachedToken)) {
                $this->accessToken = $cachedToken;
                return [
                    'success'      => true,
                    'access_token' => $cachedToken,
                    'token_type'   => 'Bearer',
                    'message'      => 'Token from cache'
                ];
            }
        }

        try {
            $endpoint = $this->baseUrl . '/auth/token';
            $payload  = [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ];

            $this->initCurl();
            $this->curl->setHeader('Content-Type', 'application/json');
            $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
            $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);

            $this->logInfo('GPAY_V2_GET_TOKEN_REQUEST', [
                'endpoint'  => $endpoint,
                'client_id' => $this->clientId
            ]);

            $this->curl->post($endpoint, json_encode($payload));

            if ($this->curl->error) {
                $errMsg = "Lỗi kết nối Gpay Auth: {$this->curl->errorCode} - {$this->curl->errorMessage}";
                $this->logError('GPAY_V2_GET_TOKEN_ERROR', ['error' => $errMsg, 'raw' => $this->curl->rawResponse]);
                return [
                    'success'      => false,
                    'message'      => $errMsg,
                    'error_code'   => $this->curl->errorCode,
                    'access_token' => null
                ];
            }

            $res = json_decode($this->curl->rawResponse, true);
            $this->logInfo('GPAY_V2_GET_TOKEN_RESPONSE', ['response' => $res]);

            $code = $res['meta']['code'] ?? 'ERR';
            if ((string)$code === '200' && !empty($res['data']['access_token'])) {
                $token     = $res['data']['access_token'];
                $expiresIn = (int)($res['data']['expires_in'] ?? 17900);

                // Cache token (trừ 60s an toàn)
                $ttlMinutes = max(1, floor(($expiresIn - 60) / 60));
                Cache::put($cacheKey, $token, now()->addMinutes($ttlMinutes));

                $this->accessToken = $token;

                return [
                    'success'      => true,
                    'access_token' => $token,
                    'expires_in'   => $expiresIn,
                    'token_type'   => $res['data']['token_type'] ?? 'Bearer',
                    'scope'        => $res['data']['scope'] ?? '',
                    'message'      => 'Lấy Access Token thành công'
                ];
            }

            $msg = $res['meta']['message'] ?? ($res['meta']['msg'] ?? 'Không lấy được access token');
            return [
                'success'      => false,
                'message'      => $msg,
                'error'        => $res['meta']['error'] ?? null,
                'access_token' => null,
                'raw'          => $res
            ];
        } catch (\Exception $e) {
            $this->logError('GPAY_V2_GET_TOKEN_EXCEPTION', ['exception' => $e->getMessage()]);
            return [
                'success'      => false,
                'message'      => 'Exception: ' . $e->getMessage(),
                'access_token' => null
            ];
        }
    }

    /**
     * Sinh chữ ký số RSA-SHA256 theo đặc tả Gpay
     * Chuỗi ký raw: `x-timestamp + x-requests-id + json(body)`
     *
     * @param string $timestamp Timestamp tại thời điểm gửi request
     * @param string $requestId Request ID duy nhất
     * @param string|array $body Payload gửi đi (dạng JSON hoặc array)
     * @return string Chữ ký Base64
     * @throws \Exception
     */
    public function generateSignature(string $timestamp, string $requestId, $body): string
    {
        $bodyJson = is_array($body) ? json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string)$body;
        $rawData  = $timestamp . $requestId . $bodyJson;

        $pKeyResource = openssl_pkey_get_private($this->privateKey);
        if (!$pKeyResource) {
            throw new \Exception('Merchant Private Key không hợp lệ hoặc chưa được cấu hình.');
        }

        $binarySignature = '';
        $success = openssl_sign($rawData, $binarySignature, $pKeyResource, OPENSSL_ALGO_SHA256);

        if (!$success) {
            throw new \Exception('Lỗi sinh chữ ký OpenSSL RSA-SHA256: ' . openssl_error_string());
        }

        return base64_encode($binarySignature);
    }

    /**
     * Kiểm tra chữ ký số từ Gpay gửi về (API Response / Payment Gateway Webhook)
     *
     * @param string $timestamp
     * @param string $requestId
     * @param string|array $body
     * @param string $signature Base64 signature
     * @return bool
     */
    public function verifySignature(string $timestamp, string $requestId, $body, string $signature): bool
    {
        if (empty($signature) || empty($this->gpayPublicKey)) {
            return false;
        }

        $bodyJson = is_array($body) ? json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string)$body;
        $rawData  = $timestamp . $requestId . $bodyJson;

        $pubKeyResource = openssl_pkey_get_public($this->gpayPublicKey);
        if (!$pubKeyResource) {
            $this->logError('GPAY_V2_VERIFY_SIGN_ERR', ['error' => 'Gpay Public Key không hợp lệ']);
            return false;
        }

        $decodedSig = base64_decode($signature);
        $result = openssl_verify($rawData, $decodedSig, $pubKeyResource, OPENSSL_ALGO_SHA256);

        return $result === 1;
    }

    /**
     * Kiểm tra chữ ký số Webhook Dịch vụ Thu hộ Virtual Account (VA Change Balance)
     * Định dạng raw string:
     * `gpay_trans_id=...&bank_trace_id=...&bank_transaction_id=...&account_number=...&amount=...&message=...&action=...`
     *
     * @param array $payload Dữ liệu webhook nhận được từ Gpay
     * @param string|null $signature Chữ ký số truyền vào (hoặc lấy từ $payload['signature'])
     * @return bool
     */
    public function verifyVaWebhookSignature(array $payload, ?string $signature = null): bool
    {
        $sig = $signature ?: ($payload['signature'] ?? '');
        if (empty($sig) || empty($this->gpayPublicKey)) {
            return false;
        }

        $rawString = sprintf(
            'gpay_trans_id=%s&bank_trace_id=%s&bank_transaction_id=%s&account_number=%s&amount=%s&message=%s&action=%s',
            $payload['gpay_trans_id'] ?? '',
            $payload['bank_trace_id'] ?? '',
            $payload['bank_transaction_id'] ?? '',
            $payload['account_number'] ?? '',
            $payload['amount'] ?? '',
            $payload['message'] ?? '',
            $payload['action'] ?? self::ACTION_CHANGE_BALANCE
        );

        $pubKeyResource = openssl_pkey_get_public($this->gpayPublicKey);
        if (!$pubKeyResource) {
            $this->logError('GPAY_V2_VA_VERIFY_ERR', ['error' => 'Gpay Public Key không hợp lệ']);
            return false;
        }

        $result = openssl_verify($rawString, base64_decode($sig), $pubKeyResource, OPENSSL_ALGO_SHA256);
        return $result === 1;
    }

    /**
     * Build bộ Security Headers chuẩn cho các API Gpay
     *
     * @param string|array $body
     * @param string|null $requestId
     * @param string|null $timestamp
     * @return array
     * @throws \Exception
     */
    public function buildSecurityHeaders($body, ?string $requestId = null, ?string $timestamp = null): array
    {
        $tokenRes = $this->getAccessToken();
        if (!$tokenRes['success'] || empty($tokenRes['access_token'])) {
            throw new \Exception('Không thể lấy Bearer Token từ Gpay: ' . ($tokenRes['message'] ?? 'Unknown error'));
        }

        $requestId = $requestId ?: (string)Str::uuid();
        $timestamp = $timestamp ?: (string)time();
        $bodyJson  = is_array($body) ? json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string)$body;

        $signature = $this->generateSignature($timestamp, $requestId, $bodyJson);

        return [
            'Authorization' => 'Bearer ' . $tokenRes['access_token'],
            'x-certificate' => $this->certificate,
            'x-requests-id' => $requestId,
            'x-timestamp'   => $timestamp,
            'signature'     => $signature,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json'
        ];
    }

    /**
     * Gửi request POST có bảo mật chữ ký số tới Gpay API
     *
     * @param string $path Đường dẫn API tương đối (VD: '/payouts/instant/transfer-to-bank')
     * @param array $body Dữ liệu JSON payload
     * @param string|null $customRequestId
     * @return array
     */
    private function postSecure(string $path, array $body = [], ?string $customRequestId = null): array
    {
        try {
            $endpoint  = $this->baseUrl . '/' . ltrim($path, '/');
            $requestId = $customRequestId ?: ((string)Str::uuid());
            $headers   = $this->buildSecurityHeaders($body, $requestId);

            $this->initCurl();
            foreach ($headers as $k => $v) {
                $this->curl->setHeader($k, $v);
            }
            $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
            $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);

            $this->logInfo('GPAY_V2_POST_REQUEST', [
                'endpoint' => $endpoint,
                'headers'  => $this->maskHeaders($headers),
                'body'     => $body
            ]);

            $this->curl->post($endpoint, json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            if ($this->curl->error) {
                $errMsg = "Lỗi kết nối Gpay ({$path}): {$this->curl->errorCode} - {$this->curl->errorMessage}";
                $this->logError('GPAY_V2_CURL_ERROR', ['error' => $errMsg, 'raw' => $this->curl->rawResponse]);
                return [
                    'success'    => false,
                    'message'    => $errMsg,
                    'error_code' => $this->curl->errorCode,
                    'raw'        => $this->curl->rawResponse
                ];
            }

            $res  = json_decode($this->curl->rawResponse, true);
            $this->logInfo('GPAY_V2_POST_RESPONSE', ['path' => $path, 'response' => $res]);

            $code = $res['meta']['code'] ?? 'ERR';
            if ((string)$code === '200') {
                return [
                    'success' => true,
                    'code'    => 200,
                    'message' => $res['meta']['msg'] ?? ($res['meta']['message'] ?? 'Thành công'),
                    'data'    => $res['data'] ?? [],
                    'meta'    => $res['meta'] ?? [],
                    'raw'     => $res
                ];
            }

            $msg = $res['meta']['msg'] ?? ($res['meta']['message'] ?? 'Thao tác thất bại');
            return [
                'success' => false,
                'code'    => $code,
                'message' => $msg,
                'error'   => $res['meta']['error'] ?? null,
                'data'    => $res['data'] ?? [],
                'raw'     => $res
            ];
        } catch (\Exception $e) {
            $this->logError('GPAY_V2_EXCEPTION', ['path' => $path, 'exception' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Gửi request GET có Bearer token tới Gpay API
     *
     * @param string $path
     * @return array
     */
    private function getSecure(string $path): array
    {
        try {
            $endpoint = $this->baseUrl . '/' . ltrim($path, '/');
            $tokenRes = $this->getAccessToken();
            if (!$tokenRes['success']) {
                return ['success' => false, 'message' => 'Không lấy được access token'];
            }

            $this->initCurl();
            $this->curl->setHeader('Authorization', 'Bearer ' . $tokenRes['access_token']);
            $this->curl->setHeader('Accept', 'application/json');
            $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
            $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);

            $this->logInfo('GPAY_V2_GET_REQUEST', ['endpoint' => $endpoint]);

            $this->curl->get($endpoint);

            if ($this->curl->error) {
                $errMsg = "Lỗi kết nối Gpay GET ({$path}): {$this->curl->errorCode} - {$this->curl->errorMessage}";
                $this->logError('GPAY_V2_GET_ERROR', ['error' => $errMsg, 'raw' => $this->curl->rawResponse]);
                return [
                    'success'    => false,
                    'message'    => $errMsg,
                    'error_code' => $this->curl->errorCode,
                    'raw'        => $this->curl->rawResponse
                ];
            }

            $res = json_decode($this->curl->rawResponse, true);
            $this->logInfo('GPAY_V2_GET_RESPONSE', ['path' => $path, 'response' => $res]);

            $code = $res['meta']['code'] ?? 'ERR';
            if ((string)$code === '200') {
                return [
                    'success' => true,
                    'code'    => 200,
                    'message' => $res['meta']['msg'] ?? ($res['meta']['message'] ?? 'Thành công'),
                    'data'    => $res['data'] ?? [],
                    'meta'    => $res['meta'] ?? [],
                    'raw'     => $res
                ];
            }

            return [
                'success' => false,
                'code'    => $code,
                'message' => $res['meta']['msg'] ?? 'Thao tác thất bại',
                'error'   => $res['meta']['error'] ?? null,
                'data'    => $res['data'] ?? [],
                'raw'     => $res
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /* =========================================================================
     * 1. DỊCH VỤ HỖ TRỢ CHI HỘ (FUND TRANSFER / PAYOUT)
     * Tài liệu: https://docs.g-pay.vn/vi/api-docs/fund-transfer
     * ========================================================================= */

    /**
     * 1.1. API Lấy thông tin số dư Merchant (GET /v1/account/information)
     * Trả về số dư khả dụng ngăn Cash (để chi hộ/rút tiền), số dư tối thiểu, thông tin nạp tiền ngăn Cash
     *
     * @return array
     */
    public function getAccountInformation(): array
    {
        $res = $this->getSecure('/account/information');
        if ($res['success'] && !empty($res['data'])) {
            $data = $res['data'];
            return [
                'success'             => true,
                'code'                => 200,
                'message'             => 'Lấy thông tin số dư thành công',
                'amount_cash'         => (int)($data['amount_cash'] ?? 0),         // Số dư khả dụng chi hộ / rút tiền
                'amount_minimum'      => (int)($data['amount_minimum'] ?? 0),      // Số dư tối thiểu
                'amount_refund'       => (int)($data['amount_refund'] ?? 0),       // Số tiền hoàn
                'amount_revenue'      => (int)($data['amount_revenue'] ?? 0),      // Doanh thu
                'cash_in_information' => $data['cash_in_information'] ?? [],      // Tài khoản nạp tiền ngăn Cash
                'data'                => $data,
                'meta'                => $res['meta'] ?? []
            ];
        }

        return $res;
    }

    /**
     * 1.2. API Lấy thông tin Sub Merchant (POST /v1/account/sub-accounts)
     *
     * @param array $params ['list_sub' => [], 'page' => 1, 'per_page' => 100]
     * @return array
     */
    public function getSubAccounts(array $params = []): array
    {
        $body = [
            'list_sub' => (array)($params['list_sub'] ?? []),
            'page'     => (int)($params['page'] ?? 1),
            'per_page' => (int)($params['per_page'] ?? 100)
        ];

        return $this->postSecure('/account/sub-accounts', $body);
    }

    /**
     * 1.3. API Truy vấn thông tin tài khoản ngân hàng / số thẻ người nhận (POST /v1/payouts/bank-account/query)
     * Tra cứu tên chủ tài khoản/chủ thẻ trước khi thực hiện lệnh chuyển tiền chi hộ
     *
     * @param string $accountNumber Số tài khoản hoặc Số thẻ
     * @param string $bankCode Mã ngân hàng (ví dụ: 'VCB', 'TCB', 'BIDV', 'VCCB',...). Bắt buộc khi type = 'ACCOUNT_NUMBER'
     * @param string $type 'ACCOUNT_NUMBER' hoặc 'CARD_NUMBER'
     * @param string|null $requestId Mã giao dịch duy nhất
     * @return array ['success' => bool, 'full_name' => string, 'order_id' => string, 'status' => string]
     */
    public function queryBankAccount(string $accountNumber, string $bankCode = '', string $type = self::PAYOUT_TYPE_ACCOUNT_NUMBER, ?string $requestId = null): array
    {
        $reqId = $requestId ?: ('INQ_' . date('YmdHis') . '_' . rand(1000, 9999));

        $body = [
            'account_number' => (string)$accountNumber,
            'request_id'     => (string)$reqId,
            'type'           => (string)$type
        ];

        if ($type === self::PAYOUT_TYPE_ACCOUNT_NUMBER && !empty($bankCode)) {
            $body['bank_code'] = (string)$bankCode;
        }

        $res = $this->postSecure('/payouts/bank-account/query', $body, $reqId);
        if ($res['success'] && !empty($res['data'])) {
            $data = $res['data'];
            return [
                'success'    => true,
                'code'       => 200,
                'message'    => 'Truy vấn tài khoản thành công',
                'full_name'  => $data['full_name'] ?? '',
                'order_id'   => $data['order_id'] ?? '',
                'status'     => $data['status'] ?? '',
                'request_id' => $reqId,
                'data'       => $data,
                'meta'       => $res['meta'] ?? []
            ];
        }

        return $res;
    }

    /**
     * 1.4. API Chuyển tiền tới tài khoản ngân hàng / số thẻ (POST /v1/payouts/instant/transfer-to-bank)
     * Thực hiện chi tiền từ ngăn Cash của Merchant tới tài khoản ngân hàng hoặc số thẻ thụ hưởng
     *
     * @param array $params
     *  - transaction_id (string, required): Mã giao dịch unique của merchant
     *  - account_number (string, required): Số tài khoản hoặc số thẻ nhận tiền
     *  - amount (int, required): Số tiền cần chuyển (VND)
     *  - full_name (string, required): Họ và tên chủ tài khoản/thẻ nhận tiền
     *  - bank_code (string, optional): Mã ngân hàng (Bắt buộc khi type = 'ACCOUNT_NUMBER')
     *  - type (string, required): 'ACCOUNT_NUMBER' hoặc 'CARD_NUMBER' (mặc định 'ACCOUNT_NUMBER')
     *  - message (string, optional): Nội dung chuyển tiền
     *  - map_id (string, optional): Mã tham chiếu (Hợp đồng, SĐT,...)
     *  - order_ref (string, optional): order_id lấy từ API queryBankAccount trước đó
     *
     * @return array Kết quả trả về gồm transfer_id, transfer_status (ORDER_SUCCESS, ORDER_FAILED, ORDER_PENDING,...)
     */
    public function transferToBank(array $params): array
    {
        $transId = (string)($params['transaction_id'] ?? ('W2B_' . date('YmdHis') . '_' . rand(1000, 9999)));

        $body = [
            'transaction_id' => $transId,
            'account_number' => (string)($params['account_number'] ?? ''),
            'amount'         => (int)($params['amount'] ?? 0),
            'full_name'      => (string)($params['full_name'] ?? ''),
            'type'           => (string)($params['type'] ?? self::PAYOUT_TYPE_ACCOUNT_NUMBER),
            'message'        => (string)($params['message'] ?? ('Chuyen tien ' . $transId))
        ];

        if (!empty($params['bank_code'])) {
            $body['bank_code'] = (string)$params['bank_code'];
        }
        if (!empty($params['map_id'])) {
            $body['map_id'] = (string)$params['map_id'];
        }
        if (!empty($params['order_ref'])) {
            $body['order_ref'] = (string)$params['order_ref'];
        }

        // Validation
        if ($body['amount'] <= 0) {
            return ['success' => false, 'message' => 'Số tiền chuyển (amount) phải lớn hơn 0'];
        }
        if (empty($body['account_number'])) {
            return ['success' => false, 'message' => 'account_number không được để trống'];
        }
        if (empty($body['full_name'])) {
            return ['success' => false, 'message' => 'full_name người nhận không được để trống'];
        }

        $res = $this->postSecure('/payouts/instant/transfer-to-bank', $body, $transId);
        if ($res['success'] && !empty($res['data'])) {
            $data   = $res['data'];
            $status = $data['transfer_status'] ?? '';
            return [
                'success'                      => true,
                'code'                         => 200,
                'message'                      => $res['meta']['msg'] ?? 'Lệnh chi hộ đã được xử lý',
                'transfer_id'                  => $data['transfer_id'] ?? '',
                'transfer_status'              => $status,
                'is_success'                   => ($status === self::STATUS_ORDER_SUCCESS),
                'is_failed'                    => ($status === self::STATUS_ORDER_FAILED),
                'is_pending'                   => in_array($status, [self::STATUS_ORDER_PENDING, self::STATUS_ORDER_PROCESSING, self::STATUS_ORDER_VERIFYING]),
                'transfer_created_time'        => $data['transfer_created_time'] ?? '',
                'transfer_status_updated_time' => $data['transfer_status_updated_time'] ?? '',
                'transaction_id'               => $transId,
                'data'                         => $data,
                'meta'                         => $res['meta'] ?? []
            ];
        }

        return $res;
    }

    /**
     * 1.5. API Chuyển tiền tới Ví điện tử GPAY (POST /v1/payouts/instant/transfer-to-wallet)
     *
     * @param string $phoneNumber Số điện thoại ví Gpay nhận tiền
     * @param int $amount Số tiền cần chuyển
     * @param string $message Nội dung chuyển tiền
     * @param string|null $transactionId Mã giao dịch unique
     * @return array
     */
    public function transferToWallet(string $phoneNumber, int $amount, string $message = '', ?string $transactionId = null): array
    {
        $transId = $transactionId ?: ('W2W_' . date('YmdHis') . '_' . rand(1000, 9999));

        $body = [
            'transaction_id' => $transId,
            'phone_number'   => (string)$phoneNumber,
            'amount'         => (int)$amount,
            'message'        => (string)($message ?: ('Chuyen tien ví Gpay ' . $transId))
        ];

        if ($body['amount'] <= 0) {
            return ['success' => false, 'message' => 'Số tiền chuyển phải lớn hơn 0'];
        }
        if (empty($body['phone_number'])) {
            return ['success' => false, 'message' => 'phone_number ví Gpay không được để trống'];
        }

        $res = $this->postSecure('/payouts/instant/transfer-to-wallet', $body, $transId);
        if ($res['success'] && !empty($res['data'])) {
            $data   = $res['data'];
            $status = $data['transfer_status'] ?? '';
            return [
                'success'                      => true,
                'code'                         => 200,
                'message'                      => $res['meta']['msg'] ?? 'Chuyển tiền ví Gpay thành công',
                'transfer_id'                  => $data['transfer_id'] ?? '',
                'transfer_status'              => $status,
                'is_success'                   => ($status === self::STATUS_ORDER_SUCCESS),
                'transfer_created_time'        => $data['transfer_created_time'] ?? '',
                'transfer_status_updated_time' => $data['transfer_status_updated_time'] ?? '',
                'transaction_id'               => $transId,
                'data'                         => $data,
                'meta'                         => $res['meta'] ?? []
            ];
        }

        return $res;
    }

    /**
     * 1.6. API Lấy danh sách ngân hàng hỗ trợ Chi hộ (GET /v1/reference/banks)
     *
     * @return array Danh sách ngân hàng gồm bank_code, bank_name, bank_bin, logo
     */
    public function getBankList(): array
    {
        $res = $this->getSecure('/reference/banks');
        if ($res['success'] && isset($res['data'])) {
            return [
                'success' => true,
                'code'    => 200,
                'message' => 'Lấy danh sách ngân hàng thành công',
                'banks'   => $res['data'],
                'data'    => $res['data']
            ];
        }

        return $res;
    }

    /**
     * 1.7. API Truy vấn chi tiết giao dịch Chi hộ theo transaction_id của Merchant
     * Endpoint: GET /v1/reporting/transactions/{transfer_type}/{transaction_id}
     *
     * @param string $transactionId Mã giao dịch của merchant khi gửi lệnh chi hộ
     * @param string $transferType 'w2b' (chuyển bank/thẻ) hoặc 'w2w' (chuyển ví)
     * @return array
     */
    public function queryPayoutTransaction(string $transactionId, string $transferType = self::TRANSFER_TYPE_W2B): array
    {
        $path = "/reporting/transactions/{$transferType}/{$transactionId}";
        $res  = $this->getSecure($path);

        if ($res['success'] && !empty($res['data'])) {
            $data   = $res['data'];
            $status = $data['transfer_status'] ?? '';
            return [
                'success'                      => true,
                'code'                         => 200,
                'status'                       => $status,
                'is_success'                   => ($status === self::STATUS_ORDER_SUCCESS),
                'is_failed'                    => ($status === self::STATUS_ORDER_FAILED),
                'is_pending'                   => in_array($status, [self::STATUS_ORDER_PENDING, self::STATUS_ORDER_PROCESSING, self::STATUS_ORDER_VERIFYING]),
                'transfer_id'                  => $data['transfer_id'] ?? '',
                'transaction_id'               => $transactionId,
                'amount'                       => (int)($data['amount'] ?? 0),
                'fee_amount'                   => (int)($data['fee_amount'] ?? 0),
                'full_name'                    => $data['full_name'] ?? '',
                'bank_received'                => $data['bank_received'] ?? '',
                'account_number'               => $data['account_number'] ?? '',
                'transfer_created_time'        => $data['transfer_created_time'] ?? '',
                'transfer_status_updated_time' => $data['transfer_status_updated_time'] ?? '',
                'data'                         => $data,
                'meta'                         => $res['meta'] ?? []
            ];
        }

        return $res;
    }

    /* =========================================================================
     * 2. DỊCH VỤ HỖ TRỢ THU HỘ (VIRTUAL ACCOUNT - VA)
     * Tài liệu: https://docs.g-pay.vn/vi/api-docs/virtual-account
     * ========================================================================= */

    /**
     * 2.1. API Tạo Virtual Account (POST /v1/collection/va/create)
     * 
     * @param array $params
     * @return array
     */
    public function createVirtualAccount(array $params): array
    {
        $body = [
            'account_name'     => (string)($params['account_name'] ?? ''),
            'account_type'     => (string)($params['account_type'] ?? self::VA_TYPE_MULTIPLE),
            'bank_code'        => (string)($params['bank_code'] ?? self::BANK_BIDV),
            'map_id'           => (string)($params['map_id'] ?? ('CUST_' . time())),
            'map_type'         => (string)($params['map_type'] ?? self::MAP_TYPE_CUSTOMER_ID),
            'customer_address' => (string)($params['customer_address'] ?? ''),
            'description'      => (string)($params['description'] ?? '')
        ];

        if (isset($params['equal_amount']) && $params['equal_amount'] > 0) {
            $body['equal_amount'] = (int)$params['equal_amount'];
        }
        if (isset($params['min_amount']) && $params['min_amount'] > 0) {
            $body['min_amount'] = (int)$params['min_amount'];
        }
        if (isset($params['max_amount']) && $params['max_amount'] > 0) {
            $body['max_amount'] = (int)$params['max_amount'];
        }

        if (empty($body['account_name'])) {
            return ['success' => false, 'message' => 'account_name không được để trống'];
        }
        if (empty($body['map_id'])) {
            return ['success' => false, 'message' => 'map_id không được để trống'];
        }

        $res = $this->postSecure('/collection/va/create', $body);
        if ($res['success'] && !empty($res['data'])) {
            $data = $res['data'];
            return [
                'success'        => true,
                'code'           => 200,
                'message'        => 'Tạo tài khoản ảo thành công',
                'account_number' => $data['account_number'] ?? '',
                'account_name'   => $data['account_name'] ?? $body['account_name'],
                'account_type'   => $data['account_type'] ?? $body['account_type'],
                'balance'        => $data['balance'] ?? 0,
                'status'         => $data['status'] ?? self::VA_STATUS_OPEN,
                'qr_code'        => $data['qr_code'] ?? '',
                'qr_code_image'  => $data['qr_code_image'] ?? '',
                'start_at'       => $data['start_at'] ?? '',
                'expire_at'      => $data['expire_at'] ?? '',
                'data'           => $data,
                'meta'           => $res['meta'] ?? []
            ];
        }

        return $res;
    }

    /**
     * 2.2. API Lấy thông tin mã QR hình ảnh (GET /v1/collection/va/vietqr/{merchant_code}/{account_number})
     * 
     * @param string $accountNumber
     * @param string|null $merchantCode
     * @return array
     */
    public function getVietQRImage(string $accountNumber, ?string $merchantCode = null): array
    {
        $code = $merchantCode ?: $this->merchantCode;
        if (empty($code)) {
            return ['success' => false, 'message' => 'merchant_code không được để trống'];
        }

        try {
            $endpoint = $this->baseUrl . "/collection/va/vietqr/{$code}/{$accountNumber}";
            $tokenRes = $this->getAccessToken();
            if (!$tokenRes['success']) {
                return ['success' => false, 'message' => 'Không lấy được access token'];
            }

            $this->initCurl();
            $this->curl->setHeader('Authorization', 'Bearer ' . $tokenRes['access_token']);
            $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
            $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);

            $this->curl->get($endpoint);

            if ($this->curl->error) {
                return [
                    'success' => false,
                    'message' => "Lỗi lấy VietQR: {$this->curl->errorCode} - {$this->curl->errorMessage}"
                ];
            }

            $rawImage = $this->curl->rawResponse;
            return [
                'success'      => true,
                'image_binary' => $rawImage,
                'image_base64' => 'data:image/png;base64,' . base64_encode($rawImage)
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Lấy URL link ảnh VietQR của tài khoản ảo
     *
     * @param string $accountNumber
     * @param string|null $merchantCode
     * @return string
     */
    public function getVietQRUrl(string $accountNumber, ?string $merchantCode = null): string
    {
        $code = $merchantCode ?: $this->merchantCode;
        return $this->baseUrl . "/collection/va/vietqr/{$code}/{$accountNumber}";
    }

    /**
     * 2.3. API Cập nhật thông tin Virtual Account (POST /v1/collection/va/update)
     * 
     * @param array $params
     * @return array
     */
    public function updateVirtualAccount(array $params): array
    {
        $body = [
            'account_number' => (string)($params['account_number'] ?? ''),
            'account_name'   => (string)($params['account_name'] ?? '')
        ];

        if (!empty($params['account_type'])) {
            $body['account_type'] = (string)$params['account_type'];
        }
        if (!empty($params['map_id'])) {
            $body['map_id'] = (string)$params['map_id'];
        }
        if (isset($params['equal_amount'])) {
            $body['equal_amount'] = (int)$params['equal_amount'];
        }
        if (isset($params['min_amount'])) {
            $body['min_amount'] = (int)$params['min_amount'];
        }
        if (isset($params['max_amount'])) {
            $body['max_amount'] = (int)$params['max_amount'];
        }

        if (empty($body['account_number'])) {
            return ['success' => false, 'message' => 'account_number không được để trống'];
        }
        if (empty($body['account_name'])) {
            return ['success' => false, 'message' => 'account_name không được để trống'];
        }

        return $this->postSecure('/collection/va/update', $body);
    }

    /**
     * 2.4. API Truy vấn lịch sử giao dịch thu hộ (POST /v1/reporting/statement/collection)
     * 
     * @param array $params
     * @return array
     */
    public function getHistoryCollection(array $params): array
    {
        $body = [
            'from_date' => (int)($params['from_date'] ?? strtotime('-1 day')),
            'to_date'   => (int)($params['to_date'] ?? time()),
            'page'      => (int)($params['page'] ?? 1),
            'per_page'  => (int)($params['per_page'] ?? 50),
        ];

        if ($body['per_page'] < 10) {
            $body['per_page'] = 10;
        }
        if ($body['per_page'] > 200) {
            $body['per_page'] = 200;
        }

        return $this->postSecure('/reporting/statement/collection', $body);
    }

    /**
     * 2.5. API Đóng Virtual Account (POST /v1/collection/va/close)
     * 
     * @param string $accountNumber
     * @param string $closeReason
     * @return array
     */
    public function closeVirtualAccount(string $accountNumber, string $closeReason = ''): array
    {
        $body = [
            'account_number' => (string)$accountNumber,
            'close_reason'   => (string)$closeReason
        ];

        return $this->postSecure('/collection/va/close', $body);
    }

    /**
     * 2.6. API Truy vấn chi tiết Virtual Account (POST /v1/collection/va/detail)
     * 
     * @param string $accountNumber
     * @return array
     */
    public function getDetailVirtualAccount(string $accountNumber): array
    {
        $body = [
            'account_number' => (string)$accountNumber
        ];

        return $this->postSecure('/collection/va/detail', $body);
    }

    /**
     * 2.7. API Re-Open mở lại Virtual Account (POST /v1/collection/va/re-open)
     * 
     * @param string $accountNumber
     * @return array
     */
    public function reopenVirtualAccount(string $accountNumber): array
    {
        $body = [
            'account_number' => (string)$accountNumber
        ];

        return $this->postSecure('/collection/va/re-open', $body);
    }

    /**
     * 2.8. Xử lý & Xác thực Webhook Thay đổi số dư tài khoản ảo (VA Balance Change Webhook)
     *
     * @param array $payload
     * @return array
     */
    public function handleVaWebhook(array $payload): array
    {
        $this->logInfo('GPAY_V2_VA_WEBHOOK_RECEIVED', $payload);

        $action = $payload['action'] ?? '';
        if ($action !== self::ACTION_CHANGE_BALANCE && $action !== 'CHANGE_BALANCE') {
            return [
                'valid'      => false,
                'message'    => 'Action webhook không phải CHANGE_BALANCE',
                'data'       => $payload
            ];
        }

        if (!empty($this->gpayPublicKey)) {
            $isValid = $this->verifyVaWebhookSignature($payload);
            if (!$isValid) {
                $this->logError('GPAY_V2_VA_WEBHOOK_SIGN_INVALID', ['payload' => $payload]);
                return [
                    'valid'   => false,
                    'message' => 'Chữ ký webhook VA không hợp lệ',
                    'data'    => $payload
                ];
            }
        }

        return [
            'valid'               => true,
            'is_success'          => true,
            'message'             => 'Webhook VA hợp lệ',
            'gpay_trans_id'       => $payload['gpay_trans_id'] ?? '',
            'bank_transaction_id' => $payload['bank_transaction_id'] ?? '',
            'bank_trace_id'       => $payload['bank_trace_id'] ?? '',
            'account_number'      => $payload['account_number'] ?? '',
            'amount'              => (int)($payload['amount'] ?? 0),
            'message_trans'       => $payload['message'] ?? '',
            'sender_name'         => $payload['sender_name'] ?? '',
            'sender_account'      => $payload['sender_account'] ?? '',
            'sender_bank_bin'     => $payload['sender_bank_bin'] ?? '',
            'merchant_code'       => $payload['merchant_code'] ?? '',
            'data'                => $payload
        ];
    }

    /* =========================================================================
     * 3. CỔNG THANH TOÁN ALL-IN-ONE (PAYMENT GATEWAY)
     * Tài liệu: https://docs.g-pay.vn/vi/api-docs/payment-gateway
     * ========================================================================= */

    /**
     * 3.1. API Init Order - Tạo đơn hàng thanh toán trên Cổng Gpay
     * Endpoint: POST /v1/payments/gateway/init-order
     *
     * @param array $params
     * @return array
     */
    public function initOrder(array $params): array
    {
        $requestId = $params['request_id'] ?? ('GPAY_' . date('YmdHis') . '_' . rand(1000, 9999));

        $body = [
            'request_id'     => (string)$requestId,
            'amount'         => (int)($params['amount'] ?? 0),
            'callback_url'   => (string)($params['callback_url'] ?? ''),
            'webhook_url'    => (string)($params['webhook_url'] ?? $params['callback_url'] ?? ''),
            'customer_id'    => (string)($params['customer_id'] ?? 'GUEST_' . time()),
            'embed_data'     => (string)(is_array($params['embed_data'] ?? '') ? json_encode($params['embed_data']) : ($params['embed_data'] ?? $requestId)),
            'payment_type'   => (string)($params['payment_type'] ?? self::PAYMENT_TYPE_IMMEDIATE),
            'customer_name'  => (string)($params['customer_name'] ?? ''),
            'email'          => (string)($params['email'] ?? ''),
            'phone'          => (string)($params['phone'] ?? ''),
            'address'        => (string)($params['address'] ?? ''),
            'title'          => (string)($params['title'] ?? 'Thanh toan don hang ' . $requestId),
            'description'    => (string)($params['description'] ?? ('Payment for order ' . $requestId)),
            'payment_method' => (string)($params['payment_method'] ?? self::METHOD_ALL)
        ];

        if ($body['amount'] <= 0) {
            return ['success' => false, 'message' => 'Số tiền (amount) phải lớn hơn 0'];
        }
        if (empty($body['callback_url'])) {
            return ['success' => false, 'message' => 'callback_url không được để trống'];
        }
        if (empty($body['webhook_url'])) {
            return ['success' => false, 'message' => 'webhook_url không được để trống'];
        }

        $res = $this->postSecure('/payments/gateway/init-order', $body, $requestId);
        if ($res['success'] && !empty($res['data'])) {
            return [
                'success'      => true,
                'code'         => 200,
                'message'      => $res['meta']['msg'] ?? 'Khởi tạo đơn hàng thành công',
                'bill_id'      => $res['data']['bill_id'] ?? '',
                'bill_url'     => $res['data']['bill_url'] ?? '',
                'expired_time' => $res['data']['expired_time'] ?? '',
                'request_id'   => $res['data']['request_id'] ?? $requestId,
                'data'         => $res['data'],
                'meta'         => $res['meta'] ?? []
            ];
        }

        return $res;
    }

    /**
     * 3.2. API Query Order - Truy vấn trạng thái đơn hàng Cổng thanh toán
     * Endpoint: POST /v1/payments/gateway/query-order
     *
     * @param string $gpayBillId
     * @param string|null $merchantOrderId
     * @return array
     */
    public function queryOrder(string $gpayBillId, ?string $merchantOrderId = ''): array
    {
        $body = [
            'gpay_bill_id'      => (string)$gpayBillId,
            'merchant_order_id' => (string)$merchantOrderId
        ];

        $res = $this->postSecure('/payments/gateway/query-order', $body);
        if ($res['success'] && !empty($res['data'])) {
            $status = $res['data']['status'] ?? '';
            return [
                'success'             => true,
                'code'                => 200,
                'status'              => $status,
                'is_paid'             => ($status === self::STATUS_ORDER_SUCCESS),
                'gpay_bill_id'        => $res['data']['gpay_bill_id'] ?? $gpayBillId,
                'gpay_trans_id'       => $res['data']['gpay_trans_id'] ?? '',
                'merchant_order_id'   => $res['data']['merchant_order_id'] ?? $merchantOrderId,
                'user_payment_method' => $res['data']['user_payment_method'] ?? '',
                'embed_data'          => $res['data']['embed_data'] ?? '',
                'data'                => $res['data'],
                'meta'                => $res['meta'] ?? []
            ];
        }

        return $res;
    }

    /**
     * 3.3. Xử lý & Xác thực Webhook / IPN Cổng thanh toán từ Gpay gửi về
     *
     * @param array $headers
     * @param string|array $rawBody
     * @return array
     */
    public function handleWebhook(array $headers, $rawBody): array
    {
        $signature = $headers['signature'] ?? ($headers['Signature'] ?? '');
        $timestamp = $headers['x-timestamp'] ?? ($headers['X-Timestamp'] ?? '');
        $requestId = $headers['x-requests-id'] ?? ($headers['X-Requests-Id'] ?? ($headers['x-request-id'] ?? ''));

        $bodyArray = is_array($rawBody) ? $rawBody : json_decode($rawBody, true);
        $bodyJson  = is_string($rawBody) ? $rawBody : json_encode($rawBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->logInfo('GPAY_V2_GATEWAY_WEBHOOK_RECEIVED', [
            'headers' => $headers,
            'body'    => $bodyArray
        ]);

        if (!empty($signature) && !empty($this->gpayPublicKey)) {
            $isValid = $this->verifySignature($timestamp, $requestId, $bodyJson, $signature);
            if (!$isValid) {
                $this->logError('GPAY_V2_GATEWAY_WEBHOOK_SIGN_INVALID', ['signature' => $signature]);
                return [
                    'valid'   => false,
                    'message' => 'Chữ ký webhook Cổng thanh toán không hợp lệ',
                    'data'    => $bodyArray
                ];
            }
        }

        $status = $bodyArray['data']['status'] ?? ($bodyArray['status'] ?? '');

        return [
            'valid'             => true,
            'message'           => 'Webhook hợp lệ',
            'status'            => $status,
            'is_success'        => ($status === self::STATUS_ORDER_SUCCESS),
            'gpay_bill_id'      => $bodyArray['data']['gpay_bill_id'] ?? ($bodyArray['gpay_bill_id'] ?? ''),
            'gpay_trans_id'     => $bodyArray['data']['gpay_trans_id'] ?? ($bodyArray['gpay_trans_id'] ?? ''),
            'merchant_order_id' => $bodyArray['data']['merchant_order_id'] ?? ($bodyArray['merchant_order_id'] ?? ''),
            'embed_data'        => $bodyArray['data']['embed_data'] ?? ($bodyArray['embed_data'] ?? ''),
            'data'              => $bodyArray['data'] ?? $bodyArray,
            'raw'               => $bodyArray
        ];
    }

    /**
     * Phản hồi chuẩn cho Gpay khi nhận Webhook thành công
     *
     * @param string $message
     * @return array
     */
    public static function webhookResponseSuccess(string $message = 'Success'): array
    {
        return [
            'meta' => [
                'code' => '200',
                'msg'  => $message
            ],
            'data' => [
                'status' => 'OK'
            ]
        ];
    }

    /* =========================================================================
     * HELPER & FORMATTING UTILITIES
     * ========================================================================= */

    /**
     * Chuẩn hóa Certificate: Xóa header/footer PEM, xóa dấu xuống dòng và khoảng trắng
     *
     * @param string $cert
     * @return string
     */
    public function cleanCertificate(string $cert): string
    {
        $cert = str_replace([
            '-----BEGIN CERTIFICATE-----',
            '-----END CERTIFICATE-----',
            "\r", "\n", ' ', "\t"
        ], '', trim($cert));

        return $cert;
    }

    /**
     * Định dạng Private Key chuẩn PEM RSA
     *
     * @param string $key
     * @return string
     */
    public function formatPrivateKey(string $key): string
    {
        $key = trim($key);
        if (empty($key)) {
            return '';
        }

        if (strpos($key, '-----BEGIN RSA PRIVATE KEY-----') !== false || strpos($key, '-----BEGIN PRIVATE KEY-----') !== false) {
            return $key;
        }

        if (file_exists($key)) {
            return file_get_contents($key);
        }

        $cleaned = str_replace(["\r", "\n", ' '], '', $key);
        $chunked = chunk_split($cleaned, 64, "\n");
        return "-----BEGIN RSA PRIVATE KEY-----\n" . trim($chunked) . "\n-----END RSA PRIVATE KEY-----";
    }

    /**
     * Định dạng Public Key chuẩn PEM RSA
     *
     * @param string $key
     * @return string
     */
    public function formatPublicKey(string $key): string
    {
        $key = trim($key);
        if (empty($key)) {
            return '';
        }

        if (strpos($key, '-----BEGIN PUBLIC KEY-----') !== false) {
            return $key;
        }

        if (file_exists($key)) {
            return file_get_contents($key);
        }

        $cleaned = str_replace(["\r", "\n", ' '], '', $key);
        $chunked = chunk_split($cleaned, 64, "\n");
        return "-----BEGIN PUBLIC KEY-----\n" . trim($chunked) . "\n-----END PUBLIC KEY-----";
    }

    /**
     * Khởi tạo đối tượng cURL
     */
    private function initCurl(): void
    {
        $this->curl = new Curl();
        $this->curl->setTimeout($this->timeout);
        $this->curl->setConnectTimeout($this->timeout);
    }

    /**
     * Masking thông tin nhạy cảm khi ghi log
     *
     * @param array $headers
     * @return array
     */
    private function maskHeaders(array $headers): array
    {
        $masked = $headers;
        if (isset($masked['Authorization'])) {
            $masked['Authorization'] = 'Bearer ***' . substr($masked['Authorization'], -8);
        }
        if (isset($masked['signature'])) {
            $masked['signature'] = substr($masked['signature'], 0, 10) . '***';
        }
        if (isset($masked['x-certificate'])) {
            $masked['x-certificate'] = substr($masked['x-certificate'], 0, 10) . '***';
        }
        return $masked;
    }

    /**
     * Ghi log Info
     *
     * @param string $title
     * @param array $context
     */
    private function logInfo(string $title, array $context = []): void
    {
        if ($this->enableLog) {
            Log::info("[GPAY_V2] {$title}", $context);
        }
    }

    /**
     * Ghi log Error
     *
     * @param string $title
     * @param array $context
     */
    private function logError(string $title, array $context = []): void
    {
        if ($this->enableLog) {
            Log::error("[GPAY_V2] {$title}", $context);
        }
    }
}
