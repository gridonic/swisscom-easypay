<?php

namespace Gridonic\EasyPay\REST;

/**
 * Base class for all responses from the REST Api.
 *
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
    private $status;

    /**
     * @var string
     */
    private $extTransactionId;

    /**
     * @var string
     */
    private $createdOn;

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
    public function setIsSuccess($success)
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
    public function setHttpStatusCode($httpStatusCode)
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
    public function setOperation($operation)
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
    public function setAmount($amount)
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
    public function setIsRoaming($isRoaming)
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
    public function setIsAdultContent($isAdultContent)
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
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @inheritdoc
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtTransactionId()
    {
        return $this->extTransactionId;
    }

    /**
     * @inheritdoc
     */
    public function setExtTransactionId($extTransactionId)
    {
        $this->extTransactionId = $extTransactionId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        $array = [];
        foreach ($this as $key => $value) {
            if ($value === null) {
                continue;
            }
            $array[$key] = $value;
        }

        return $array;
    }
}
