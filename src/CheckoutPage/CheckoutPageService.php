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
    private $signature;

    /**
     * @var ProtocolDetectorService
     */
    private $protocolDetector;

    /**
     * @param Environment $environment
     * @param SignatureService $signature
     * @param ProtocolDetectorService $protocolDetector
     */
    public function __construct(Environment $environment, SignatureService $signature, ProtocolDetectorService $protocolDetector)
    {
        $this->environment = $environment;
        $this->signature = $signature;
        $this->protocolDetector = $protocolDetector;
    }

    /**
     * @param Environment $environment
     * @return CheckoutPageService
     */
    public static function create(Environment $environment)
    {
        return new self($environment, new SignatureService(), new ProtocolDetectorService());
    }

    /**
     * @param CheckoutPageItem $item
     *
     * @return string
     */
    public function getUrl(CheckoutPageItem $item)
    {
        return $this->buildUrl($item);
    }

    /**
     * Build the checkout page URL for the given CheckoutPageItem.
     *
     * @param CheckoutPageItem $item
     *
     * @return string
     */
    protected function buildUrl(CheckoutPageItem $item)
    {
        $data = array_merge(
            $item->toArray(),
            ['merchantId' => $this->environment->getMerchantId()]
        );
        $paymentData = json_encode($data);

        $signature = $this->signature->sign($paymentData, $this->environment->getSecret());

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
