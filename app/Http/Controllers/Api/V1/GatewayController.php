<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\GatewayService;

class GatewayController extends BaseController
{

    private $gatewayService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(GatewayService $gatewayService)
    {
        $this->gatewayService = $gatewayService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]['name'])) {
            $arrParams["query_like"]["name"] = $arrParams["query"]['name'];
            unset($arrParams["query"]["name"]);
        }

        return response()->json($this->gatewayService->getList($arrParams));
    }

    public function select2GetList()
    { 
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        if (!empty($arrParams["query"]["name"])) {
            $arrParams["query_or_like"] = [
                "gateways.name" => $arrParams["query"]["name"],
            ];
            unset($arrParams["query"]["name"]);
        }
        return response()->json($this->gatewayService->responseSelect2($this->gatewayService->getList($arrParams)));
    }
    public function add()
    {
        $arrParams = request()->all();
        return response()->json($this->gatewayService->add($arrParams));
    }


    public function update()
    {
        $arrParams = request()->all();
        return response()->json($this->gatewayService->update($arrParams));
    }

    public function generateKey()
    {
        $config = array(
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privateKey);

        $publicKeyDetails = openssl_pkey_get_details($res);
        $publicKey = $publicKeyDetails["key"];

        $dn = [
            "countryName" => "VN",
            "stateOrProvinceName" => "Ha Noi",
            "localityName" => "Ha Noi",
            "organizationName" => "Merchant",
            "organizationalUnitName" => "Payment",
            "commonName" => "merchant.gpay.vn",
            "emailAddress" => "merchant@example.com"
        ];
        $csr = openssl_csr_new($dn, $res, ['digest_alg' => 'sha256']);
        $sscert = openssl_csr_sign($csr, null, $res, 3650, ['digest_alg' => 'sha256']);
        openssl_x509_export($sscert, $certificate);

        return response()->json([
            'error_code' => 0,
            'message' => 'Success',
            'data' => [
                'private_key' => $privateKey,
                'public_key' => $publicKey,
                'certificate' => $certificate
            ]
        ]);
    }

    public function getDetail()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);
        return response()->json($this->gatewayService->getDetail($arrParams));
    }
}