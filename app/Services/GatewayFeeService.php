<?php

namespace App\Services;

use App\Models\GatewayFee;

class GatewayFeeService extends AbstractService
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
}