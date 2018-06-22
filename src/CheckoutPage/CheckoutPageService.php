<?php

namespace Gridonic\EasyPay\CheckoutPage;

use Gridonic\EasyPay\Environment\Environment;
use Gridonic\EasyPay\Signature\SignatureService;

/**
 * @package Gridonic\EasyPay\CheckoutPage
 */
class CheckoutPageService
{
    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var SignatureService
     */
    private $signatureService;

    /**
     * @var ProtocolDetectorService
     */
    private $protocolDetector;

    /**
     * Required parameters for the checkout page request.
     *
     * @var array
     */
    public static $requiredParameters = [
        'title',
        'paymentInfo',
        'description',
        'amount',
        'successUrl',
        'cancelUrl',
        'errorUrl',
        'merchantId',
    ];

    /**
     * @param Environment $environment
     * @param SignatureService $signatureService
     * @param ProtocolDetectorService $protocolDetector
     */
    public function __construct(Environment $environment, SignatureService $signatureService, ProtocolDetectorService $protocolDetector)
    {
        $this->environment = $environment;
        $this->signatureService = $signatureService;
        $this->protocolDetector = $protocolDetector;
    }

    /**
     * Create an instance from the given environment.
     *
     * @param Environment $environment
     *
     * @return CheckoutPageService
     */
    public static function create(Environment $environment)
    {
        return new self($environment, new SignatureService(), new ProtocolDetectorService());
    }

    /**
     * Build the url to the checkout page for the given item.
     *
     * @param CheckoutPageItem $item
     * @throws \DomainException
     *
     * @return string
     */
    public function getCheckoutPageUrl(CheckoutPageItem $item)
    {
        $requestData = $this->buildRequestData($item);
        $this->checkMandatoryParameters($requestData);
        $requestData = json_encode($requestData);

        $signature = $this->signatureService->sign($requestData, $this->environment->getSecret());

        $params = http_build_query([
            'checkoutRequestItem' => base64_encode($requestData),
            'signature' => base64_encode($signature),
        ]);

        return sprintf('%s://%s/charging-engine-checkout/authorize.jsf?%s', $this->getProtocol(), $this->environment->getHost(), $params);
    }

    /**
     * @param array $requestData
     * @throws \DomainException
     */
    protected function checkMandatoryParameters(array $requestData)
    {
        $missingParams = array_filter(self::$requiredParameters, function ($requiredParam) use ($requestData) {
            return (!isset($requestData[$requiredParam]));
        });

        if (count($missingParams)) {
            throw new \DomainException(sprintf('The following mandatory parameters are missing for the checkout page request: %s', implode(', ', $missingParams)));
        }
    }

    /**
     * Build the data sent with the request (GET).
     *
     * Note: We remove any fields being NULL or empty from the checkout page item.
     *
     * @param CheckoutPageItem $item
     *
     * @return array
     */
    protected function buildRequestData(CheckoutPageItem $item)
    {
        $itemData = array_filter($item->toArray(), function ($data) {
           return ($data !== null && $data !== '');
        });

        return array_merge(
            $itemData,
            ['merchantId' => $this->environment->getMerchantId()]
        );
    }

    /**
     * @return string
     */
    protected function getProtocol()
    {
        return $this->protocolDetector->detect($_SERVER['REMOTE_ADDR'] ?? '');
    }

}
