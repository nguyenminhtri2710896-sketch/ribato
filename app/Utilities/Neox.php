<?php

namespace App\Utilities;

use Curl\Curl;

class Neox
{
    private $strUrl = 'https://sandbox-api.neopay.vn';
    private $_curl = null;
    private $strClientId = "d4ed3a8040abc3fd2656e1b885a3b333f194912b";
    private $strClientSecretKey = "503d54b432b6ec0ae80caec6135cf120c302ce9f7987b8030a4f77620977ca4c140515c93530d890";
    private $strAuthentication = null;

    public function __construct()
    {

        $this->_curl = new Curl();
        $this->_curl->setTimeout(80);
        $this->_curl->setConnectTimeout(80);
    }
    public function setAuthentication($strAuthentication)
    {
        $this->strAuthentication = $strAuthentication;
        return $this;
    }

    public function setClientId($strClientId)
    {
        $this->strClientId = $strClientId;
        return $this;
    }

    public function setClientSecretKey($strClientSecretKey)
    {
        $this->strClientSecretKey = $strClientSecretKey;
        return $this;
    }

    public function getClientSecretKey()
    {
        return  $this->strClientSecretKey;
    }

    public function createAccessToken()
    {
        try {
            $arrQuery = [
                "client_id" => $this->strClientId,
                "client_secret" => $this->strClientSecretKey,
                "scope" => "collection",
                "grant_type" => "client_credentials"
            ];
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->post($this->strUrl . '/oapi/v2/auth/oauth2/token', json_encode($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }
            $arrResult = json_decode($this->_curl->rawResponse, true);
            $strCode   = $arrResult["code"] ?? 0;
            if ($strCode != 1) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'data' => $arrResult,
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

    public function createVA($arrParrams = [])
    {

        if (empty($arrParrams["account_name"])) {
            return [
                "success" => false,
                "message" => "Vui lòng nhập Account Name"
            ];
        }
        try {
            $strRequestId   = \Str::uuid();
            $strAccountName = $arrParrams["account_name"];
            $strDesc        = $arrParrams["desc"] ?? "";
            $strGroupId     = $arrParrams["group_id"] ?? "default";
            $strAddress     = $arrParrams["address"] ?? "no address";

            $arrQuery = [
                "requestId" => $strRequestId,
                "virtualAccounts" => [
                    [
                        "accountName" => $strAccountName,
                        "virtualAccountRequestId" => $strRequestId,
                        "accountAddress" => $strAddress,
                        "serviceInformation" => [
                            "code" => "VND",
                            "desc" => $strDesc,
                            "groupId" => $strGroupId
                        ]
                    ]
                ]
            ];
            $this->_curl->setHeader('Content-Type', 'application/json');
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->_curl->setHeader('Authorization', "Bearer " . $this->strAuthentication);
            $this->_curl->post($this->strUrl . '/oapi/v2/col/requests', json_encode($arrQuery));
            if ($this->_curl->error) {
                return [
                    'success' => false,
                    'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'error' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                    'data' => $this->_curl->rawResponse
                ];
            }
            $arrResult = json_decode($this->_curl->rawResponse, true);
            $strCode   = $arrResult["code"] ?? 0;
            if ($strCode != 1) {
                return [
                    'success' => false,
                    'message' => 'Thất bại',
                    'data' => $arrResult,
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
}