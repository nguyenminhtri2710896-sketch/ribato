<?php

namespace App\Utilities;

class General
{

    public static function formatNumber84($strPhone)
    {
        if (!is_numeric($strPhone)) {
            return strtolower($strPhone);
        }

        $strPhone = trim($strPhone);
        if (empty($strPhone)) {
            return false;
        }

        if (substr($strPhone, 0, 1) == '0') {
            $strPhone = substr($strPhone, 1, strlen($strPhone));
        }

        if (strlen($strPhone) <= 10) {
            $strPhone = '84' . $strPhone;
        }
        return $strPhone;
    }

    public static function formatNumber($strPhone = 0)
    {
        $strPhone = trim($strPhone);
        if (empty($strPhone)) {
            return false;
        }

        if (strlen($strPhone) >= 10 && substr($strPhone, 0, 2) == '84') {
            $strPhone = substr($strPhone, 2, strlen($strPhone));
        }

        if (substr($strPhone, 0, 1) != 0) {
            $strPhone = '0' . $strPhone;
        }
        return $strPhone;
    }

    public static function detectTelcoBySubscriber($strSubscriber = '')
    {

        $strSubscriber = trim($strSubscriber);
        if (empty($strSubscriber)) {
            return false;
        }

        if (strlen($strSubscriber) > 10 && substr($strSubscriber, 0, 2) == '84') {
            $strSubscriber = substr($strSubscriber, 2, strlen($strSubscriber));
        }

        if (substr($strSubscriber, 0, 1) != 0) {
            $strSubscriber = '0' . $strSubscriber;
        }
        /**
         * Detect đầu số cũ, thay đầu số mới
         */

        $arrHeadPhone = [
            '0162' => '032',
            '0163' => '033',
            '0164' => '034',
            '0165' => '035',
            '0166' => '036',
            '0167' => '037',
            '0168' => '038',
            '0169' => '039',
            '0123' => '083',
            '0124' => '084',
            '0125' => '085',
            '0127' => '081',
            '0129' => '082',
            '0120' => '070',
            '0121' => '079',
            '0122' => '077',
            '0126' => '076',
            '0128' => '078',
            '0199' => '059'
        ];
        $strHeadNumber = substr($strSubscriber, 0, 4);
        if (!empty($arrHeadPhone[$strHeadNumber])) {
            $strHeadNumber = $arrHeadPhone[$strHeadNumber];
        }
        $strHeadNumber = substr($strSubscriber, 0, 3);
        /**
         * Detect nhà mạng
         */
        $arrTelco = [
            '086' => 'viettel',
            '096' => 'viettel',
            '097' => 'viettel',
            '098' => 'viettel',
            '032' => 'viettel',
            '033' => 'viettel',
            '034' => 'viettel',
            '035' => 'viettel',
            '036' => 'viettel',
            '037' => 'viettel',
            '038' => 'viettel',
            '039' => 'viettel',
            '088' => 'vinaphone',
            '091' => 'vinaphone',
            '094' => 'vinaphone',
            '083' => 'vinaphone',
            '084' => 'vinaphone',
            '085' => 'vinaphone',
            '081' => 'vinaphone',
            '082' => 'vinaphone',
            '089' => 'mobifone',
            '090' => 'mobifone',
            '093' => 'mobifone',
            '070' => 'mobifone',
            '079' => 'mobifone',
            '077' => 'mobifone',
            '076' => 'mobifone',
            '078' => 'mobifone',
            '092' => 'vietnamobile',
            '056' => 'vietnamobile',
            '058' => 'vietnamobile',
            '099' => 'gmobile',
            '059' => 'gmobile'
        ];
        return $arrTelco[$strHeadNumber] ?? 'unknown';
    }
    public static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }



    public static function formatInputDay($input = "", $format = "Y/m/d H:i:s")
    {
        $input = str_replace("-", "/", $input);
        /**
         * Detect date
         */
        $arrDate = explode(" ", $input);
        $strHouse = $arrDate[1] ?? "00:00:00";
        $strDate = $arrDate[0] ?? "";
        $arrDate = explode("/", $arrDate[0] ?? "");


        if (strlen($arrDate[0]) >= 4) {
            // yyyy/mm/dd
            return date($format, strtotime($strDate . " " . $strHouse));
        }

        if (strlen($arrDate[2]) >= 4) {
            // dd/mm/yyyy
            $strDate = implode("/", array_reverse($arrDate));
            return date($format, strtotime($strDate . " " . $strHouse));
        }
        return "";
    }

    public static function getContentNone($string)
    {
        $arrCharFrom = array("ạ", "á", "à", "ả", "ã", "Ạ", "Á", "À", "Ả", "Ã", "â", "ậ", "ấ", "ầ", "ẩ", "ẫ", "Â", "Ậ", "Ấ", "Ầ", "Ẩ", "Ẫ", "ă", "ặ", "ắ", "ằ", "ẳ", "ẵ", "ẫ", "Ă", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "Ẵ", "ê", "ẹ", "é", "è", "ẻ", "ẽ", "Ê", "Ẹ", "É", "È", "Ẻ", "Ẽ", "ế", "ề", "ể", "ễ", "ệ", "Ế", "Ề", "Ể", "Ễ", "Ệ", "ọ", "ộ", "ổ", "ỗ", "ố", "ồ", "Ọ", "Ộ", "Ổ", "Ỗ", "Ố", "Ồ", "Ô", "ô", "ó", "ò", "ỏ", "õ", "Ó", "Ò", "Ỏ", "Õ", "ơ", "ợ", "ớ", "ờ", "ở", "ỡ", "Ơ", "Ợ", "Ớ", "Ờ", "Ở", "Ỡ", "ụ", "ư", "ứ", "ừ", "ử", "ữ", "ự", "Ụ", "Ư", "Ứ", "Ú", "Ừ", "Ử", "Ữ", "Ự", "ú", "ù", "ủ", "ũ", "Ú", "Ù", "Ủ", "Ũ", "ị", "í", "ì", "ỉ", "ĩ", "Ị", "Í", "Ì", "Ỉ", "Ĩ", "ỵ", "ý", "ỳ", "ỷ", "ỹ", "Ỵ", "Ý", "Ỳ", "Ỷ", "Ỹ", "đ", "Đ", "Ó");
        $arrCharEnd = array("a", "a", "a", "a", "a", "A", "A", "A", "A", "A", "a", "a", "a", "a", "a", "a", "A", "A", "A", "A", "A", "A", "a", "a", "a", "a", "a", "a", "a", "A", "A", "A", "A", "A", "A", "A", "e", "e", "e", "e", "e", "e", "E", "E", "E", "E", "E", "E", "e", "e", "e", "e", "e", "E", "E", "E", "E", "E", "o", "o", "o", "o", "o", "o", "O", "O", "O", "O", "O", "O", "O", "o", "o", "o", "o", "o", "O", "O", "O", "O", "o", "o", "o", "o", "o", "o", "O", "O", "O'", "O", "O", "O", "u", "u", "u", "u", "u", "u", "u", "U", "U", "U", "U", "U", "U", "U", "U", "u", "u", "u", "u", "U", "U", "U", "U", "i", "i", "i", "i", "i", "I", "I", "I", "I", "I", "y", "y", "y", "y", "y", "Y", "Y", "Y", "Y", "Y", "d", "D", "O");
        return str_replace($arrCharFrom, $arrCharEnd, $string);
    }

    public static function minifyKey($strKey = "")
    {
        $strKey = trim(preg_replace('/\s+/', '', $strKey));
        $strKey = preg_replace('/-----.*?-----/', "", $strKey);
        return $strKey;

    }

    public static function beautyKey($strKey = "", $keyName = "PUBLIC KEY")
    {


        $strKey = self::minifyKey($strKey);
        return "-----BEGIN $keyName-----\n" . chunk_split($strKey, 64, "\n") . "-----END $keyName-----\n";

    }

    public static function httpBuildQuery($array = [])
    {
        if (!is_array($array)) {
            return http_build_query([]);
        }
        return http_build_query(self::nullToEmptyString($array));
    }

    public static function nullToEmptyString($array = [])
    {

        if (empty($array)) {
            return "";
        }
        $arrTemp = [];
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $arrTemp[$key] = self::nullToEmptyString($val);
                continue;
            }
            $arrTemp[$key] = $val === null ? '' : $val;
        }
        return $arrTemp;
    }

    public static function getSign($arrData, $strSecretKey, $strPrivateKey)
    {

        if (!empty($arrData["sign"])) {
            unset($arrData['sign']);
        }
        ksort($arrData);
        $strOpenSSLData = urldecode(self::httpBuildQuery($arrData)) . $strSecretKey;
        $strOpenSSLBinary = "";
        openssl_sign($strOpenSSLData, $strOpenSSLBinary, $strPrivateKey, "SHA256");
        return base64_encode($strOpenSSLBinary);
    }

    public static function verifySign($arrData, $strSecretKey, $strPublicKey)
    {
        if (empty($arrData['sign'])) {
            return false;
        }
        $strSign = $arrData['sign'];
        unset($arrData['sign']);
        ksort($arrData);
        $verified = openssl_verify(urldecode(self::httpBuildQuery($arrData)) . $strSecretKey, base64_decode($strSign), openssl_pkey_get_public($strPublicKey), OPENSSL_ALGO_SHA256);
        if ($verified === 1) {
            return true;
        }
        return false;
    }


    public static function getSignDebug($arrData, $strSecretKey, $strPrivateKey)
    {

        if (!empty($arrData["sign"])) {
            unset($arrData['sign']);
        }
        if (is_array($arrData)) {
            ksort($arrData);
            $strOpenSSLData = urldecode(self::httpBuildQuery($arrData)) . $strSecretKey;
        } else {
            $strOpenSSLData = $arrData . $strSecretKey;
        }
        $strOpenSSLBinary = "";
        openssl_sign($strOpenSSLData, $strOpenSSLBinary, $strPrivateKey, "SHA256");
        return base64_encode($strOpenSSLBinary);
    }


    public static function removeSpecialUnicode($str)
    {
        // Loại ký hiệu đặc biệt, giữ chữ Unicode, số, khoảng trắng, dấu cơ bản
        return preg_replace('/[^\p{L}\p{N}\s\.\,\!\?\-\_]/u', '', $str);
    }

}
