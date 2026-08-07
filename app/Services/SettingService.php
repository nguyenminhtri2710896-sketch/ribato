<?php

namespace App\Services;


use App\Models\User;
use Dolondro\GoogleAuthenticator\GoogleAuthenticator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Dolondro\GoogleAuthenticator\SecretFactory;
// use Endroid\QrCode\Builder\Builder;
// use Endroid\QrCode\Encoding\Encoding;
// use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevel;
// use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
// use Endroid\QrCode\Label\Font\NotoSans;
// use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeMode;
// use Endroid\QrCode\Writer\PngWriter;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;


class SettingService extends AbstractService
{
    public $arrFillable = [];
    public function __construct()
    {

    }



}