<?php

namespace Gridonic\EasyPay\REST;

/**
 * @package Gridonic\EasyPay\REST
 */
class DirectPaymentResponse extends RESTApiResponse
{
    /**
     * @var string
     */
    private $paymentInfo;

    /**
     * @var string
     */
    private $userAgentOrigin;

    /**
     * @var string
     */
    private $userSourceIp;

    /**
     * @return string
     */
    public function getPaymentInfo()
    {
        return $this->paymentInfo;
    }

    /**
     * @param string $paymentInfo
     * @return DirectPaymentResponse
     */
    public function setPaymentInfo($paymentInfo)
    {
        $this->paymentInfo = $paymentInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgentOrigin()
    {
        return $this->userAgentOrigin;
    }

    /**
     * @param string $userAgentOrigin
     * @return DirectPaymentResponse
     */
    public function setUserAgentOrigin($userAgentOrigin)
    {
        $this->userAgentOrigin = $userAgentOrigin;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserSourceIp()
    {
        return $this->userSourceIp;
    }

    /**
     * @param string $userSourceIp
     * @return DirectPaymentResponse
     */
    public function setUserSourceIp($userSourceIp)
    {
        $this->userSourceIp = $userSourceIp;

        return $this;
    }
}
