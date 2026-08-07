<?php

namespace App\Utilities;

use Curl\Curl;

class Gpay
{

    // trường Sandbox: https://mpa-va.sandbox.g-pay.vn/api/v3/
// - Môi trường Production: https://mpa-va.g-pay.vn/api/v3
    private $strUrlVa = 'https://mpa-va.sandbox.g-pay.vn/api/v3';
    private $strUrl = 'https://mpa.sandbox.g-pay.vn/api/v3';
    private $strMerchanteCode = "GIAONHAN247";
    private $strMerchantPassword = "11sdf!@$@#A";
    private $strPrivateKey = null;
    private $strAuthentication = null;
    private $strPublicKey = null;

    private $_curl = null;

    public function __construct()
    {
        // if (env('APP_ENV') == "production") {
        $this->strUrlVa            = 'https://mpa-va.g-pay.vn/api/v3';
        $this->strUrl              = 'https://mpa.g-pay.vn/api/v3';
        $this->strMerchanteCode    = "UNIVERSAL ECOM";
        $this->strMerchantPassword = "Giaonhan@247";
        // }

        $this->_curl = new Curl();
        $this->_curl->setTimeout(180);
        $this->_curl->setConnectTimeout(180);
    }

    public function setMerchantCode($strMerchanteCode)
    {
        $this->strMerchanteCode = $strMerchanteCode;
        return $this;
    }

    public function setMerchantPassword($strMerchantPassword)
    {
        $this->strMerchantPassword = $strMerchantPassword;
        return $this;
    }

    public function setPrivateKey($strPrivateKey)
    {
        $this->strPrivateKey = $strPrivateKey;
        return $this;
    }

    public function setAuthentication($strAuthentication)
    {
        $this->strAuthentication = $strAuthentication;
        return $this;
    }

    public function setPublicKey($strPublicKey)
    {
        $this->strPublicKey = $strPublicKey;
        return $this;
    }

    public function getBanks()
    {
        // try {
        //     // bankId=&businessId=19&merchantId=215&timestamp=1705859152791&version=2.0"

        //     $arrQuery         = [
        //         "bankId" => 0,
        //         "merchantId" => $this->intMerchantId,
        //         "businessId" => $this->intBusinessId,
        //         "timestamp" => floor(microtime(true) * 1000),
        //         "version" => "2.0"
        //     ];
        //     $arrQuery["sign"] = $this->getSign($arrQuery);
        //     $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        //     $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
        //     $this->_curl->get($this->strUrl . '/trade/vn/transfer/getBankList?' . http_build_query($arrQuery));
        //     if ($this->_curl->error) {
        //         return [
        //             'success' => false,
        //             'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
        //             'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
        //             'data' => $this->_curl->rawResponse
        //         ];
        //     }

        //     return [
        //         'success' => true,
        //         'message' => 'Thành công',
        //         'data' => json_decode($this->_curl->rawResponse, true),
        //     ];
        // } catch (\Exception $e) {
        //     return [
        //         'success' => false,
        //         'message' => 'Có lỗi Exception',
        //         'error' => 'Error Exception: ' . $e->getMessage(),
        //         'data' => [],
        //     ];
        // }
    }

