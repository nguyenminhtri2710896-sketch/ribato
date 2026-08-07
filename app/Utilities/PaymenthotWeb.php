<?php

namespace App\Utilities;

use Curl\Curl;

class PaymenthotWeb
{
    private $strUrl = 'https://merchant.paymenthot.com';
    private $_curl = null;
    private $strUsername = null;
    private $strPassword = null;
    private $strAuthorization = null;

    public function __construct()
    {

        $this->_curl = new Curl();
        $this->_curl->setTimeout(80);
        $this->_curl->setConnectTimeout(80);
    }

    public function setAuthorization($strAuthorization)
    {
        $this->strAuthorization = $strAuthorization;
        return $this;
    }

    public function setUsername($strUsername)
    {
        $this->strUsername = $strUsername;
        return $this;
    }

    public function setPassword($strPassword)
    {
        $this->strPassword = $strPassword;
        return $this;
    }


    public function login()
    {

        try {
            $arrQuery = [
                "username" => $this->strUsername,
                "password" => $this->strPassword,
            ];
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/api/user/login', json_encode($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResult = json_decode($this->_curl->rawResponse, true);
            if (empty($arrResult["success"])) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'error' => 'Thất bại',
                    'data' => $arrResult
                ];
            }
            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResult["data"]
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

    public function merchantTranferHistory()
    {

        try {
            $arrQuery = [
                "pageSize" => 10,
                "type" => "CASH_IN",
                "status" => "SUCCESS",
            ];
            $this->_curl->setHeader('Authorization', 'Bearer ' . $this->strAuthorization);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->get($this->strUrl . '/api/merchant-transfer/history?' . http_build_query($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            $arrResult = json_decode($this->_curl->rawResponse, true);
            if (empty($arrResult["success"])) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'error' => 'Thất bại',
                    'data' => $arrResult
                ];
            }
            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResult["data"]
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

    public function merchantTranferVerify($arrParams = [])
    {
        try {
            $arrQuery = [
                "bankId" => $arrParams["bankId"] ?? "",
                "bankRefNumber" => $arrParams["bankRefNumber"] ?? "",
            ];
            $this->_curl->setHeader('Authorization', 'Bearer ' . $this->strAuthorization);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/api/merchant-transfer/verify', json_encode($arrQuery));
            $arrResult = json_decode($this->_curl->rawResponse, true);
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . ($arrResult["message"] ?? "") . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            if (empty($arrResult["data"]["success"])) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'error' => 'Thất bại',
                    'data' => $arrResult
                ];
            }
            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResult["data"]["data"] ?? []
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

    public function merchantTranferImploreTransfer247($arrParams = [])
    {

        try {
            $arrQuery = [
                "username" => $arrParams["username"] ?? "",
                "passCode" => $arrParams["passCode"] ?? "",
            ];
            $this->_curl->setHeader('Authorization', 'Bearer ' . $this->strAuthorization);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/api/merchant-transfer/implore-transfer-247', json_encode($arrQuery));
            $arrResult = json_decode($this->_curl->rawResponse, true);
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . ($arrResult["message"] ?? "") . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            if (empty($arrResult["data"]["success"])) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'error' => 'Thất bại',
                    'data' => $arrResult
                ];
            }
            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResult["data"]["data"]
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

    public function merchantTransfer247($arrParams = [])
    {
        try {
            $arrQuery = [
                "body" => [
                    "audit" => $arrParams["audit"] ?? "",
                    "amount" => $arrParams["amount"] ?? 0,
                    "bankCode" => $arrParams["bankCode"] ?? "",
                    "passCode" => $arrParams["passCode"] ?? "",
                    "username" => $arrParams["username"] ?? "",
                    "bankId" => $arrParams["bankId"] ?? "",
                    "bankRefName" => $arrParams["bankRefName"] ?? "",
                    "bankRefNumber" => $arrParams["bankRefNumber"] ?? "",
                    "content" => $arrParams["content"] ?? "",
                ],
                "verifyKey" => $arrParams["verifyKey"] ?? "",
            ];


            $this->_curl->setHeader('Authorization', 'Bearer ' . $this->strAuthorization);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/api/merchant-transfer/transfer-247', json_encode($arrQuery));
            $arrResult = json_decode($this->_curl->rawResponse, true);
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . ($arrResult["message"] ?? "") . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }


            if (empty($arrResult["data"]["success"])) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'error' => 'Thất bại',
                    'data' => $arrResult
                ];
            }
            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResult["data"]["data"]
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


    public function getTotalBalance()
    {
        try {
            $this->_curl->setHeader('Authorization', 'Bearer ' . $this->strAuthorization);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->get($this->strUrl . '/api/balance/total-balance');
            $arrResult = json_decode($this->_curl->rawResponse, true);
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . ($arrResult["message"] ?? "") . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }

            if (empty($arrResult["data"]["success"])) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'error' => 'Thất bại',
                    'data' => $arrResult
                ];
            }
            return [
                'success' => true,
                'message' => 'Thành công',
                'data' => $arrResult["data"]["data"]
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