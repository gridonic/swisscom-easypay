<?php

namespace Gridonic\EasyPay\REST;

/**
 * @package Gridonic\EasyPay\REST
 */
abstract class RESTApiResponse implements RESTApiResponseInterface
{
    /**
     * @var int
     */
    private $httpStatusCode;

    /**
     * @var bool
     */
    private $isSuccess;

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var string
     */
    private $operation;

    /**
     * @var bool
     */
    private $isRoaming;

    /**
     * @var bool
     */
    private $isAdultContent;

    /**
     * @var array
     */
    private $errorMessages = [];

    /**
     * @inheritdoc
     */
    public function isSuccess()
    {
        return $this->isSuccess;
    }

    /**
     * @inheritdoc
     */
    public function setIsSuccess(bool $success)
    {
        $this->isSuccess = $success;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * @inheritdoc
     */
    public function setHttpStatusCode(int $httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * @inheritdoc
     */
    public function setErrorMessages(array $errorMessages)
    {
        $this->errorMessages = $errorMessages;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @inheritdoc
     */
    public function setOperation(string $operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @inheritdoc
     */
    public function setAmount(string $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isRoaming()
    {
        return $this->isRoaming;
    }

    /**
     * @inheritdoc
     */
    public function setIsRoaming(bool $isRoaming)
    {
        $this->isRoaming = $isRoaming;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isAdultContent()
    {
        return $this->isAdultContent;
    }

    /**
     * @inheritdoc
     */
    public function setIsAdultContent(bool $isAdultContent)
    {
        $this->isAdultContent = $isAdultContent;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @inheritdoc
     */
    public function setOrderId(string $orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }
}
