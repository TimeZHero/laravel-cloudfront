<?php

namespace Meema\CloudFront;

use Aws\CloudFront\CloudFrontClient;
use Aws\Credentials\Credentials;
use Exception;
use Illuminate\Support\Manager;

class CloudFrontManager extends Manager
{
    /**
     * Get a driver instance.
     *
     * @param string|null $name
     * @return mixed
     */
    public function engine($name = null)
    {
        return $this->driver($name);
    }

    /**
     * Create an Amazon CloudFront instance.
     *
     * @return \Meema\CloudFront\CloudFront
     * @throws \Exception
     */
    public function createCloudFrontDriver(): CloudFront
    {
        $this->ensureAwsSdkIsInstalled();

        $client = $this->setCloudFrontClient();

        return new CloudFront($client);
    }

    /**
     * Sets the polly client.
     */
    protected function setCloudFrontClient(): CloudFrontClient
    {
        $args = [
            'version' => config('cloudfront.version'),
            'region' => config('cloudfront.region'),
        ];

        if (config('cloudfront.credentials')
            && config('cloudfront.credentials.key')
            && config('cloudfront.credentials.secret')
        ) {
            $credentials = new Credentials(config('cloudfront.credentials.key'), config('cloudfront.credentials.secret'));

            $args['credentials'] = $credentials;
        }

        return new CloudFrontClient($args);
    }

    /**
     * Ensure the AWS SDK is installed.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function ensureAwsSdkIsInstalled()
    {
        if (! class_exists(CloudFrontClient::class)) {
            throw new Exception('Please install the AWS SDK PHP using `composer require aws/aws-sdk-php`.');
        }
    }

    /**
     * Get the default Text to Speech driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return 'cloudfront';
    }
}
