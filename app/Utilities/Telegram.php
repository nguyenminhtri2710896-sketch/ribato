<?php

namespace App\Utilities;

use Curl\Curl;

class Telegram
{
    private $strUrl = 'https://api.telegram.org';
    private $_curl = null;
    private $strToken = null;
    public function __construct()
    {

        $this->_curl = new Curl();
        $this->_curl->setTimeout(40);
        $this->_curl->setConnectTimeout(40);
        $this->_curl->setUserAgent('Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36');
    }

    public function setToken($strToken = '')
    {

        $this->strToken = $strToken;
        return $this;
    }

    public function sendMessage($arrParams = [])
    {


        $intChatId = $arrParams["chat_id"] ?? 0;
        $strMessage = $arrParams["message"] ?? "";


        $this->_curl->get("$this->strUrl/bot$this->strToken/sendMessage?chat_id=$intChatId&text=" . urlencode($strMessage) . "&parse_mode=HTML");
        if ($this->_curl->error) {
            return [
                'success' => false,
                'message' => 'Thất bại (' . $this->_curl->errorCode . ': ' . $this->_curl->errorMessage . ')',
                'error' => $this->_curl->errorCode . ': ' . $this->_curl->errorMessage,
                'data' => $this->_curl->rawResponse,
            ];
        }

        return [
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'error' =>  'Đăng nhập thành công',
            'data' => json_decode($this->_curl->rawResponse, true),
        ];
    }
}
