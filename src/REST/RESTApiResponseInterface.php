<?php

namespace Gridonic\EasyPay\REST;

/**
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
    public function setHttpStatusCode(int $httpStatusCode);

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
    public function setIsSuccess(bool $success);

    /**
     * @return string
     */
    public function getOperation();

    /**
     * @param string $operation
     * @return $this
     */
    public function setOperation(string $operation);

    /**
     * @return string
     */
    public function getAmount();

    /**
     * @param string $amount
     * @return $this
     */
    public function setAmount(string $amount);

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @param string $id
     * @return $this
     */
    public function setOrderId(string $id);

    /**
     * @return bool
     */
    public function isRoaming();

    /**
     * @param bool $isRoaming
     * @return $this
     */
    public function setIsRoaming(bool $isRoaming);

    /**
     * @return bool
     */
    public function isAdultContent();

    /**
     * @param bool $adultContent
     * @return $this
     */
    public function setIsAdultContent(bool $adultContent);
}
