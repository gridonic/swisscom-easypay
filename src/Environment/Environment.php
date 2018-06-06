<?php

namespace Gridonic\EasyPay\Environment;

/**
 * @package Gridonic\EasyPay\Environment
 */
class Environment
{
    const ENV_PROD = 'prod';
    const ENV_STAGING = 'staging';
    const HOST_PROD = 'easypay.swisscom.ch';
    const HOST_STAGING = 'easypay-staging.swisscom.ch';

    /**
     * @var string
     */
    private $environmentType = self::ENV_PROD;

    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var string
     */
    private $secret;

    /**
     * @param string $environmentType
     * @param string $merchantId
     * @param string $secret
     */
    public function __construct(string $environmentType, string $merchantId, string $secret)
    {
        if (!in_array($environmentType, [self::ENV_PROD, self::ENV_STAGING])) {
            throw new \InvalidArgumentException('Environment type must be either "prod" or "staging"');
        }

        $this->environmentType = $environmentType;
        $this->merchantId = $merchantId;
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->environmentType == self::ENV_PROD ? self::HOST_PROD : self::HOST_STAGING;
    }

    /**
     * @return string
     */
    public function getEnvironmentType()
    {
        return $this->environmentType;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }
}
