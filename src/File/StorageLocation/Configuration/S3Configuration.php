<?php

namespace Concrete5\DropBox\File\StorageLocation\Configuration;

use Aws\S3\S3Client;
use Concrete\Core\File\StorageLocation\Configuration\DeferredConfigurationInterface;
use Concrete\Core\Http\Request;
use Concrete5\DropBox\Form\Service\Validation;
use Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface;
use Concrete\Core\File\StorageLocation\Configuration\Configuration;
use League\Url\Url;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

class S3Configuration extends Configuration implements ConfigurationInterface, DeferredConfigurationInterface
{

    public $bucket;
    public $key;
    public $secret;
    public $expire;
    public $region;
    public $baseUrl;
    public $useIAM;

    protected $formValidation;
    
    public function __construct(
        Validation $formValidation
    )
    {
        $this->formValidation = $formValidation;
    }

    public function hasPublicURL()
    {
        return true;
    }

    public function hasRelativePath()
    {
        return false;
    }

    public function loadFromRequest(Request $request)
    {
        $data = $request->request->get('fslType');
        
        $this->useIAM = $data['useIAM'];
        $this->bucket = $data['bucket'];
        $this->key = $data['key'];
        $this->secret = $data['secret'];
        $this->region = $data['region'];
        $this->baseUrl = $data['baseUrl'];
    }

    public function validateRequest(Request $request)
    {
        $data = $request->request->get('fslType');

        $this->formValidation->setData($data);
        
        $this->formValidation->addRequired("bucket", t("You must set a S3 Bucket."));

        if (!$data['useIAM']) {
            $this->formValidation->addRequired("key", t("You must set a S3 Key."));
            $this->formValidation->addRequired("secret", t("You must set a S3 Secret."));
            $this->formValidation->addRequired("region", t("You must set a region."));
        }

        $this->formValidation->test();
        
        return $this->formValidation->getError();
    }

    public function getAdapter()
    {
        return new AwsS3Adapter($this->getClient(), $this->bucket);
    }

    protected function getClient()
    {
        if ($this->useIAM) {
            return new S3Client([]);
        } else {
            return new S3Client([
                'credentials' => [
                    'key' => $this->key,
                    'secret' => $this->secret
                ],
                'region' => $this->region,
                'version' => 'latest'
            ]);
        }
    }

    public function getPublicURLToFile($file)
    {
        $file = trim($file, '/');
        
        $url = $this->getClient()->getObjectUrl($this->bucket, $file);
        
        if (strlen($this->baseUrl)) {
            $url = Url::createFromUrl($this->baseUrl);
            $url->setPath($file);
        }
        
        return (string)$url;
    }

    public function getRelativePathToFile($file)
    {
        return $file;
    }
}