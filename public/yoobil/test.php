<?php

use Curl\Curl;

public function test()
{
    $baseUrl = 'https://www.yoobil.com/yoobil';
    
    $privateKeyPath         = 'assets/rsa_private_key.pem';
    $publicKeyPath          = 'assets/rsa_public_key.pem';
    $systemRsaPublicKeyPath = 'assets/rsa/merchant_public_key.pem';
    
    $secretKey = 'j0D03AlJx67VPSr122581v8m75y68o0O8792EG14';
    
    $businessId = 19;
    $merchantId = 215;
    
    $data = [
        'accountBase' => '0773744112',
        'amount'      => 500000,
        'businessId'  => $businessId,
        'clientId'    => 'bina',
        'currency'    => 'VND',
        'expireDate'  => date('Ymd'),
        'idNo'        => random_string('numeric', 10),
        'feeId'       => 25,
        'merchantId'  => $merchantId,
        'orderNo'     => substr(time(), -5) . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
        'phoneNumber' => '0773744112',
        'returnUrl'   => 'https://bill.bina.best/success',
        'timestamp'   => floor(microtime(true) * 1000),
        'userName'    => 'Justtest',
        'version'     => '2.0',
    ];
    
    ksort($data);
    
    $data_query = '';
    
    foreach ($data as $param => $value) {
        $data_query .= $param . '=' . $value . "&";
    }
    
    $data_query = substr($data_query, 0, -1);
    
    $string2          = $data_query . $secretKey;
    $private_key      = file_get_contents(FCPATH . $privateKeyPath);
    $binary_signature = "";
    $algo             = "SHA256";
    
    openssl_sign($string2, $binary_signature, $private_key, $algo);
    
    $signed       = base64_encode($binary_signature);
    $data['sign'] = $signed;
    
    echo "<textarea>$string2</textarea>";
    echo '<pre>';
    print_r($data);
    
    $curl = new Curl();
    $curl->setHeader('Content-Type', 'application/json');
    $curl->post($baseUrl . '/trade/vn/virtual/create', json_encode($data));
    
    if ($curl->error) {
        echo 'Error: ' . $curl->errorMessage . "\n";
    }else {
        print_r($curl->response);
    }
}

