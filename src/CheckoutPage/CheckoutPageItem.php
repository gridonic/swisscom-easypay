<?php

namespace Gridonic\EasyPay\CheckoutPage;

/**
 * Represents the item to purchase on the Easypay checkout page.
 *
 * @package Gridonic\EasyPay\CheckoutPage
 */
class CheckoutPageItem
{
    const DURATION_WEEK = 'WEEK';
    const DURATION_MONTH = 'MONTH';

    /**
     * @var string
     */
    private $paymentInfo;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

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
    private $amount;

    /**
     * @var string
     */
    private $promotionAmount;

    /**
     * @var bool
     */
    private $isAdultContent;

    /**
     * @var bool
     */
    private $isRoaming;

    /**
     * @var string
     */
    private $successUrl;

    /**
     * @var string
     */
    private $cancelUrl;

    /**
     * @var string
     */
    private $errorUrl;

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
     * @var string
     */
    private $imageUrl;

    /**
     * @var string
     */
    private $userLanguage;

    /**
     * @var string
     */
    private $msisdn;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $storeSource;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    /**
     * @return array
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

    /**
     * @return string
     */
    public function getPaymentInfo()
    {
        return $this->paymentInfo;
    }

    /**
     * @param string $paymentInfo
     * @return CheckoutPageItem
     */
    public function setPaymentInfo(string $paymentInfo)
    {
        $this->paymentInfo = $paymentInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return CheckoutPageItem
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return CheckoutPageItem
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

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
     * @return CheckoutPageItem
     */
    public function setDuration(int $duration)
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
     * @return CheckoutPageItem
     */
    public function setDurationUnit(string $durationUnit)
    {
        if (!in_array($durationUnit, [self::DURATION_WEEK, self::DURATION_MONTH])) {
            throw new \InvalidArgumentException('Duration unit must be "WEEK" or "MONTH"');
        }

        $this->durationUnit = $durationUnit;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return CheckoutPageItem
     */
    public function setAmount(string $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPromotionAmount()
    {
        return $this->promotionAmount;
    }

    /**
     * @param mixed $promotionAmount
     * @return CheckoutPageItem
     */
    public function setPromotionAmount($promotionAmount)
    {
        $this->promotionAmount = $promotionAmount;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAdultContent()
    {
        return $this->isAdultContent;
    }

    /**
     * @param bool $isAdultContent
     * @return CheckoutPageItem
     */
    public function setIsAdultContent(bool $isAdultContent)
    {
        $this->isAdultContent = $isAdultContent;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRoaming()
    {
        return $this->isRoaming;
    }

    /**
     * @param bool $isRoaming
     * @return CheckoutPageItem
     */
    public function setIsRoaming(bool $isRoaming)
    {
        $this->isRoaming = $isRoaming;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->successUrl;
    }

    /**
     * @param string $successUrl
     * @return CheckoutPageItem
     */
    public function setSuccessUrl(string $successUrl)
    {
        $this->successUrl = $successUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * @param string $cancelUrl
     * @return CheckoutPageItem
     */
    public function setCancelUrl(string $cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorUrl()
    {
        return $this->errorUrl;
    }

    /**
     * @param string $errorUrl
     * @return CheckoutPageItem
     */
    public function setErrorUrl(string $errorUrl)
    {
        $this->errorUrl = $errorUrl;

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
     * @return CheckoutPageItem
     */
    public function setCpServiceId(string $cpServiceId)
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
     * @return CheckoutPageItem
     */
    public function setCpUserId(string $cpUserId)
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
     * @return CheckoutPageItem
     */
    public function setCpSubscriptionId(string $cpSubscriptionId)
    {
        $this->cpSubscriptionId = $cpSubscriptionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     * @return CheckoutPageItem
     */
    public function setImageUrl(string $imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserLanguage()
    {
        return $this->userLanguage;
    }

    /**
     * @param string $userLanguage
     * @return CheckoutPageItem
     */
    public function setUserLanguage(string $userLanguage)
    {
        $this->userLanguage = $userLanguage;

        return $this;
    }

    /**
     * @return string
     */
    public function getMsisdn()
    {
        return $this->msisdn;
    }

    /**
     * @param string $msisdn
     * @return CheckoutPageItem
     */
    public function setMsisdn(string $msisdn)
    {
        $this->msisdn = $msisdn;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     * @return CheckoutPageItem
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStoreSource()
    {
        return $this->storeSource;
    }

    /**
     * @param mixed $storeSource
     * @return CheckoutPageItem
     */
    public function setStoreSource($storeSource)
    {
        $this->storeSource = $storeSource;

        return $this;
    }

    /**
     * Set properties from the given data via setter.
     *
     * @param array $data
     */
    protected function setData(array $data)
    {
        if (!count($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            $setter = sprintf('set%s', ucfirst($key));
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }
}
