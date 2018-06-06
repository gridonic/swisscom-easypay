<?php

namespace Gridonic\EasyPay\CheckoutPage;

/**
 * @package Gridonic\EasyPay\CheckoutPage
 */
class CheckoutPageResponse
{
    /**
     * @var array
     */
    private $params = [];

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Create an instance from the $_GET parameters.
     *
     * @return CheckoutPageResponse
     */
    public static function createFromGet()
    {
        return new self($_GET);
    }

    /**
     * @return string|null
     */
    public function getPaymentId()
    {
        return $this->params['paymentId'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getAuthSubscriptionId()
    {
        return $this->params['authSubscriptionId'] ?? null;
    }

    /**
     * @return bool
     */
    public function isRecurrent()
    {
        return ($this->getAuthSubscriptionId() !== null);
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return !$this->isError();
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return (isset($this->params['purchase']) && $this->params['purchase'] === 'error');
    }

    /**
     * @return string|null
     */
    public function getErrorCode()
    {
        if ($this->isError()) {
            return $this->params['error'] ?? null;
        }

        return null;
    }
}