    public function createToken($arrParams = [])
    {
        try {

            $arrQuery = [
                "merchant_code" => $this->strMerchanteCode,
                "password" => $this->strMerchantPassword,
            ];
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrlVa . '/authentication/token/create', json_encode($arrQuery));

            $arrResponse    = json_decode($this->_curl->rawResponse, true);
            $strMetaCode    = $arrResponse["meta"]["code"] ?? "999";
            $strMetaMessage = $arrResponse["meta"]["msg"] ?? "ERR";

            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => $strMetaMessage,
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $arrResponse
                ];
            }


            if ($strMetaCode != 200) {
                return [
                    'success' => false,
                    'message' => 'Lỗi ' . $strMetaMessage,
                    'data' => $arrResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse["response"] ?? []
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
    public function getSign($arrParams = [])
    {
        $strOpenSSLData = str_replace("+", " ", General::httpBuildQuery($arrParams));
        openssl_sign($strOpenSSLData, $strOpenSSLBinary, $this->strPrivateKey, "SHA256");
        return base64_encode($strOpenSSLBinary);
    }

    public function verifySign($arrData)
    {
        if (empty($arrData['signature'])) {
            return false;
        }
        $strSign = $arrData['signature'] ?? "";
        if (isset($arrData['signature'])) {
            unset($arrData['signature']);
        }

        if (isset($arrData['merchant_code'])) {
            unset($arrData['merchant_code']);
        }

        // ksort($arrData);
        $verified = openssl_verify(urldecode(str_replace("+", " ", General::httpBuildQuery($arrData))), base64_decode($strSign), openssl_pkey_get_public($this->strPublicKey), OPENSSL_ALGO_SHA256);
        if ($verified === 1) {
            return true;
        }
        return false;
    }
    public function createVirtualAccount($arrParams = [])
    {
        try {
            $intMapId = $arrParams["map_id"] ?? time() . rand(111, 999);
            $arrQuery = [
                "merchant_code" => $this->strMerchanteCode,
                "account_name" => $arrParams["account_name"] ?? "",
                "map_id" => $intMapId,
                "map_type" => $arrParams["map_type"] ?? "CCCD", //CMND, CCCD, PASSPORT, MHD, CUSTOMER_ID, EMAIL, PHONE_NUMBER
                "account_type" => $arrParams["account_type"] ?? "M", // M: nhiều lần , O: 1 lần
                "bank_code" => $arrParams["bank_code"] ?? "TCB", //[MSB, VCCB, BIDV, VPB, WOO, TCB].
                "max_amount" => $arrParams["max_amount"] ?? 0, //[MSB, VCCB, BIDV, VPB, WOO, TCB].
                "min_amount" => $arrParams["min_amount"] ?? 0,
                "equal_amount" => $arrParams["equal_amount"] ?? 0
            ];

            $arrQuery["signature"] = $this->getSign([
                "merchant_code" => $this->strMerchanteCode,
                "account_name" => $arrParams["account_name"] ?? "",
                "map_id" => $intMapId,
                "map_type" => $arrParams["map_type"] ?? "CCCD", //CMND, CCCD, PASSPORT, MHD, CUSTOMER_ID, EMAIL, PHONE_NUMBER
                "account_type" => $arrParams["account_type"] ?? "M",
                "bank_code" => $arrParams["bank_code"] ?? "TCB", //[MSB, VCCB, BIDV, VPB, WOO, TCB].
            ]);
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthentication);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrlVa . '/virtual-account/create', json_encode($arrQuery));

            $arrResponse    = json_decode($this->_curl->rawResponse, true);
            $strMetaCode    = $arrResponse["meta"]["code"] ?? "999";
            $strMetaMessage = $arrResponse["meta"]["msg"] ?? "ERR";

            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => $strMetaMessage,
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $arrResponse
                ];
            }


            if ($strMetaCode != 200) {
                return [
                    'success' => false,
                    'message' => 'Lỗi ' . $strMetaMessage,
                    'data' => $arrResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse["response"] ?? []
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


    public function updateVirtualAccount($arrParams = [])
    {
        try {
            $arrQuery = [
                "merchant_code" => $this->strMerchanteCode,
                "account_name" => $arrParams["account_name"] ?? "",
                "account_number" => $arrParams["account_number"] ?? "",
                "account_type" => $arrParams["account_type"] ?? "M",
                "max_amount" => $arrParams["max_amount"] ?? 0, //[MSB, VCCB, BIDV, VPB, WOO, TCB].
                "min_amount" => $arrParams["min_amount"] ?? 0,
                "equal_amount" => $arrParams["equal_amount"] ?? 0
            ];

            $arrQuery["signature"] = $this->getSign([
                "merchant_code" => $this->strMerchanteCode,
                "account_number" => $arrParams["account_number"] ?? "",
                "account_name" => $arrParams["account_name"] ?? "",
            ]);
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthentication);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrlVa . '/virtual-account/update', json_encode($arrQuery));

            $arrResponse    = json_decode($this->_curl->rawResponse, true);
            $strMetaCode    = $arrResponse["meta"]["code"] ?? "999";
            $strMetaMessage = $arrResponse["meta"]["msg"] ?? "ERR";

            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => $strMetaMessage,
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $arrResponse
                ];
            }


            if ($strMetaCode != 200) {
                return [
                    'success' => false,
                    'message' => 'Lỗi ' . $strMetaMessage,
                    'data' => $arrResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse["response"] ?? []
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


    public function closeVirtualAccount($arrParams = [])
    {
        try {
            $arrQuery = [
                "merchant_code" => $this->strMerchanteCode,
                "account_name" => $arrParams["account_name"] ?? "",
                "account_number" => $arrParams["account_number"] ?? "",
                "close_reason" => $arrParams["close_reason"] ?? "",
            ];

            $arrQuery["signature"] = $this->getSign([
                "merchant_code" => $this->strMerchanteCode,
                "account_number" => $arrParams["account_number"] ?? "",
            ]);
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthentication);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrlVa . '/virtual-account/close', json_encode($arrQuery));

            $arrResponse    = json_decode($this->_curl->rawResponse, true);
            $strMetaCode    = $arrResponse["meta"]["code"] ?? "999";
            $strMetaMessage = $arrResponse["meta"]["msg"] ?? "ERR";

            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => $strMetaMessage,
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $arrResponse
                ];
            }


            if ($strMetaCode != 200) {
                return [
                    'success' => false,
                    'message' => 'Lỗi ' . $strMetaMessage,
                    'data' => $arrResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse["response"] ?? []
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

    public function reOpenVirtualAccount($arrParams = [])
    {
        try {
            $arrQuery = [
                "merchant_code" => $this->strMerchanteCode,
                "account_name" => $arrParams["account_name"] ?? "",
                "account_number" => $arrParams["account_number"] ?? "",
            ];

            $arrQuery["signature"] = $this->getSign([
                "merchant_code" => $this->strMerchanteCode,
                "account_number" => $arrParams["account_number"] ?? ""
            ]);
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthentication);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrlVa . '/virtual-account/re-open', json_encode($arrQuery));

            $arrResponse    = json_decode($this->_curl->rawResponse, true);
            $strMetaCode    = $arrResponse["meta"]["code"] ?? "999";
            $strMetaMessage = $arrResponse["meta"]["msg"] ?? "ERR";

            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => $strMetaMessage,
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $arrResponse
                ];
            }


            if ($strMetaCode != 200) {
                return [
                    'success' => false,
                    'message' => 'Lỗi ' . $strMetaMessage,
                    'data' => $arrResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse["response"] ?? []
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


    public function fundTransfersInquiry($arrParams = [])
    {
        try {

            $arrQuery = [
                "request_id" => $arrParams["request_id"] ?? "",
                "merchant_code" => $this->strMerchanteCode,
                "account_number" => $arrParams["account_number"] ?? "",
                "type" => $arrParams["type"] ?? "",
                "bank_code" => $arrParams["bank_code"] ?? "",
            ];

            $arrQuery["signature"] = $this->getSign([
                "merchant_code" => $this->strMerchanteCode,
                "account_number" => $arrParams["account_number"] ?? "",
                "type" => $arrParams["type"] ?? "",
                "request_id" => $arrParams["request_id"] ?? "",
            ]);

            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/fund-transfers/inquiry', json_encode($arrQuery));
            $arrResponse    = json_decode($this->_curl->rawResponse, true);
            $strMetaCode    = $arrResponse["meta"]["code"] ?? "999";
            $strMetaMessage = $arrResponse["meta"]["msg"] ?? "ERR";

            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => $strMetaMessage,
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $arrResponse
                ];
            }


            if ($strMetaCode != 200) {
                return [
                    'success' => false,
                    'message' => 'Lỗi ' . $strMetaMessage,
                    'data' => $arrResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse["response"] ?? []
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

    public function fundTransfersFtToBank($arrParams = [])
    {
        try {

            $arrQuery = [
                "account_number" => $arrParams["account_number"] ?? "",
                "bank_code" => $arrParams["bank_code"] ?? "",
                "full_name" => $arrParams["full_name"] ?? "",
                "amount" => $arrParams["amount"] ?? "",
                "merchant_code" => $this->strMerchanteCode,
                "transaction_id" => $arrParams["transaction_id"] ?? "",
                "type" => $arrParams["type"] ?? "",
                "order_ref" => $arrParams["order_ref"] ?? "",
                "map_id" => $arrParams["map_id"] ?? "",
                "message" => $arrParams["message"] ?? "",
            ];

            $arrQuery["signature"] = $this->getSign([
                "merchant_code" => $this->strMerchanteCode,
                "account_number" => $arrParams["account_number"] ?? "",
                "bank_code" => $arrParams["bank_code"] ?? "",
                "order_ref" => $arrParams["order_ref"] ?? "",
                "amount" => $arrParams["amount"] ?? "",
                "transaction_id" => $arrParams["transaction_id"] ?? "",
                "type" => $arrParams["type"] ?? "",
            ]);
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/fund-transfers/ft-to-bank', json_encode($arrQuery));


            $arrResponse    = json_decode($this->_curl->rawResponse, true);
            $strMetaCode    = $arrResponse["meta"]["code"] ?? "999";
            $strMetaMessage = $arrResponse["meta"]["msg"] ?? "ERR";

            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => $strMetaMessage,
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $arrResponse
                ];
            }


            if ($strMetaCode != 200) {
                return [
                    'success' => false,
                    'message' => 'Lỗi ' . $strMetaMessage,
                    'data' => $arrResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse["response"] ?? []
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


    public function getMerchantAccountInfomation()
    {
        try {

            $arrQuery = [
                "merchant_code" => $this->strMerchanteCode
            ];

            $arrQuery["signature"] = $this->getSign([
                "merchant_code" => $this->strMerchanteCode
            ]);
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/merchants/get-merchant-account-information', json_encode($arrQuery));


            $arrResponse    = json_decode($this->_curl->rawResponse, true);
            $strMetaCode    = $arrResponse["meta"]["code"] ?? "999";
            $strMetaMessage = $arrResponse["meta"]["msg"] ?? "ERR";

            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => $strMetaMessage,
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $arrResponse
                ];
            }


            if ($strMetaCode != 200) {
                return [
                    'success' => false,
                    'message' => 'Lỗi ' . $strMetaMessage,
                    'data' => $arrResponse
                ];
            }

            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResponse["response"] ?? []
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
}