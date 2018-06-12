<?php

namespace Gridonic\EasyPay\Tests;

use Gridonic\EasyPay\CheckoutPage\CheckoutPageItem;
use Gridonic\EasyPay\CheckoutPage\CheckoutPageService;
use Gridonic\EasyPay\CheckoutPage\ProtocolDetectorService;
use Gridonic\EasyPay\Environment\Environment;
use Gridonic\EasyPay\Signature\SignatureService;
use PHPUnit\Framework\TestCase;

/**
 * @package Gridonic\EasyPay\Tests
 */
class CheckoutPageServiceTest extends TestCase
{
    public function testGetCheckoutPageUrl_UrlDifferentDependingOnEnvironment_UrlContainsCorrectHost()
    {
        $environment = new Environment(Environment::ENV_PROD, 'gridonic-123', 's3cr3t');
        $checkoutPageService = CheckoutPageService::create($environment);

        $url = $checkoutPageService->getCheckoutPageUrl(new CheckoutPageItem());
        $this->assertNotFalse(strpos($url, 'easypay.swisscom.ch'));
        $this->assertFalse(strpos($url, 'easypay-staging.swisscom.ch'));

        $environment = new Environment(Environment::ENV_STAGING, 'gridonic-123', 's3cr3t');
        $checkoutPageService = CheckoutPageService::create($environment);

        $url = $checkoutPageService->getCheckoutPageUrl(new CheckoutPageItem());
        $this->assertFalse(strpos($url, 'easypay.swisscom.ch'));
        $this->assertNotFalse(strpos($url, 'easypay-staging.swisscom.ch'));
    }

    public function testGetCheckoutPageUrl_BasedOnEnvironmentAndCheckoutItem_UrlCorrect()
    {
        $checkoutPageService = $this->getCheckoutPageService(function($environmentMock, $signatureServiceMock, $protocolDetectorServiceMock) {
            $environmentMock
                ->expects($this->once())
                ->method('getMerchantId')
                ->willReturn('merchantId-123');

            $environmentMock
                ->expects($this->once())
                ->method('getSecret')
                ->willReturn('s3cr3t');

            $environmentMock
                ->expects($this->once())
                ->method('getHost')
                ->willReturn('easypay.swisscom.ch');

            $signatureServiceMock
                ->expects($this->once())
                ->method('sign')
                ->willReturn('signatureString');

            $protocolDetectorServiceMock
                ->expects($this->once())
                ->method('detect')
                ->willReturn('http');
        });

        $checkoutItem = new CheckoutPageItem();
        $checkoutItem
            ->setTitle('Checkout Item')
            ->setPaymentInfo('Some payment information')
            ->setAmount('99.90')
            ->setDescription('A description');

        $paymentData = json_encode(array_merge($checkoutItem->toArray(), ['merchantId' => 'merchantId-123']));

        $urlParams = http_build_query([
            'checkoutRequestItem' => base64_encode($paymentData),
            'signature' => base64_encode('signatureString'),
        ]);

        $expectedUrl = sprintf('http://easypay.swisscom.ch/charging-engine-checkout/authorize.jsf?%s', $urlParams);
        $this->assertEquals($expectedUrl, $checkoutPageService->getCheckoutPageUrl($checkoutItem));
    }


    /**
     * @param \closure|null $dependencyManipulator
     * @return CheckoutPageService
     */
    protected function getCheckoutPageService(\closure $dependencyManipulator = null)
    {
        $environmentMock = $this->createMock(Environment::class);
        $signatureServiceMock = $this->createMock(SignatureService::class);
        $protocolDetectorServiceMock = $this->createMock(ProtocolDetectorService::class);

        if ($dependencyManipulator !== null) {
            $dependencyManipulator($environmentMock, $signatureServiceMock, $protocolDetectorServiceMock);
        }

        return new CheckoutPageService($environmentMock, $signatureServiceMock, $protocolDetectorServiceMock);
    }
}
