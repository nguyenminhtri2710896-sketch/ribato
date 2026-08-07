<?php

namespace App\Utilities;

use Curl\Curl;

class Yoobil
{
    private $strUrl = 'https://www.yoobil.com/yoobil';
    private $_curl = null;
    private $intBusinessId = 0;
    private $intMerchantId = 0;
    private $strSecretKey = null;
    private $strPrivateKey = null;
    private $strPublicKey = null;

    public $arrTransactionStatus = [
        "-1" => "Waiting for verifying",
        "1" => "Rejected",
        "9" => "The account number does not exist or input a wrong bank number",
        "19" => "Wrong account name",
        "25" => "Transaction amount limit",
        "26" => "Pending, do not retry",
        "34" => "Failed, retry please",
        "36" => "Pending, do not retry",
        "37" => "Insufficient balance"
    ];
    public function __construct()
    {

        $this->_curl = new Curl();
        $this->_curl->setTimeout(80);
        $this->_curl->setConnectTimeout(80);
    }

    public function setBusinessId($intBusinessId)
    {
        $this->intBusinessId = $intBusinessId;
        return $this;
    }

    public function setMerchantId($intMerchantId)
    {
        $this->intMerchantId = $intMerchantId;
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


    public function getBalance()
    {
        try {
            $arrQuery = [
                "merchantId" => $this->intMerchantId,
                "businessId" => $this->intBusinessId,
                "timestamp" => floor(microtime(true) * 1000),
                "currency" => "VND",
                "version" => 2.0
            ];
            $arrQuery["sign"] = $this->getSign($arrQuery);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->get($this->strUrl . '/trade/balance/query?' . http_build_query($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }


            $arrResponse = json_decode($this->_curl->rawResponse, true);
            $strCode = $arrResponse["code"] ?? "-1";
            if ($strCode != 10000) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'data' => $arrResponse,
                    'debug' => [
                        "request" => $arrQuery
                    ]
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

    public function getBanks()
    {
        try {
            // bankId=&businessId=19&merchantId=215&timestamp=1705859152791&version=2.0"

            $arrQuery = [
                "bankId" => 0,
                "merchantId" => $this->intMerchantId,
                "businessId" => $this->intBusinessId,
                "timestamp" => floor(microtime(true) * 1000),
                "version" => "2.0"
            ];
            $arrQuery["sign"] = $this->getSign($arrQuery);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->get($this->strUrl . '/trade/vn/transfer/getBankList?' . http_build_query($arrQuery));
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

    public function createCashOutOrder($arrParams = [])
    {

        try {

            $strCurrency = $arrParams["currency"] ?? "VND";
            $strBankLocation = $arrParams["bank_location"] ?? "VN";
            $strReturnUrl = $arrParams["return_url"] ?? "";
            $intAmount = $arrParams["amount"] ?? 0;
            $strOrderNo = $arrParams["order_no"] ?? 0;
            $intBankNo = $arrParams["bank_no"] ?? 0;
            $strAccountName = $arrParams["account_name"] ?? "";
            $strAccountNo = $arrParams["account_no"] ?? "";
            $strRemark = $arrParams["remark"] ?? "";
            $strIdNo = $arrParams["id_no"] ?? "";
            $strPhoneNumber = $arrParams["phone_number"] ?? "";


            $arrQuery = [
                "merchantId" => $this->intMerchantId,
                "businessId" => $this->intBusinessId,
                "timestamp" => floor(microtime(true) * 1000),
                "version" => "2.0",
                "returnUrl" => $strReturnUrl,
                "orderNo" => $strOrderNo,
                "currency" => $strCurrency,
                "clientId" => "",
                "amount" => $intAmount,
                "bankLocation" => $strBankLocation,
                "bankNo" => $intBankNo,
                "accountName" => $strAccountName,
                "accountNo" => $strAccountNo,
                "phoneNumber" => $strPhoneNumber,
                "accountType" => 0, //Benefit bank account type 0 -- Account number, 1--- Card number, not all banks support with card number transactions
                "remark" => $strRemark,
                "idNo" => $strIdNo,
            ];
            $arrQuery["sign"] = $this->getSign($arrQuery);
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/trade/vn/transfer/pay', $arrQuery);
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResponse = json_decode($this->_curl->rawResponse, true);
            $strCode = $arrResponse["code"] ?? "-1";
            if ($strCode != 10000) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'data' => $arrResponse,
                    'debug' => [
                        "request" => $arrQuery
                    ]
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

    public function getTransaction($arrParams = [])
    {
        try {

            $startTime = $arrParams["startTime"] ?? "";
            $endTime = $arrParams["endTime"] ?? "";
            $strOrderNo = $arrParams["orderNo"] ?? "";
            $strtradeNo = $arrParams["tradeNo"] ?? "";
            $intPageNumber = $arrParams["pageNum"] ?? 1;
            $intPageSize = $arrParams["pageSize"] ?? 10;
            $arrQuery = [
                "merchantId" => $this->intMerchantId,
                "businessId" => $this->intBusinessId,
                "startTime" => $startTime,
                "endTime" => $endTime,
                "pageNum" => $intPageNumber,
                "pageSize" => $intPageSize,
                "timestamp" => floor(microtime(true) * 1000),
                "version" => "2.0",
                "orderNo" => $strOrderNo,
                "tradeNo" => $strtradeNo
            ];
            $arrQuery["sign"] = $this->getSign($arrQuery);
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->get($this->strUrl . '/trade/vn/virtual/query?' . http_build_query($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResponse = json_decode($this->_curl->rawResponse, true);
            $strCode = $arrResponse["code"] ?? "-1";
            if ($strCode != 10000) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'data' => $arrResponse,
                    'debug' => [
                        "request" => $arrQuery
                    ]
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


    public function createVA($arrParams = [])
    {
        try {

            $strAccountName = $arrParams["userName"] ?? "";
            $returnUrl = $arrParams["returnUrl"] ?? "";
            $intUserId = $arrParams["user_id"] ?? 0;
            $arrQuery = [
                'accountBase' => '07' . time() . rand(1111, 9999),
                'amount' => 10000,
                'businessId' => $this->intBusinessId,
                'clientId' => 'member_' . $intUserId,
                'currency' => 'VND',
                'expireDate' => date('Ymd'),
                'idNo' => rand(888888888, 999999999),
                'feeId' => 25,
                'merchantId' => $this->intMerchantId,
                'orderNo' => \Str::random(5) . rand(11111, 99999),
                'phoneNumber' => '07' . time() . rand(1111, 9999),
                'returnUrl' => $returnUrl,
                'timestamp' => floor(microtime(true) * 1000),
                'userName' => $strAccountName,
                'version' => '2.0',
            ];
            $arrQuery["sign"] = $this->getSign($arrQuery);
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/trade/vn/virtual/create', json_encode($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResponse = json_decode($this->_curl->rawResponse, true);
            $strCode = $arrResponse["code"] ?? "-1";
            if ($strCode != 10000) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'data' => $arrResponse,
                    'debug' => [
                        "request" => $arrQuery
                    ]
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
                'error' => 'Error Exception: ' . $e->getLine() . " " . $e->getMessage(),
                'data' => [],
            ];
        }
    }


    public function updateVA($arrParams = [])
    {
        try {

            $strOrderNo = $arrParams["orderNo"] ?? "";
            $intStatus = $arrParams["status"] ?? 0;
            $arrQuery = [
                'merchantId' => $this->intMerchantId,
                'businessId' => $this->intBusinessId,
                'feeId' => 25,
                'orderNo' => $strOrderNo,
                'version' => '2.0',
                'timestamp' => floor(microtime(true) * 1000),
                'status' => $intStatus,
                'amount' => 10000
            ];
            if (!empty($arrParams["returnUrl"])) {
                $arrQuery["returnUrl"] = $arrParams["returnUrl"];
            }
            $arrQuery["sign"] = $this->getSign($arrQuery);
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/trade/vn/virtual/update', json_encode($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResponse = json_decode($this->_curl->rawResponse, true);
            $strCode = $arrResponse["code"] ?? "-1";
            if ($strCode != 10000) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'data' => $arrResponse,
                    'debug' => [
                        "request" => $arrQuery
                    ]
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
                'error' => 'Error Exception: ' . $e->getLine() . " " . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    public function getSign($arrData)
    {
        return General::getSign($arrData, $this->strSecretKey, $this->strPrivateKey);
    }

    public function verifySign($arrData)
    {
        return General::verifySign($arrData, $this->strSecretKey, $this->strPublicKey);
    }

    public function test1688pays()
    {
        $baseUrl = 'https://www.yoobil.com/yoobil';

        $privateKeyPath = base_path('yobill_1688pays_private_key.pem');
        $publicKeyPath = base_path('yobill_1688pays_rsa_public_key.pem');
        $secretKey = 'T700hW11NARV6SqP00TTx844Kd8bh748J5aLU4f9';

        $businessId = 47;
        $merchantId = 234;
        $this->setBusinessId($businessId)->setMerchantId($merchantId)->setSecretKey($secretKey)->setPrivateKey(file_get_contents($privateKeyPath))->setPublicKey(file_get_contents($publicKeyPath));
        $arrQuery = [
            'test' => '1'
        ];
        echo http_build_query($arrQuery);
        $arrQuery["sign"] = $this->getSign($arrQuery);
        dd($arrQuery);

    }

    public function test($strToken = '')
    {
        $baseUrl = 'https://www.yoobil.com/yoobil';

        $privateKeyPath = base_path('app/Utilities/Yoobil/rsa_private_key.pem');
        $publicKeyPath = base_path('app/Utilities/Yoobil/rsa_public_key.pem');
        $secretKey = 'j0D03AlJx67VPSr122581v8m75y68o0O8792EG14';

        $businessId = 19;
        $merchantId = 215;

        $arrPost = [
            'accountBase' => '07737441000',
            'amount' => 10000,
            'businessId' => $businessId,
            'clientId' => 'bina',
            'currency' => 'VND',
            'expireDate' => date('Ymd'),
            'idNo' => rand(888888888, 999999999),
            'feeId' => 25,
            'merchantId' => $merchantId,
            'orderNo' => \Str::random(5) . rand(11111, 99999),
            'phoneNumber' => '0773744115',
            'returnUrl' => 'https://uat.vnpay.biz/api/app-message/yoobil-forward?device=yoobil&token=e2676f0aaebb30547d677e76c94a12ec',
            'timestamp' => floor(microtime(true) * 1000),
            'userName' => 'TRAN QUANG KHAI',
            'version' => '2.0',
        ];

        ksort($arrPost);
        $strOpenSSLData = urldecode(http_build_query($arrPost)) . $secretKey;
        $strPrivateKey = file_get_contents($privateKeyPath);
        $strOpenSSLBinary = "";
        openssl_sign($strOpenSSLData, $strOpenSSLBinary, $strPrivateKey, "SHA256");
        $arrPost['sign'] = base64_encode($strOpenSSLBinary);


        echo "<textarea>$strOpenSSLData</textarea>";
        echo '<pre>';

        print_r($arrPost);

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post($baseUrl . '/trade/vn/virtual/create', json_encode($arrPost));

        if ($curl->error) {
            echo 'Error: ' . $curl->errorMessage . "\n";
        } else {
            $arData = $curl->rowResponse;
            $arData["code"] = 1409;
            $arData["msg"] = "The service is currently under maintenance.";
            print_r($arData);
        }

    }


    public function createGiaoNhan($strToken = '')
    {

        //  [accountName] => STAGING GIAONHAN
        //     [accountNo] => 9631242000001107979
        //     [amount] => 10000
        //     [bankName] => BIDV
        //     [businessId] => 25
        //     [currency] => VND
        //     [expireDate] => 
        //     [feeId] => 17
        //     [merchantId] => 210
        //     [orderNo] => 7TSTY66407
        //     [sign] => eAF03HrUM4K2GYxeQtQ6U0LLA2wwJhLy6fNCWzIuErUuLf4DKu9QyagYAsVqCspmQSB+Azt0gub11+WfQBJLfatPwRrL/Zhow6nCuxeUGX7+PlTAIgRDntW2dgbG++8LPaaBELf6YsJUExlNHq5Ew/5iSLmOKsz50VsyP2p2fcNz3oZm1yJtG9FY0KktPfUnXsWa0hasRZjjZ4ZFCzrSEtfkoWWht59JgHmXrb9E+wwRd2Hn7ra3hlXZbafQHhTaV/Fe8G9K2cPRGxyDPtesv8IAuosbY2tHIILrk/BUl61PNBqv0AlyrsxbmqboXX9PM5KsslV36OQRSt6uPpn6IA==
        //     [userName] => STAGING GIAONHAN
        // exit;
        // // exit;

        //  [accountName] => HONG HOANG PHI
        //     [accountNo] => 9631242000001108890
        //     [amount] => 10000
        //     [bankName] => BIDV
        //     [businessId] => 25
        //     [currency] => VND
        //     [expireDate] => 
        //     [feeId] => 17
        //     [merchantId] => 210
        //     [orderNo] => vy2Ya20840
        //     [sign] => FW5EZI4lknQ4eyak0oLDqkoWrbFSkW/948cDngsG7CUEWsvUM1+/GPpadFekITvUxkNEUJ6Qu57GrCEyJdc9TR2s2Kr544n8+uTqR09iN60MKvelVihc6lb10RQKKxCcxL2Wnh0tqLYp4YgyAPVHfTEABFNg4t5CwzVT7H62WmUYKfdH4jZUb0TI2Vdr7blpgYpl0q1s8K7coBEty14UF1wS02ee2B4wp+EsPwgQDEXx02seArLexu6OM0Jd/ucT0SQUV8v8vGM4MPcR8gKBiCsihBfgsnpxe9gVFE3e5k+0FmCpn9ClFdFyHi9vt+uQ/RHQkFNKU8D1amierc6T2Q==
        //     [userName] => HONG HOANG PHI
        $baseUrl = 'https://www.yoobil.com/yoobil';

        $privateKeyPath = base_path('app/Utilities/Yoobil/rsa_private_key_giaonhan_2.pem');
        $publicKeyPath = base_path('app/Utilities/Yoobil/rsa_public_key_giaonhan_2.pem');
        $secretKey = '5J68sSLn0m1P0Y10a93f055R0u41ghF33uR7Xj22';

        $businessId = 25;
        $merchantId = 210;

        $arrPost = [
            'accountBase' => '07737441130',
            'amount' => 10000,
            'businessId' => $businessId,
            'clientId' => 'bina',
            'currency' => 'VND',
            'expireDate' => date('Ymd'),
            'idNo' => rand(888888888, 999999999),
            'feeId' => 17, // nofee
            'merchantId' => $merchantId,
            'orderNo' => \Str::random(5) . rand(11111, 99999),
            'phoneNumber' => '0773744130',
            'returnUrl' => 'https://uat.ribato.com/api/app-message/yoobil-forward?device=yoobil&token=68b7e21a974a8faa965d8b35ef1131c2	',
            'timestamp' => floor(microtime(true) * 1000),
            'userName' => 'GIAONHAN247',
            'version' => '2.0',
        ];

        ksort($arrPost);
        $strOpenSSLData = urldecode(http_build_query($arrPost)) . $secretKey;
        $strPrivateKey = file_get_contents($privateKeyPath);
        $strOpenSSLBinary = "";
        openssl_sign($strOpenSSLData, $strOpenSSLBinary, $strPrivateKey, "SHA256");
        $arrPost['sign'] = base64_encode($strOpenSSLBinary);


        echo "<textarea>$strOpenSSLData</textarea>";
        echo '<pre>';
        print_r($arrPost);

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post($baseUrl . '/trade/vn/virtual/create', json_encode($arrPost));

        if ($curl->error) {
            echo 'Error: ' . $curl->errorMessage . "\n";
        } else {
            print_r($curl->response);
        }

    }
}