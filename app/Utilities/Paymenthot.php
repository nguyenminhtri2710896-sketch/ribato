<?php

namespace App\Utilities;

use Curl\Curl;
use Illuminate\Support\Facades\Crypt;

class Paymenthot
{
    private $strUrl = 'https://api.paymenthot.com';
    private $_curl = null;
    private $strUsername = null;
    private $strTenant = null;
    private $strPassword = null;
    private $strPrivateKey = null;
    private $strPublicKey = null;
    private $strAuthorization = null;
    private $strPasscode = null;
    private $strSecretKey = null;

    public function __construct()
    {

        $this->_curl = new Curl();
        $this->_curl->setTimeout(80);
        $this->_curl->setConnectTimeout(80);
    }

    public function setTenant($strTenant)
    {
        $this->strTenant = $strTenant;
        return $this;
    }

    public function setPasscode($strPasscode)
    {
        $this->strPasscode = Crypt::decryptString($strPasscode);
        return $this;
    }


    public function setUsername($strUsername)
    {
        $this->strUsername = $strUsername;
        return $this;
    }

    public function setPassword($strPassword)
    {
        $this->strPassword = Crypt::decryptString($strPassword);
        return $this;
    }

    public function setSecretKey($strSecretKey)
    {
        $this->strSecretKey = $strSecretKey;
        return $this;
    }

    public function setPublicKey($strPublicKey)
    {
        $this->strPublicKey = $strPublicKey;
        return $this;
    }

    public function setPrivateKey($strPrivateKey)
    {
        $this->strPrivateKey = $strPrivateKey;
        return $this;
    }

    public function setAuthorization($strAuthorization)
    {
        $this->strAuthorization = $strAuthorization;
        return $this;
    }

