<?php

namespace App\Utilities;

use Curl\Curl;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class GpayV2
 * 
 * Thư viện tích hợp Cổng thanh toán All-in-one & Open API V1/V2 của Gpay.
 * Tài liệu kỹ thuật: https://docs.g-pay.vn/vi/api-docs/payment-gateway
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
     * Các phương thức thanh toán hỗ trợ
     */
    const METHOD_ALL                = '';
    const METHOD_BANK_ATM           = 'BANK_ATM';
    const METHOD_BANK_INTERNATIONAL = 'BANK_INTERNATIONAL';
    const METHOD_QR_PAYMENT         = 'QR_PAYMENT';

    /**
     * Loại thanh toán
     */
    const PAYMENT_TYPE_IMMEDIATE = 'IMMEDIATE';

    /**
     * Trạng thái đơn hàng
     */
    const STATUS_SUCCESS = 'ORDER_SUCCESS';
    const STATUS_PENDING = 'ORDER_PENDING';
    const STATUS_FAILED  = 'ORDER_FAILED';

    /**
     * @var string Môi trường kết nối (sandbox / production)
     */
    private $environment = self::ENV_SANDBOX;

    /**
     * @var string Base API URL
     */
    private $baseUrl = self::URL_SANDBOX;

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
     * 1. Lấy Access Token từ Gpay API (POST /v1/auth/token)
     * Token được tự động lưu vào Cache Laravel để tái sử dụng theo TTL
     *
     * @param bool $forceRefresh Bắt buộc lấy mới không dùng cache
     * @return array ['success' => bool, 'access_token' => string, 'expires_in' => int, 'message' => string]
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
     * 2. Sinh chữ ký số RSA-SHA256 theo đặc tả Gpay
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
     * 3. Kiểm tra chữ ký số từ Gpay gửi về (Webhook / Response)
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
     * 4. Build bộ Security Headers chuẩn cho các API Gpay
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

    /* =========================================================================
     * PAYMENT GATEWAY ALL-IN-ONE APIS (https://docs.g-pay.vn/vi/api-docs/payment-gateway)
     * ========================================================================= */

    /**
     * 1. API Init Order - Tạo đơn hàng thanh toán trên Cổng Gpay
     * Endpoint: POST /v1/payments/gateway/init-order
     *
     * @param array $params Danh sách tham số tạo đơn:
     *  - amount (int, required): Số tiền giao dịch (VND)
     *  - callback_url (string, required): URL redirect người dùng sau khi thanh toán xong
     *  - webhook_url (string, required): URL IPN nhận notify trạng thái giao dịch
     *  - customer_id (string, required): Mã khách hàng phía merchant
     *  - embed_data (string, required): Dữ liệu tùy biến đính kèm trả về khi callback
     *  - payment_type (string, required): Loại thanh toán, mặc định 'IMMEDIATE'
     *  - request_id (string, optional): Mã định danh request (tự sinh nếu rỗng)
     *  - title (string, optional): Tiêu đề đơn hàng
     *  - description (string, optional): Mô tả đơn hàng
     *  - customer_name (string, optional): Tên khách hàng
     *  - email (string, optional): Email khách hàng
     *  - phone (string, optional): Số điện thoại khách hàng
     *  - address (string, optional): Địa chỉ khách hàng
     *  - payment_method (string, optional): 'BANK_ATM', 'BANK_INTERNATIONAL', 'QR_PAYMENT' hoặc rỗng để hiển thị tất cả
     *
     * @return array Kết quả trả về gồm bill_id, bill_url để redirect khách hàng
     */
    public function initOrder(array $params): array
    {
        try {
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

            // Validation bắt buộc theo đặc tả Gpay
            if ($body['amount'] <= 0) {
                return ['success' => false, 'message' => 'Số tiền (amount) phải lớn hơn 0'];
            }
            if (empty($body['callback_url'])) {
                return ['success' => false, 'message' => 'callback_url không được để trống'];
            }
            if (empty($body['webhook_url'])) {
                return ['success' => false, 'message' => 'webhook_url không được để trống'];
            }

            $endpoint = $this->baseUrl . '/payments/gateway/init-order';
            $headers  = $this->buildSecurityHeaders($body, $requestId);

            $this->initCurl();
            foreach ($headers as $k => $v) {
                $this->curl->setHeader($k, $v);
            }
            $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
            $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);

            $this->logInfo('GPAY_V2_INIT_ORDER_REQUEST', [
                'endpoint' => $endpoint,
                'headers'  => $this->maskHeaders($headers),
                'body'     => $body
            ]);

            $this->curl->post($endpoint, json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            if ($this->curl->error) {
                $errMsg = "Lỗi kết nối Gpay Init Order: {$this->curl->errorCode} - {$this->curl->errorMessage}";
                $this->logError('GPAY_V2_INIT_ORDER_CURL_ERROR', ['error' => $errMsg, 'raw' => $this->curl->rawResponse]);
                return [
                    'success'    => false,
                    'message'    => $errMsg,
                    'error_code' => $this->curl->errorCode,
                    'raw'        => $this->curl->rawResponse
                ];
            }

            $res  = json_decode($this->curl->rawResponse, true);
            $this->logInfo('GPAY_V2_INIT_ORDER_RESPONSE', ['response' => $res]);

            $code = $res['meta']['code'] ?? 'ERR';
            if ((string)$code === '200' && !empty($res['data'])) {
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

            $msg = $res['meta']['msg'] ?? ($res['meta']['message'] ?? 'Khởi tạo đơn hàng thất bại');
            return [
                'success' => false,
                'code'    => $code,
                'message' => $msg,
                'error'   => $res['meta']['error'] ?? null,
                'raw'     => $res
            ];
        } catch (\Exception $e) {
            $this->logError('GPAY_V2_INIT_ORDER_EXCEPTION', ['exception' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 2. API Query Order - Truy vấn trạng thái đơn hàng
     * Endpoint: POST /v1/payments/gateway/query-order
     *
     * @param string $gpayBillId Mã bill_id do Gpay trả về khi init-order
     * @param string|null $merchantOrderId Mã request_id của merchant
     * @return array
     */
    public function queryOrder(string $gpayBillId, ?string $merchantOrderId = ''): array
    {
        try {
            $body = [
                'gpay_bill_id'      => (string)$gpayBillId,
                'merchant_order_id' => (string)$merchantOrderId
            ];

            $endpoint  = $this->baseUrl . '/payments/gateway/query-order';
            $requestId = (string)Str::uuid();
            $headers   = $this->buildSecurityHeaders($body, $requestId);

            $this->initCurl();
            foreach ($headers as $k => $v) {
                $this->curl->setHeader($k, $v);
            }
            $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
            $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);

            $this->logInfo('GPAY_V2_QUERY_ORDER_REQUEST', ['endpoint' => $endpoint, 'body' => $body]);

            $this->curl->post($endpoint, json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            if ($this->curl->error) {
                $errMsg = "Lỗi truy vấn đơn hàng Gpay: {$this->curl->errorCode} - {$this->curl->errorMessage}";
                $this->logError('GPAY_V2_QUERY_ORDER_ERROR', ['error' => $errMsg, 'raw' => $this->curl->rawResponse]);
                return [
                    'success'    => false,
                    'message'    => $errMsg,
                    'error_code' => $this->curl->errorCode,
                    'raw'        => $this->curl->rawResponse
                ];
            }

            $res  = json_decode($this->curl->rawResponse, true);
            $this->logInfo('GPAY_V2_QUERY_ORDER_RESPONSE', ['response' => $res]);

            $code = $res['meta']['code'] ?? 'ERR';
            if ((string)$code === '200' && !empty($res['data'])) {
                $status = $res['data']['status'] ?? '';
                return [
                    'success'             => true,
                    'code'                => 200,
                    'status'              => $status,
                    'is_paid'             => ($status === self::STATUS_SUCCESS),
                    'gpay_bill_id'        => $res['data']['gpay_bill_id'] ?? $gpayBillId,
                    'gpay_trans_id'       => $res['data']['gpay_trans_id'] ?? '',
                    'merchant_order_id'   => $res['data']['merchant_order_id'] ?? $merchantOrderId,
                    'user_payment_method' => $res['data']['user_payment_method'] ?? '',
                    'embed_data'          => $res['data']['embed_data'] ?? '',
                    'data'                => $res['data'],
                    'meta'                => $res['meta'] ?? []
                ];
            }

            return [
                'success' => false,
                'code'    => $code,
                'message' => $res['meta']['msg'] ?? 'Không tìm thấy thông tin đơn hàng',
                'error'   => $res['meta']['error'] ?? null,
                'raw'     => $res
            ];
        } catch (\Exception $e) {
            $this->logError('GPAY_V2_QUERY_ORDER_EXCEPTION', ['exception' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 3. Xử lý & Xác thực Webhook / IPN từ Gpay gửi về
     *
     * @param array $headers Headers từ Request (chứa signature, x-timestamp, x-requests-id)
     * @param string|array $rawBody Payload nhận được từ Gpay
     * @return array ['valid' => bool, 'data' => array, 'message' => string]
     */
    public function handleWebhook(array $headers, $rawBody): array
    {
        $signature = $headers['signature'] ?? ($headers['Signature'] ?? '');
        $timestamp = $headers['x-timestamp'] ?? ($headers['X-Timestamp'] ?? '');
        $requestId = $headers['x-requests-id'] ?? ($headers['X-Requests-Id'] ?? ($headers['x-request-id'] ?? ''));

        $bodyArray = is_array($rawBody) ? $rawBody : json_decode($rawBody, true);
        $bodyJson  = is_string($rawBody) ? $rawBody : json_encode($rawBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->logInfo('GPAY_V2_WEBHOOK_RECEIVED', [
            'headers' => $headers,
            'body'    => $bodyArray
        ]);

        // Nếu có chữ ký số thì verify bằng public key
        if (!empty($signature) && !empty($this->gpayPublicKey)) {
            $isValid = $this->verifySignature($timestamp, $requestId, $bodyJson, $signature);
            if (!$isValid) {
                $this->logError('GPAY_V2_WEBHOOK_SIGNATURE_INVALID', ['signature' => $signature]);
                return [
                    'valid'   => false,
                    'message' => 'Chữ ký webhook Gpay không hợp lệ',
                    'data'    => $bodyArray
                ];
            }
        }

        return [
            'valid'             => true,
            'message'           => 'Webhook hợp lệ',
            'status'            => $bodyArray['data']['status'] ?? ($bodyArray['status'] ?? ''),
            'is_success'        => ($bodyArray['data']['status'] ?? ($bodyArray['status'] ?? '')) === self::STATUS_SUCCESS,
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

        // Nếu đã có header/footer
        if (strpos($key, '-----BEGIN RSA PRIVATE KEY-----') !== false || strpos($key, '-----BEGIN PRIVATE KEY-----') !== false) {
            return $key;
        }

        // Nếu là đường dẫn file
        if (file_exists($key)) {
            return file_get_contents($key);
        }

        // Bọc header/footer RSA standard
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

        // Nếu đã có header/footer
        if (strpos($key, '-----BEGIN PUBLIC KEY-----') !== false) {
            return $key;
        }

        // Nếu là đường dẫn file
        if (file_exists($key)) {
            return file_get_contents($key);
        }

        // Bọc header/footer standard
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
