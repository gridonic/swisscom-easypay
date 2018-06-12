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
     *
     * @return string
     */
    public function getCheckoutPageUrl(CheckoutPageItem $item)
    {
        $data = array_merge(
            $item->toArray(),
            ['merchantId' => $this->environment->getMerchantId()]
        );
        $paymentData = json_encode($data);

        $signature = $this->signatureService->sign($paymentData, $this->environment->getSecret());

        $params = http_build_query([
            'checkoutRequestItem' => base64_encode($paymentData),
            'signature' => base64_encode($signature),
        ]);

        return sprintf('%s://%s/charging-engine-checkout/authorize.jsf?%s', $this->getProtocol(), $this->environment->getHost(), $params);
    }

    /**
     * @return string
     */
    protected function getProtocol()
    {
        return $this->protocolDetector->detect($_SERVER['REMOTE_ADDR'] ?? '');
    }

}
