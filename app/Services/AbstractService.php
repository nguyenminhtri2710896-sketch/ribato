<?php

namespace App\Services;

use App\Utilities\General;

class AbstractService
{
    const ARRAY_MESSAGE = [
        0 => 'Thành công.',
        403 => 'Bạn không có quyền thực hiện chức năng này.',
        404 => 'Có lỗi xảy ra.',
        414 => 'Thông tin đầu vào không chính xác.',
        502 => 'Hệ thống lỗi.',
        100 => 'Xác thực lỗi.',
        101 => 'Yêu cầu đăng nhập bảo mật 2 lớp.',
        405 => 'Sai phương thức.',
        401 => 'Token hết hạn.',
        400 => 'Token không tồn tại.',
        505 => 'Dectypt data lỗi.',
        808 => 'Giao dịch đã xác nhận rồi.',
        809 => 'Callback lỗi.',
    ];


    private $arrData = [];
    private $arrErrors = [];
    private $intStatusCode = 0;
    private $strMessage = '';

    public function setData($arrData)
    {
        $this->arrData = $arrData;
        return $this;
    }

    public function setErrors($arrErrors)
    {
        $this->arrErrors = $arrErrors;
        return $this;
    }

    public function setStatusCode($intStatusCode)
    {
        $this->intStatusCode = $intStatusCode;
        return $this;
    }

    public function setMessage($strMessage)
    {
        $this->strMessage = $strMessage;
        return $this;
    }

    public function result()
    {

        $arrErrorMessage = [];
        if ($this->arrErrors) {
            foreach ($this->arrErrors as $errors) {
                foreach ($errors as $error) {
                    $arrErrorMessage[] = trim($error, ".");
                }
            }
        }

        $strMessage = self::ARRAY_MESSAGE[$this->intStatusCode];
        if (!empty($arrErrorMessage)) {
            $strMessage = implode("<br/>", $arrErrorMessage);
        }

        if (!empty($this->strMessage)) {
            $strMessage = $this->strMessage;
        }

        $strTranIdTracking = md5(crc32(time() . rand(1000000000, 999999999)));
        $arrReturn         = [
            'error_code' => $this->intStatusCode,
            'system_time' => date('Y-m-d H:i:s'),
            'message' => $strMessage,
            'errors' => $arrErrorMessage,
            'data' => !empty($this->arrData) ? $this->arrData : null,
            'tranid_tracking' => $strTranIdTracking
        ];

        /**
         * Sau này ghi log strTranIdTracking
         */

        return $arrReturn;
    }


