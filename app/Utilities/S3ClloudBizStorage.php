<?php

namespace App\Utilities;

class S3ClloudBizStorage
{
    private $arrConfig = [
        'version' => 'latest',
        'region' => 'vn-hcm-1',
        'endpoint' => 'http://s3.vncloud.biz',
        'credentials' =>
            [
                'key' => 'Nh99ONOVxjb4U6yeOYRa',
                'secret' => 'Roba1rKN3uqJp6f2eqMydkbHjPl24kE4sXOx0GOF',
            ],
        'use_path_style_endpoint' => true,
        'signature_version' => 'v4',
        'retries' => [
            'mode' => 'legacy',
            'max_attempts' => 3,
        ],                      // tắt retry (tránh phải rewind stream)
        'http' => [
            'stream' => false,                 // buffer response vào memory => seekable
            'decode_content' => false,         // tránh Guzzle tự decode gzip/chunked
            'connect_timeout' => 5,
            'timeout' => 30,
        ],
    ];
    private $client = null;

    public function __construct()
    {
        $this->client = new \Aws\S3\S3Client($this->arrConfig);
    }

    private function replateDomain($strUrlOrigin = "")
    {
        return $strUrlOrigin;
    }

    public function putPublic($strBucket = 'sim', $strPath = '', $strKey = '')
    {
        $this->client->putObject([
            'ACL' => 'public-read',
            'Bucket' => $strBucket,
            'Key' => $strKey,
            'SourceFile' => $strPath
        ]);
        if ($this->objectExist($strBucket, $strKey)) {
            return $this->replateDomain($this->client->getObjectUrl($strBucket, $strKey));
        } else {
            return false;
        }
    }

    public function putPrivate($strBucket = 'sim', $strPath = '', $strKey = '')
    {
        $this->client->putObject([
            'Bucket' => $strBucket,
            'Key' => $strKey,
            'SourceFile' => $strPath
        ]);
        if ($this->objectExist($strBucket, $strKey)) {
            try {
                return $this->replateDomain($this->client->getObjectUrl($strBucket, $strKey));
            } catch (\Exception $e) {
                dump($e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    public function objectExist($strBucket = 'sim', $strKey = '')
    {
        try {
            return $this->client->doesObjectExist($strBucket, $strKey);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            if ($e->getStatusCode() === 404) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return true;
        }
    }

    public function copyObject($strBucketSource = 'sim', $strKeySource = '', $strBucket = 'sim', $strKey = '')
    {
        $this->client->copyObject([
            'ACL' => 'public-read',
            'Bucket' => $strBucket,
            'CopySource' => "$strBucketSource/$strKeySource",
            'Key' => $strKey,
        ]);

        if ($this->objectExist($strBucket, $strKey)) {
            return $this->replateDomain($this->client->getObjectUrl($strBucket, $strKey));
        } else {
            return false;
        }
    }



    public function putObjectDataOld($strUrl = '', $strUrlName = '')
    {

        $this->client->putObject([
            'ACL' => 'public-read',
            'Bucket' => env('BUCKET_STORAGE') ?? 'dktt-vip',
            'Key' => $strUrlName,
            'SourceFile' => $strUrl
        ]);

        return $this->replateDomain($this->client->getObjectUrl(env('BUCKET_STORAGE') ?? 'dktt-vip', $strUrlName));
    }

    public function putObject($strPath = '', $strKey = '')
    {

        $this->client->putObject([
            'ACL' => 'public-read',
            'Bucket' => env('BUCKET_STORAGE') ?? 'dktt-vip',
            'Key' => $strKey,
            'SourceFile' => $strPath
        ]);
        if ($this->objectExist(env('BUCKET_STORAGE') ?? 'dktt-vip', $strKey)) {
            return $this->replateDomain($this->client->getObjectUrl(env('BUCKET_STORAGE') ?? 'dktt-vip', $strKey));
        } else {
            return false;
        }
    }

    public function putObjectPrivate($strPath = '', $strKey = '')
    {

        $this->client->putObject([
            'Bucket' => env('BUCKET_STORAGE') ?? 'dktt-vip',
            'Key' => $strKey,
            'SourceFile' => $strPath
        ]);
        if ($this->objectExist(env('BUCKET_STORAGE') ?? 'dktt-vip', $strKey)) {
            return $this->replateDomain($this->client->getObjectUrl(env('BUCKET_STORAGE') ?? 'dktt-vip', $strKey));
        } else {
            return false;
        }
    }

    public function putObjectPublic($strPath = '', $strKey = '')
    {

        $this->client->putObject([
            'ACL' => 'public-read',
            'Bucket' => env('BUCKET_STORAGE') ?? 'dktt-vip',
            'Key' => $strKey,
            'SourceFile' => $strPath
        ]);
        if ($this->objectExist(env('BUCKET_STORAGE') ?? 'dktt-vip', $strKey)) {
            return $this->replateDomain($this->client->getObjectUrl(env('BUCKET_STORAGE') ?? 'dktt-vip', $strKey));
        } else {
            return false;
        }
    }

    public function remove($Bucket = "truyen-anime", $prefix = "")
    {
        $intCount = 0;
        $result = $this->client->listObjects([
            'Bucket' => $Bucket,
            'Prefix' => $prefix
        ]);

        foreach ($result['Contents'] as $object) {
            $intCount += 1;
            $this->client->deleteObject([
                'Bucket' => $Bucket,
                'Key' => $object['Key']
            ]);
            // dump($object['Key']);
        }
        return $intCount;
    }
}
