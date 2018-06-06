<?php

namespace Gridonic\EasyPay\REST;

/**
 * Contract for all responses from the REST Api.
 *
 * @package Gridonic\EasyPay\REST
 */
interface RESTApiResponseInterface
{
    /**
     * @return bool
     */
    public function isSuccess();

    /**
     * @return int
     */
    public function getHttpStatusCode();

    /**
     * @param int $httpStatusCode
     * @return $this
     */
    public function setHttpStatusCode($httpStatusCode);

    /**
     * @return array
     */
    public function getErrorMessages();

    /**
     * @param array $errorMessages
     * @return $this
     */
    public function setErrorMessages(array $errorMessages);

    /**
     * @param bool $success
     * @return $this
     */
    public function setIsSuccess($success);

    /**
     * @return string
     */
    public function getOperation();

    /**
     * @param string $operation
     * @return $this
     */
    public function setOperation($operation);

    /**
     * @return string
     */
    public function getAmount();

    /**
     * @param string $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @param string $id
     * @return $this
     */
    public function setOrderId($id);

    /**
     * @return bool
     */
    public function isRoaming();

    /**
     * @param bool $isRoaming
     * @return $this
     */
    public function setIsRoaming($isRoaming);

    /**
     * @return bool
     */
    public function isAdultContent();

    /**
     * @param bool $adultContent
     * @return $this
     */
    public function setIsAdultContent($adultContent);

    /**
     * @return string
     */
    public function getCreatedOn();

    /**
     * @param string $createdOn
     * @return $this
     */
    public function setCreatedOn($createdOn);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getExtTransactionId();

    /**
     * @param string $extTransactionId
     * @return $this
     */
    public function setExtTransactionId($extTransactionId);

    /**
     * Returns an array with all properties except the ones being null.
     *
     * @return array
     */
    public function toArray();
}
