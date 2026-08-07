<?php

namespace App\Http\Controllers\Frontend\V1;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use tttran\viet_qr_generator\Generator;


class QrcodeController extends BaseController
{


        /**
         * Create a new controller instance.
         *
         * @return void
         */
        public function __construct()
        {


        }

        public function index($bank = "", $code = "")
        {
                $arrParams = request()->all();

                $strInfoRemark = $arrParams["remark"] ?? "";
                $intAmount     = $arrParams["amount"] ?? "";
                $generator     = (new Generator())->create()
                        ->bankId($bank)
                        ->accountNo($code)// Account number
                        ->amount($intAmount)// Money
                        ->info($strInfoRemark) // Ref
                        ->generate();

                $arrGenerator = json_decode($generator, true);
                if ($arrGenerator["code"] != 200) {
                        return "";
                }

                $builder = new Builder(
                        writer: new PngWriter(),
                        writerOptions: [],
                        validateResult: false,
                        data: $arrGenerator["data"] ?? "",
                        encoding: new Encoding('UTF-8'),
                        errorCorrectionLevel: ErrorCorrectionLevel::High,
                        size: $arrParams["size"] ?? 200,
                        margin: 3,
                        roundBlockSizeMode: RoundBlockSizeMode::Margin,
                        logoResizeToWidth: 50,
                        logoPunchoutBackground: true,
                        labelText: $arrParams["label"] ?? "",
                        labelFont: new OpenSans(20),
                        labelAlignment: LabelAlignment::Center
                );
                $result  = $builder->build();


                return \Illuminate\Support\Facades\Response::make($result->getString(), 200, [
                        'Content-Type' => 'image/png',
                        'Content-Disposition' => 'inline; filename="image.png"',
                ]);

        }

}