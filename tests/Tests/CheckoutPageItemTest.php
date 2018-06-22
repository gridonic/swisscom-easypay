<?php

namespace Gridonic\EasyPay\Tests;

use Gridonic\EasyPay\CheckoutPage\CheckoutPageItem;
use PHPUnit\Framework\TestCase;

/**
 * @package Gridonic\EasyPay\Tests
 */
class CheckoutPageItemTest extends TestCase
{
    public function testConstructor_ItemInitializedWithDataSet_PropertiesAreSetCorrectly()
    {
        $data = [
            'title' => 'title',
            'paymentInfo' => 'paymentInfo',
            'amount' => '19.90',
            'description' => 'description',
            'promotionAmount' => null,
            'isAdultContent' => true,
            'isRoaming' => false,
            'msisdn' => '0800 123456789',
            'successUrl' => 'successUrl',
            'cancelUrl' => 'cancelUrl',
        ];

        $checkoutPageItem = new CheckoutPageItem($data);

        $this->assertEquals('title', $checkoutPageItem->getTitle());
        $this->assertEquals('paymentInfo', $checkoutPageItem->getPaymentInfo());
        $this->assertEquals('19.90', $checkoutPageItem->getAmount());
        $this->assertEquals('description', $checkoutPageItem->getDescription());
        $this->assertEquals(null, $checkoutPageItem->getPromotionAmount());
        $this->assertTrue($checkoutPageItem->isAdultContent());
        $this->assertFalse($checkoutPageItem->isRoaming());
        $this->assertEquals('0800 123456789', $checkoutPageItem->getMsisdn());
        $this->assertEquals('successUrl', $checkoutPageItem->getSuccessUrl());
        $this->assertEquals('cancelUrl', $checkoutPageItem->getCancelUrl());
    }

    public function testSetDurationUnit_InvalidDurationUnit_ShouldThrowException()
    {
        $this->expectException(\DomainException::class);

        $checkoutPageItem = new CheckoutPageItem();
        $checkoutPageItem
            ->setDurationUnit('YEAR');

        $checkoutPageItem2 = new CheckoutPageItem(['durationUnit' => 'YEAR']);
    }
}
