<?php

namespace App\Utilities;

class ContaboObjectStorage
{
    private $arrConfig = [
        'version' => 'latest',
        'region' => 'US-central',
        'endpoint' => 'https://usc1.contabostorage.com',
        'credentials' =>
            [
                'key' => 'a23697bb31ebd46491b00dbbed3e3210',
                'secret' => '049f08897d26920a16563e8151c82dde',
            ],
        'use_path_style_endpoint' => true
    ];
    private $client = null;

    public function __construct()
    {
        $this->client = new \Aws\S3\S3Client($this->arrConfig);
    }

    private function replateDomain($strUrlOrigin = "")
    {
        return str_replace(["sin1.contabostorage.com"], ["sin-ctb.vstorage.pro"], $strUrlOrigin);
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
            return $this->replateDomain($this->client->getObjectUrl($strBucket, $strKey));
        } else {
            return false;
        }
    }

    public function objectExist($strBucket = 'sim', $strKey = '')
    {
        return $this->client->doesObjectExist($strBucket, $strKey);
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
        $result   = $this->client->listObjects([
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