    public function login()
    {
        try {
            $arrQuery = [
                "username" => $this->strUsername,
                "password" => base64_encode(hash('sha256', $this->strUsername . $this->strPassword)),
            ];

            $strRequestId    = \Str::uuid();
            $strPrequestTime = date('YmdHis');

            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('p-request-id', $strRequestId);
            $this->_curl->setHeader('p-request-time', $strPrequestTime);
            $this->_curl->setHeader('p-tenant', $this->strTenant);
            $this->_curl->setHeader('p-signature', $this->getSign($strRequestId . $strPrequestTime . $this->strTenant . json_encode($arrQuery), $this->strPrivateKey));

            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);

            $this->_curl->post($this->strUrl . '/auth-service/api/v1.0/user/login', json_encode($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResponse = json_decode($this->_curl->rawResponse, true);
            $strCode     = $arrResponse["code"] ?? "";
            if ($strCode != "SUCCESS") {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'error' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'data' => $arrResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => json_decode($this->_curl->rawResponse, true),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi Exception',
                'error' => 'Error Exception: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    public function bodGetName($arrParams = [])
    {
        try {
            $arrQuery        = [
                "bankId" => $arrParams["bankId"] ?? "",
                "bankRefNumber" => $arrParams["bankRefNumber"] ?? "",
            ];
            $strRequestId    = \Str::uuid();
            $strPrequestTime = date('YmdHis');


            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('p-request-id', $strRequestId);
            $this->_curl->setHeader('p-request-time', $strPrequestTime);
            $this->_curl->setHeader('p-tenant', $this->strTenant);
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthorization);
            $this->_curl->setHeader('p-signature', $this->getSign("Bearer " . $this->strAuthorization . $strRequestId . $strPrequestTime . $this->strTenant . json_encode($arrQuery), $this->strPrivateKey));

            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/bank-gateway-service/mch/api/v1.0/pob/get_name', json_encode($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => json_decode($this->_curl->rawResponse, true),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi Exception',
                'error' => 'Error Exception: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    public function imploreAuth($arrParams = [])
    {
        try {
            $arrQuery = [
                "authValue" => base64_encode(hash('sha256', $this->strUsername . $this->strPasscode)),
                "phone" => $this->strUsername,
                "api" => $arrParams["api"] ?? "/merchant-transaction-service/api/v1.0/transfer_247",
                "authMode" => "PASSCODE",
            ];

            $strRequestId    = \Str::uuid();
            $strPrequestTime = date('YmdHis');

            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('p-request-id', $strRequestId);
            $this->_curl->setHeader('p-request-time', $strPrequestTime);
            $this->_curl->setHeader('p-tenant', $this->strTenant);
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthorization);
            $this->_curl->setHeader('p-signature', $this->getSign("Bearer " . $this->strAuthorization . $strRequestId . $strPrequestTime . $this->strTenant . json_encode($arrQuery), $this->strPrivateKey));
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/auth-service/api/v1.0/implore-auth', json_encode($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResponse = json_decode($this->_curl->rawResponse, true);
            $strCode     = $arrResponse["code"] ?? "";
            if ($strCode != "SUCCESS") {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'error' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'data' => $arrResponse
                ];
            }
            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi Exception',
                'error' => 'Error Exception: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }


    public function tranfer247($arrParams = [])
    {

        try {
            $arrQuery = [
                "audit" => $arrParams["audit"],//8755886270480207,
                "amount" => $arrParams["amount"],//200000,
                "bankCode" => $arrParams["bankCode"],//"970418",
                "bankId" => $arrParams["bankId"],//"BIDV",
                "bankRefName" => $arrParams["bankRefName"],//"NGUYEN VAN A",
                "bankRefNumber" => $arrParams["bankRefNumber"],//"1023020330000",
                "content" => $arrParams["content"],//"123123123123"
            ];

            $strRequestId    = \Str::uuid();
            $strPrequestTime = date('YmdHis');
            $strVerification = $arrParams['verification'];

            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('p-request-id', $strRequestId);
            $this->_curl->setHeader('p-request-time', $strPrequestTime);
            $this->_curl->setHeader('p-tenant', $this->strTenant);
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthorization);
            $this->_curl->setHeader('verification', $strVerification);
            $this->_curl->setHeader('p-signature', $this->getSign("Bearer " . $this->strAuthorization . $strRequestId . $strPrequestTime . $this->strTenant . $strVerification . json_encode($arrQuery), $this->strPrivateKey));
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/merchant-transaction-service/api/v1.0/transfer_247', json_encode($arrQuery));

            // dump($this->_curl->getRequestHeaders());
            // dump("URL:" . $this->strUrl . '/merchant-transaction-service/api/v1.0/transfer_247');
            // dump("Data POST:" . json_encode($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResponse = json_decode($this->_curl->rawResponse, true);
            $strCode     = $arrResponse["code"] ?? "";
            if ($strCode != "SUCCESS") {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'error' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'data' => $arrResponse
                ];
            }
            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi Exception',
                'error' => 'Error Exception: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    public function tranfer247V2($arrParams = [])
    {
        try {
            $arrQuery = [
                "audit" => $arrParams["audit"],//8755886270480207,
                "amount" => $arrParams["amount"],//200000,
                "bankCode" => $arrParams["bankCode"],//"970418",
                "bankId" => $arrParams["bankId"],//"BIDV",
                "bankRefName" => $arrParams["bankRefName"],//"NGUYEN VAN A",
                "bankRefNumber" => $arrParams["bankRefNumber"],//"1023020330000",
                "content" => $arrParams["content"],//"123123123123"
            ];

            $strRequestId    = \Str::uuid();
            $strPrequestTime = date('YmdHis');
            $strVerification = $arrParams['verification'];

            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('p-request-id', $strRequestId);
            $this->_curl->setHeader('p-request-time', $strPrequestTime);
            $this->_curl->setHeader('p-tenant', $this->strTenant);
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthorization);
            $this->_curl->setHeader('verification', $strVerification);
            $this->_curl->setHeader('p-signature', $this->getSign("Bearer " . $this->strAuthorization . $strRequestId . $strPrequestTime . $this->strTenant . $strVerification . json_encode($arrQuery), $this->strPrivateKey));
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/merchant-transaction-service/api/v2.0/transfer_247', json_encode($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResponse = json_decode($this->_curl->rawResponse, true);
            $strCode     = $arrResponse["code"] ?? "";
            if ($strCode != "SUCCESS") {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'error' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'data' => $arrResponse
                ];
            }
            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi Exception',
                'error' => 'Error Exception: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    public function paymentMethod()
    {
        try {
            $strRequestId    = \Str::uuid();
            $strPrequestTime = date('YmdHis');

            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('p-request-id', $strRequestId);
            $this->_curl->setHeader('p-request-time', $strPrequestTime);
            $this->_curl->setHeader('p-tenant', $this->strTenant);
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthorization);
            $this->_curl->setHeader('p-signature', $this->getSign("Bearer " . $this->strAuthorization . $strRequestId . $strPrequestTime . $this->strTenant, $this->strPrivateKey));
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->get($this->strUrl . '/bank-gateway-service/mch/api/v1.0/payment_method');
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => json_decode($this->_curl->rawResponse, true),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi Exception',
                'error' => 'Error Exception: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }




    public function initialize($arrParams = [])
    {
        try {
            $arrQuery = [
                "currency" => $arrParams["currency"] ?? "VND",
                "issuerId" => $arrParams["issuerId"] ?? "PAYMENTHOT",
                "command" => $arrParams["command"] ?? "PAY",
                "paymentMethod" => $arrParams["paymentMethod"] ?? "QRBANK",
                "merchantData" => [
                    "orderId" => $arrParams["merchantData"]["orderId"] ?? "",
                    "orderDesc" => $arrParams["merchantData"]["orderDesc"] ?? "",
                    "amount" => $arrParams["merchantData"]["amount"] ?? 0
                ]
            ];

            $strRequestId    = \Str::uuid();
            $strPrequestTime = date('YmdHis');

            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('p-request-id', $strRequestId);
            $this->_curl->setHeader('p-request-time', $strPrequestTime);
            $this->_curl->setHeader('p-tenant', $this->strTenant);
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthorization);
            $this->_curl->setHeader('p-signature', $this->getSign("Bearer " . $this->strAuthorization . $strRequestId . $strPrequestTime . $this->strTenant . json_encode($arrQuery), $this->strPrivateKey));
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/pgw-transaction-service/mch/api/v1.0/initialize', json_encode($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => json_decode($this->_curl->rawResponse, true),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi Exception',
                'error' => 'Error Exception: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    public function balanceTechnicalWallet($arrParams = [])
    {
        try {
            $strRequestId    = \Str::uuid();
            $strPrequestTime = date('YmdHis');
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('p-request-id', $strRequestId);
            $this->_curl->setHeader('p-request-time', $strPrequestTime);
            $this->_curl->setHeader('p-tenant', $this->strTenant);
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthorization);
            $this->_curl->setHeader('p-signature', $this->getSign("Bearer " . $this->strAuthorization . $strRequestId . $strPrequestTime . $this->strTenant, $this->strPrivateKey));
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->get($this->strUrl . '/merchant-transaction-service/api/v1.0/balance/technical-wallet');
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResponse = json_decode($this->_curl->rawResponse, true);
            $strCode     = $arrResponse["code"] ?? "";
            if ($strCode != "SUCCESS") {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'error' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'data' => $arrResponse
                ];
            }
            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi Exception',
                'error' => 'Error Exception: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }


    public function inquiry($arrParams = [])
    {
        try {
            $strRequestId    = \Str::uuid();
            $strPrequestTime = date('YmdHis');
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('p-request-id', $strRequestId);
            $this->_curl->setHeader('p-request-time', $strPrequestTime);
            $this->_curl->setHeader('p-tenant', $this->strTenant);
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthorization);
            $this->_curl->setHeader('p-signature', $this->getSign("Bearer " . $this->strAuthorization . $strRequestId . $strPrequestTime . $this->strTenant, $this->strPrivateKey));
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->get($this->strUrl . '/merchant-transaction-service/api/v1.0/inquiry?auditNumber=' . ($arrParams['auditNumber'] ?? "") . '&txnDate=' . ($arrParams['txnDate'] ?? ""));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResponse = json_decode($this->_curl->rawResponse, true);
            $strCode     = $arrResponse["code"] ?? "";
            if ($strCode != "SUCCESS") {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'error' => 'Thất bại (' . $arrResponse["code"] . ': ' . $arrResponse["message"] . ')',
                    'data' => $arrResponse
                ];
            }
            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi Exception',
                'error' => 'Error Exception: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }



    public function getSign($strData, $strPrivateKey)
    {
        $strOpenSSLData   = $strData;
        $strOpenSSLBinary = "";
        openssl_sign($strOpenSSLData, $strOpenSSLBinary, $strPrivateKey, "SHA256");
        return base64_encode($strOpenSSLBinary);
    }
}