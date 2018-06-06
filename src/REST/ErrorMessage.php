<?php

namespace Gridonic\EasyPay\REST;

/**
 * @package Gridonic\EasyPay\REST
 */
class ErrorMessage
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $requestId;

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return ErrorMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return ErrorMessage
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return ErrorMessage
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param string $requestId
     *
     * @return ErrorMessage
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;

        return $this;
    }
}
