<?php

namespace App\Services;

use App\Models\GatewayFee;
use Curl\Curl;
use Illuminate\Support\Facades\Validator;


class GatewayForwardService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {
        $this->arrFillable = (new GatewayFee())->getFillable();
    }

    public static $arrStatusId = [
        1 => [
            'name' => 'Đang bảo trì'
        ],
        2 => [
            'name' => 'Hoạt động'
        ]
    ];

    //1: phí cố định in, 2: phí cố định out, 3: phí % in, 4: phí % out
    public static $arrTypeId = [
        1 => [
            'name' => 'Phí cố định in'
        ],
        2 => [
            'name' => 'Phí cố định out'
        ],
        3 => [
            'name' => 'Phí % in'
        ],
        4 => [
            'name' => 'Phí % out'
        ]
    ];


    public function sendRibato($arrParams = [])
    {


        $validated = Validator::make(
            $arrParams,
            [
                "url_forward" => "required",
                "params" => "required",
            ],
            [

                "url_forward.required" => __("Vui lòng nhập url_forward."),
                "params.required" => __("Vui lòng nhập params."),
            ]
        );
        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $strUrlForward        = $arrParams["url_forward"];
        $arrParams            = $arrParams["params"];
        if(isset($arrParams["q"])){
            unset($arrParams["q"]);
        }

        if (isset($arrParams["token"])) {
            unset($arrParams["token"]);
        }

        $_curl = new Curl();
        $_curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
        $_curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
        $_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $_curl->setOpt(CURLOPT_ENCODING, "");
        // $_curl->setHeader('Content-Type', 'application/json');
        $_curl->setHeader('User-agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36');
        $_curl->setTimeout(90);
        $_curl->setConnectTimeout(90);
        $_curl->post($strUrlForward, $arrParams);
        if ($_curl->error) {
            return $this->setStatusCode(404)->setMessage("")->setData($arrParams)->setErrors([
                [__($_curl->errorCode . ': ' . $_curl->errorMessage)]
            ])->result();
        }

        return $this->setStatusCode(0)->setMessage(__("Thành công"))->setData([
            'response' => $_curl->rawResponse,
        ])->result();
    }
}