    public function getListBuilder($objModel = null, $arrParams = [], $arrFillable = [])
    {

        /**
         * Định nghĩa một số query 
         */
        if (!empty($arrParams["query"]['created_at_from'])) {
            $arrParams["query_greater_than"]["created_at"] = General::formatInputDay($arrParams["query"]['created_at_from'] . " 00:00:00");
            unset($arrParams["query"]["created_at_from"]);
        }

        if (!empty($arrParams["query"]['created_at_to'])) {
            $arrParams["query_less_than_equato"]["created_at"] = General::formatInputDay($arrParams["query"]['created_at_to'] . " 23:59:59");
            unset($arrParams["query"]["created_at_to"]);
        }

        if (!empty($arrParams["query"]['report_at_from'])) {
            $arrParams["query_greater_than_equato"]["report_at"] = General::formatInputDay($arrParams["query"]['report_at_from'] . " 00:00:00");
            unset($arrParams["query"]["report_at_from"]);
        }

        if (!empty($arrParams["query"]['report_at_to'])) {
            $arrParams["query_less_than_equato"]["report_at"] = General::formatInputDay($arrParams["query"]['report_at_to'] . " 23:59:59");
            unset($arrParams["query"]["report_at_to"]);
        }


        if (!empty($arrParams["query"]['updated_at_from'])) {
            $arrParams["query_greater_than"]["updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_from'] . " 00:00:00");
            unset($arrParams["query"]["updated_at_from"]);
        }

        if (!empty($arrParams["query"]['updated_at_to'])) {
            $arrParams["query_less_than"]["updated_at"] = General::formatInputDay($arrParams["query"]['updated_at_to'] . " 23:59:59");
            unset($arrParams["query"]["updated_at_to"]);
        }

        if (!empty($arrParams["query"]['received_at_from'])) {
            $arrParams["query_greater_than"]["received_at"] = General::formatInputDay($arrParams["query"]['received_at_from'] . " 00:00:00");
            unset($arrParams["query"]["received_at_from"]);
        }

        if (!empty($arrParams["query"]['received_at_to'])) {
            $arrParams["query_less_than"]["received_at"] = General::formatInputDay($arrParams["query"]['received_at_to'] . " 23:59:59");
            unset($arrParams["query"]["received_at_to"]);
        }

        /**
         * QUERY
         */

        if (!empty($arrParams["query"])) {
            foreach ($arrParams["query"] as $whereCol => $whereValue) {
                if ($whereValue == null) {
                    continue;
                }

                if (!in_array($whereCol, $arrFillable)) {
                    continue;
                }

                $objModel = $objModel->where($whereCol, $whereValue);
            }
        }

        if (!empty($arrParams["query_greater_than"])) {
            foreach ($arrParams["query_greater_than"] as $whereCol => $whereValue) {
                if ($whereValue == null) {
                    continue;
                }

                if (!in_array($whereCol, $arrFillable)) {
                    continue;
                }

                $objModel = $objModel->where($whereCol, ">", $whereValue);
            }
        }

        if (!empty($arrParams["query_greater_than_equato"])) {
            foreach ($arrParams["query_greater_than_equato"] as $whereCol => $whereValue) {
                if ($whereValue == null) {
                    continue;
                }

                if (!in_array($whereCol, $arrFillable)) {
                    continue;
                }

                $objModel = $objModel->where($whereCol, ">=", $whereValue);
            }
        }

        if (!empty($arrParams["query_less_than"])) {
            foreach ($arrParams["query_less_than"] as $whereCol => $whereValue) {
                if ($whereValue == null) {
                    continue;
                }

                if (!in_array($whereCol, $arrFillable)) {
                    continue;
                }

                $objModel = $objModel->where($whereCol, "<", $whereValue);
            }
        }

            if (!empty($arrParams["query_less_than_equato"])) {
            foreach ($arrParams["query_less_than_equato"] as $whereCol => $whereValue) {
                if ($whereValue == null) {
                    continue;
                }

                if (!in_array($whereCol, $arrFillable)) {
                    continue;
                }

                $objModel = $objModel->where($whereCol, "<=", $whereValue);
            }
        }

        if (!empty($arrParams["query_difference"])) {
            foreach ($arrParams["query_difference"] as $whereCol => $whereValue) {
                if ($whereValue == null) {
                    continue;
                }

                if (!in_array($whereCol, $arrFillable)) {
                    continue;
                }

                $objModel = $objModel->where($whereCol, "<>", $whereValue);
            }
        }

        if (!empty($arrParams["query_not_in_list"])) {
            foreach ($arrParams["query_not_in_list"] as $whereCol => $whereValue) {
                if ($whereValue == null) {
                    continue;
                }

                if (!in_array($whereCol, $arrFillable)) {
                    continue;
                }

                $objModel = $objModel->whereNotIn($whereCol, $whereValue);
            }
        }

        if (!empty($arrParams["query_in_list"])) {
            foreach ($arrParams["query_in_list"] as $whereCol => $whereValue) {
                if ($whereValue == null) {
                    continue;
                }

                if (!is_array($whereValue)) {
                    $whereValue = explode(',', $whereValue);
                }

                if (!in_array($whereCol, $arrFillable)) {
                    continue;
                }

                $objModel = $objModel->whereIn($whereCol, $whereValue);
            }
        }

        if (!empty($arrParams["query_like"])) {
            foreach ($arrParams["query_like"] as $whereCol => $whereValue) {
                if ($whereValue == null) {
                    continue;
                }

                if (!in_array($whereCol, $arrFillable)) {
                    continue;
                }

                $objModel = $objModel->whereRaw($whereCol . ' like ?', "%$whereValue%");
            }
        }


        if (!empty($arrParams["query_or_like"])) {
            $arrQuery = $arrParams["query_or_like"];

            $objModel = $objModel->where(function ($query) use ($arrQuery, $arrFillable) {
                foreach ($arrQuery as $whereCol => $whereValue) {
                    if ($whereValue == null) {
                        continue;
                    }
                    if (!in_array($whereCol, $arrFillable)) {
                        continue;
                    }
                    $query = $query->orWhereRaw($whereCol . ' like ?', "%$whereValue%");
                }
            });

        }

        if (!empty($arrParams["query_not_like"])) {
            $arrQuery = $arrParams["query_not_like"];

            $objModel = $objModel->where(function ($query) use ($arrQuery, $arrFillable) {
                foreach ($arrQuery as $whereCol => $whereValue) {
                    if ($whereValue == null) {
                        continue;
                    }
                    if (!in_array($whereCol, $arrFillable)) {
                        continue;
                    }
                    $query = $query->orWhereRaw($whereCol . ' not like ?', "$whereValue");
                }
            });

        }

        // sort data
        if (!empty($arrParams["sort"])) {
            foreach ($arrParams["sort"] as $sortCol => $sortValue) {
                if (!in_array($sortCol, $arrFillable)) {
                    continue;
                }
                $objModel = $objModel->orderBy($sortCol, $sortValue);
            }
        }

        return $objModel;
    }

    public static function formatNumber84($strPhone)
    {
        $strPhone = trim($strPhone);
        if (!is_numeric($strPhone)) {
            return $strPhone;
        }

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
        if (!is_numeric($strPhone)) {
            return $strPhone;
        }
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

        $strSubscriber = self::formatNumber($strSubscriber);
        /**
         * Detect đầu số cũ, thay đầu số mới
         */

        $arrHeadPhone  = [
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
            '086' => 3,
            '096' => 3,
            '097' => 3,
            '098' => 3,
            '032' => 3,
            '033' => 3,
            '034' => 3,
            '035' => 3,
            '036' => 3,
            '037' => 3,
            '038' => 3,
            '039' => 3,
            '088' => 2,
            '091' => 2,
            '094' => 2,
            '083' => 2,
            '084' => 2,
            '085' => 2,
            '081' => 2,
            '082' => 2,
            '089' => 1,
            '090' => 1,
            '093' => 1,
            '070' => 1,
            '079' => 1,
            '077' => 1,
            '076' => 1,
            '078' => 1,
            '092' => 4,
            '056' => 4,
            '058' => 4,
            '099' => 5,
            '059' => 5
        ];
        return $arrTelco[$strHeadNumber] ?? 0;
    }

    public static function detectTelco($strTelco = '')
    {
        $strTelco = strtolower($strTelco);
        if (strpos($strTelco, 'viettel') !== false) {
            return [
                'name' => 'Viettel',
                'id' => 3
            ];
        } elseif (strpos($strTelco, 'mobifone') !== false) {
            return [
                'name' => 'Mobifone',
                'id' => 1
            ];
        } elseif (strpos($strTelco, 'vinaphone') !== false) {
            return [
                'name' => 'VinaPhone',
                'id' => 2
            ];
        } elseif (strpos($strTelco, 'vietnamobile') !== false) {
            return [
                'name' => 'Vietnamobile',
                'id' => 4
            ];
        }
        return [
            'name' => 'NULL',
            'id' => 99
        ];
    }

    public static function detectSubscriberNumber($strMessage = '')
    {
        $strMessage = str_replace(["\r\n", "\r", "\n", "\"", '""'], " ", $strMessage);
        $strMessage = trim($strMessage);
        preg_match("/(.*?)\. TKG/", $strMessage, $arrSubscriber);
        if (empty($arrSubscriber[1])) {
            preg_match("/So TB (.*?) /", $strMessage, $arrSubscriber);
        }

        // +84767509137. MobiQ . TKC 10000 d, TK no 0VND, HSD: 00:00 30-08-2023. QK khong co TK Khuyen mai.
        if (empty($arrSubscriber[1])) {
            preg_match("/\+(.*?)\. (MobiQ)/", $strMessage, $arrSubscriber);
        }

        return self::formatNumber84($arrSubscriber[1] ?? "");
    }

    public static function getFilterParams($arrParams = [], $arrFilter = [])
    {
        $arrParamTemp = [];
        foreach ($arrParams as $key => $value) {
            if ($key == "id") {
                continue;
            }

            if (in_array($key, $arrFilter)) {

                $arrParamTemp[$key] = $value;
            }
        }
        return $arrParamTemp;
    }

}