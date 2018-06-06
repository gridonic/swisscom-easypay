<?php

namespace Gridonic\EasyPay\REST;

/**
 * @package Gridonic\EasyPay\REST
 */
class AuthSubscriptionResponse extends RESTApiResponse
{
    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var string
     */
    private $msidn;

    /**
     * @var int
     */
    private $duration;

    /**
     * @var string
     */
    private $durationUnit;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $nextPayment;

    /**
     * @var string
     */
    private $startRefund;

    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var string
     */
    private $cpServiceId;

    /**
     * @var string
     */
    private $cpUserId;

    /**
     * @var string
     */
    private $cpSubscriptionId;

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return AuthSubscriptionResponse
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return string
     */
    public function getMsidn()
    {
        return $this->msidn;
    }

    /**
     * @param string $msidn
     * @return AuthSubscriptionResponse
     */
    public function setMsidn($msidn)
    {
        $this->msidn = $msidn;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     * @return AuthSubscriptionResponse
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return string
     */
    public function getDurationUnit()
    {
        return $this->durationUnit;
    }

    /**
     * @param string $durationUnit
     * @return AuthSubscriptionResponse
     */
    public function setDurationUnit($durationUnit)
    {
        $this->durationUnit = $durationUnit;

        return $this;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     * @return AuthSubscriptionResponse
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getNextPayment()
    {
        return $this->nextPayment;
    }

    /**
     * @param string $nextPayment
     * @return AuthSubscriptionResponse
     */
    public function setNextPayment($nextPayment)
    {
        $this->nextPayment = $nextPayment;

        return $this;
    }

    /**
     * @return string
     */
    public function getStartRefund()
    {
        return $this->startRefund;
    }

    /**
     * @param string $startRefund
     * @return AuthSubscriptionResponse
     */
    public function setStartRefund($startRefund)
    {
        $this->startRefund = $startRefund;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     * @return AuthSubscriptionResponse
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpServiceId()
    {
        return $this->cpServiceId;
    }

    /**
     * @param string $cpServiceId
     * @return AuthSubscriptionResponse
     */
    public function setCpServiceId($cpServiceId)
    {
        $this->cpServiceId = $cpServiceId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpUserId()
    {
        return $this->cpUserId;
    }

    /**
     * @param string $cpUserId
     * @return AuthSubscriptionResponse
     */
    public function setCpUserId($cpUserId)
    {
        $this->cpUserId = $cpUserId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpSubscriptionId()
    {
        return $this->cpSubscriptionId;
    }

    /**
     * @param string $cpSubscriptionId
     * @return AuthSubscriptionResponse
     */
    public function setCpSubscriptionId($cpSubscriptionId)
    {
        $this->cpSubscriptionId = $cpSubscriptionId;

        return $this;
    }
}
